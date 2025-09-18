<?php
declare(strict_types=1);
require_once '../backend/config/config.php';

ob_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('X-Content-Type-Options: nosniff');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax'
]);

if (!defined('OPENROUTER_API_KEY')) {
    http_response_code(500);
    exit(json_encode(['error' => 'Server configuration error']));
}

// Define room capacities similar to your Python script
$ROOM_CAPACITIES = [
    'Auditorium' => 200,
    'Classroom 301' => 50,
    'Classroom 302' => 70,
    'Classroom 401' => 100,
    'Classroom 402' => 20,
    'Seminar hall' => 150
];

$defaultContext = [
    'date' => '',
    'start_time' => '',
    'duration' => 1,
    'capacity' => 10,
    'requirements' => [],
    'step' => 'ask_capacity' // Start by asking for capacity
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Invalid request method', 405);
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException('Invalid JSON input', 400);
    }

    if (empty($input['message'])) {
        throw new InvalidArgumentException('Message is required', 400);
    }

    if (!isset($_SESSION['booking_context'])) {
        $_SESSION['booking_context'] = $defaultContext;
    }
    $context = $_SESSION['booking_context'];

    // Handle different steps in the booking process
    switch ($context['step']) {
        case 'ask_capacity':
            // Extract number from message (e.g., "50 people" -> 50)
            if (preg_match('/\d+/', $input['message'], $matches)) {
                $capacity = (int)$matches[0];
                if ($capacity > 0) {
                    $context['capacity'] = $capacity;
                    $context['step'] = 'ask_date_time';
                    $responseText = "Great! For $capacity people, I can recommend a suitable room. " .
                        "When would you like to book? (e.g., 'March 25 at 2 PM')";
                } else {
                    $responseText = "Please enter a valid number of attendees (greater than 0).";
                }
            } else {
                $responseText = "How many people will be attending? Please enter a number.";
            }
            break;

        case 'ask_date_time':
            // Parse date and time from message (simplified for example)
            $dateTime = parseDateTime($input['message']);
            if ($dateTime) {
                $context['date'] = $dateTime['date'];
                $context['start_time'] = $dateTime['time'];
                $context['step'] = 'check_availability';
                $responseText = "Checking availability for " . formatDateTimeDisplay($dateTime['date'], $dateTime['time']) . "...";
            } else {
                $responseText = "I couldn't understand the date/time. Please try again (e.g., 'March 25 at 2 PM').";
            }
            break;

        case 'check_availability':
            // Find available rooms
            $availableRooms = findAvailableRoomsWithRecommendation(
                $conn,
                $context['date'],
                $context['start_time'],
                $context['duration'],
                $context['capacity'],
                $context['requirements'],
                $ROOM_CAPACITIES
            );

            if (!empty($availableRooms)) {
                $context['step'] = 'confirm_booking';
                $roomList = array_map(function($room) {
                    return $room['name'] . " (Capacity: " . $room['capacity'] . ")";
                }, $availableRooms);

                $responseText = "I found these available rooms:\n" . implode("\n", $roomList) .
                    "\n\nPlease select one to proceed with booking.";
            } else {
                // Suggest alternative times
                $alternativeTimes = suggestAlternativeTimes($conn, $context['date'], $context['start_time']);
                $responseText = "Sorry, no rooms are available at that time. " .
                    "Here are some alternative times:\n" . implode("\n", $alternativeTimes);
                $context['step'] = 'ask_date_time'; // Go back to asking for time
            }
            break;

        case 'confirm_booking':
            // Check if user selected a room
            $selectedRoom = null;
            foreach ($ROOM_CAPACITIES as $room => $capacity) {
                if (stripos($input['message'], $room) !== false) {
                    $selectedRoom = $room;
                    break;
                }
            }

            if ($selectedRoom) {
                $context['selected_room'] = $selectedRoom;
                $context['step'] = 'get_user_details';
                $responseText = "You've selected $selectedRoom. " .
                    "Please provide your details in this format:\n" .
                    "Name: [Your Name]\n" .
                    "Email: [Your Email]\n" .
                    "Purpose: [Meeting/Class/Event]";
            } else {
                $responseText = "I didn't recognize that room. Please select from the available options.";
            }
            break;

        case 'get_user_details':
            // Parse user details (simplified for example)
            $details = parseUserDetails($input['message']);
            if ($details) {
                // Complete the booking
                $bookingSuccess = storeBooking(
                    $conn,
                    $context,
                    $context['selected_room'],
                    $_SESSION['user_id'] ?? 1 // Default to 1 if not logged in
                );

                if ($bookingSuccess) {
                    $responseText = "Thank you, {$details['name']}! Your booking for {$context['selected_room']} on " .
                        formatDateTimeDisplay($context['date'], $context['start_time']) .
                        " is confirmed. A confirmation has been sent to {$details['email']}.";
                    $context['step'] = 'complete';
                } else {
                    $responseText = "Sorry, there was an error processing your booking. Please try again.";
                }
            } else {
                $responseText = "Please provide your details in the requested format:\n" .
                    "Name: [Your Name]\n" .
                    "Email: [Your Email]\n" .
                    "Purpose: [Meeting/Class/Event]";
            }
            break;

        default:
            $responseText = "How can I help you with your booking today?";
            $context = $defaultContext;
    }

    // Update session context
    $_SESSION['booking_context'] = $context;

    // Prepare response
    $response = [
        'status' => 'success',
        'response' => $responseText,
        'next_step' => $context['step']
    ];

    // Include available rooms if we're at that step
    if ($context['step'] === 'confirm_booking') {
        $availableRooms = findAvailableRoomsWithRecommendation(
            $conn,
            $context['date'],
            $context['start_time'],
            $context['duration'],
            $context['capacity'],
            $context['requirements'],
            $ROOM_CAPACITIES
        );
        $response['rooms'] = $availableRooms;
    }

    echo json_encode($response, JSON_THROW_ON_ERROR);

} catch (Throwable $e) {
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage(),
        'response' => "Sorry, we encountered an error. Please try again."
    ], JSON_THROW_ON_ERROR);
} finally {
    ob_end_flush();
    if (isset($conn)) $conn->close();
}

/**
 * Helper function to parse date and time from user input
 */
/**
 * Helper function to parse date and time from user input
 * @param string $message
 * @return array|null Returns array with 'date' and 'time' keys or null if parsing fails
 */
/**
 * Parses date and time from user input with multiple format support
 * @param string $message User input containing date/time information
 * @return array|null Array with 'date' and 'time' keys or null if parsing fails
 */
function parseDateTime($message) {
    // Try DD-MM-YYYY or DD/MM/YYYY format (18-04-2025 or 18/04/2025)
    if (preg_match('/\b(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})\b/', $message, $dateMatches)) {
        $day = $dateMatches[1];
        $month = $dateMatches[2];
        $year = $dateMatches[3];

        // Validate date
        if (!checkdate($month, $day, $year)) {
            return null;
        }

        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        $time = '10:00'; // default time

        // Check if time was also provided (e.g., "18-04-2025 at 2 PM")
        if (preg_match('/\b(\d{1,2}:\d{2}\s*(?:am|pm)?)\b/i', $message, $timeMatches)) {
            $time = date('H:i', strtotime($timeMatches[1]));
        }

        return array('date' => $date, 'time' => $time);
    }

    // Try textual dates (today, tomorrow, next monday) with optional time
    if (preg_match('/(today|tomorrow|next monday|next week|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\D*(\d{1,2})?(?:\D*(\d{1,2}:\d{2}\s*(?:am|pm)?))?/i', $message, $matches)) {
        $dateStr = $matches[1];
        $day = isset($matches[2]) ? $matches[2] : '';
        $timeStr = isset($matches[3]) ? $matches[3] : '';

        // Handle month names (e.g., "April 18")
        if (preg_match('/^(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)/i', $dateStr, $monthMatch)) {
            $month = strtolower($monthMatch[1]);
            $currentYear = date('Y');
            $date = date('Y-m-d', strtotime("$day $month $currentYear"));
        }
        // Handle relative dates
        else {
            $date = date('Y-m-d', strtotime($dateStr));
        }

        // Convert time to 24-hour format
        $time = '10:00'; // default time
        if (!empty($timeStr)) {
            $time = date('H:i', strtotime($timeStr));
        }

        return array('date' => $date, 'time' => $time);
    }

    // Try standalone time (e.g., "2 PM" - assumes today)
    if (preg_match('/\b(\d{1,2}:\d{2}\s*(?:am|pm)?)\b/i', $message, $timeMatches)) {
        return array(
            'date' => date('Y-m-d'),
            'time' => date('H:i', strtotime($timeMatches[1]))
        );
    }

    return null;
}

/**
 * Helper function to format date for display
 */
function formatDateTimeDisplay(string $date, string $time): string {
    return date('F j, Y \a\t g:i A', strtotime("$date $time"));
}

/**
 * Helper function to parse user details
 *//**
 * Helper function to parse user details
 * @param string $message The input message containing user details
 * @return array|null Returns associative array with name, email, purpose or null if parsing fails
 */
function parseUserDetails($message) {
    // Simple parsing - in a real app you'd want more validation
    $namePattern = '/name:\s*(.+?)(?:\n|email:|$)/i';
    $emailPattern = '/email:\s*([^\s@]+@[^\s@]+\.[^\s@]+)(?:\n|purpose:|$)/i';
    $purposePattern = '/purpose:\s*(.+?)(?:\n|$)/i';

    preg_match($namePattern, $message, $nameMatches);
    preg_match($emailPattern, $message, $emailMatches);
    preg_match($purposePattern, $message, $purposeMatches);

    if (!empty($nameMatches) && !empty($emailMatches) && !empty($purposeMatches)) {
        return array(
            'name' => trim($nameMatches[1]),
            'email' => trim($emailMatches[1]),
            'purpose' => trim($purposeMatches[1])
        );
    }
    return null;
}

/**
 * Function to suggest alternative times when primary time is unavailable
 */
function suggestAlternativeTimes(mysqli $conn, string $date, string $time): array {
    $suggestions = [];

    // Check same day, 2 hours later
    $newTime = date('H:i', strtotime("$time +2 hours"));
    $available = checkTimeAvailability($conn, $date, $newTime);
    if ($available) {
        $suggestions[] = "- Same day at " . date('g:i A', strtotime($newTime));
    }

    // Check next day same time
    $newDate = date('Y-m-d', strtotime("$date +1 day"));
    $available = checkTimeAvailability($conn, $newDate, $time);
    if ($available) {
        $suggestions[] = "- Next day (" . date('M j', strtotime($newDate)) . ") at " . date('g:i A', strtotime($time));
    }

    // Check same time next week
    $newDate = date('Y-m-d', strtotime("$date +7 days"));
    $available = checkTimeAvailability($conn, $newDate, $time);
    if ($available) {
        $suggestions[] = "- Next week (" . date('M j', strtotime($newDate)) . ") at " . date('g:i A', strtotime($time));
    }

    return !empty($suggestions) ? $suggestions : ["- No alternative times found. Please try a different time."];
}

/**
 * Helper function to check availability of a specific time
 */
function checkTimeAvailability(mysqli $conn, string $date, string $time): bool {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count FROM bookings 
        WHERE date = ? AND start_time <= ? AND end_time > ?
    ");
    $stmt->bind_param('sss', $date, $time, $time);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] == 0;
}

// Keep your existing storeBooking(), findAvailableRoomsWithRecommendation(), and findAvailableRooms() functions