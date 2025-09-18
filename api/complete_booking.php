<?php
// Ensure no output before headers
if (ob_get_level()) ob_end_clean();


// Set strict headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configure error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Set up error logging
$log_dir = __DIR__ . '/../../logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
$log_file = $log_dir . '/booking_errors.log';

function log_error($message) {
    global $log_file;
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $log_message = sprintf(
        "[%s] %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $message,
        $backtrace[0]['file'] ?? 'unknown',
        $backtrace[0]['line'] ?? 0
    );
    file_put_contents($log_file, $log_message . PHP_EOL, FILE_APPEND);
}

// Register shutdown function for fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Internal server error occurred',
            'code' => 500,
            'details' => 'A fatal error prevented request processing'
        ]);
        log_error("Fatal Error: {$error['message']}");
        exit;
    }
});

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed', 405);
    }

    // Start secure session
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 86400,
            'path' => '/BookMySlot',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }

    // Check authentication
    if (empty($_SESSION['user_id'])) {
        throw new Exception('Authentication required. Please log in.', 401);
    }

    // Get and validate input
    $jsonInput = file_get_contents('php://input');
    if (empty($jsonInput)) {
        throw new Exception('No input data received', 400);
    }

    $input = json_decode($jsonInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg(), 400);
    }

    // Validate required fields
    $required = ['hall_name', 'date', 'start_time', 'booking_purpose'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field", 400);
        }
    }
    log_error("Attempting to connect to database");
    if (!file_exists(__DIR__ . '/../backend/config/config.php')) {
        log_error("Config file not found");
    }

    // Database connection
    require_once __DIR__ . '/../backend/config/config.php';

    // Verify database connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed', 500);
    }

    // Get user details
    $userStmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
    if (!$userStmt) {
        throw new Exception('Database preparation failed: ' . htmlspecialchars($conn->error), 500);
    }

    $userStmt->bind_param('i', $_SESSION['user_id']);
    if (!$userStmt->execute()) {
        throw new Exception('Database execution failed: ' . htmlspecialchars($userStmt->error), 500);
    }

    $user = $userStmt->get_result()->fetch_assoc();
    if (!$user) {
        throw new Exception('User account not found', 404);
    }

    // Calculate end time (1 hour duration)
    $startTime = date('H:i:s', strtotime($input['start_time']));
    $endTime = date('H:i:s', strtotime($startTime) + 3600);

    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO bookings (
            user_id, hall_name, date, start_time, end_time, status,
            booker_name, booker_email, booker_phone, booking_purpose,
            capacity, requirements
        ) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception('Database preparation failed: ' . htmlspecialchars($conn->error), 500);
    }

    $bindResult = $stmt->bind_param(
        'issssssssis',
        $_SESSION['user_id'],
        $input['hall_name'],
        $input['date'],
        $startTime,
        $endTime,
        $user['name'],
        $user['email'],
        $input['booker_phone'] ?? '',
        $input['booking_purpose'],
        $input['capacity'] ?? 1,
        $input['requirements'] ?? ''
    );

    if (!$bindResult) {
        throw new Exception('Database binding failed', 500);
    }

    if (!$stmt->execute()) {
        throw new Exception('Database execution failed: ' . htmlspecialchars($stmt->error), 500);
    }

    // Success response
    $response = [
        'success' => true,
        'booking_id' => $conn->insert_id,
        'message' => 'Booking submitted successfully',
        'data' => [
            'room' => $input['hall_name'],
            'date' => $input['date'],
            'time' => $startTime,
            'purpose' => $input['booking_purpose']
        ]
    ];

    // Ensure valid JSON output
    $jsonResponse = json_encode($response);
    if ($jsonResponse === false) {
        throw new Exception('Failed to encode JSON response', 500);
    }

    echo $jsonResponse;

} catch (Exception $e) {
    // Log the error
    log_error($e->getMessage());

    // Ensure valid JSON error response
    $errorResponse = json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode() ?: 500
    ]);

    if ($errorResponse === false) {
        // Fallback minimal error if JSON encoding fails
        http_response_code(500);
        echo '{"success":false,"error":"Internal server error","code":500}';
    } else {
        http_response_code($e->getCode() ?: 500);
        echo $errorResponse;
    }
    exit;
}