<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to parse date/time
function parseDateTimeInput($input) {
    // Try multiple date formats
    $formats = [
        // ISO format (2023-04-20)
        '/\b(\d{4})-(\d{2})-(\d{2})\b/' => function($m) { return $m[0]; },

        // DD-MM-YYYY or DD/MM/YYYY
        '/\b(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})\b/' => function($m) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        },

        // Month names (April 20 2023)
        '/(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*\s+(\d{1,2})\D+(\d{4})/i' => function($m) {
            return sprintf('%04d-%02d-%02d', $m[3], date('m', strtotime($m[1])), $m[2]);
        },

        // Relative dates (today, tomorrow)
        '/(today|tomorrow|next week|next month)/i' => function($m) {
            return date('Y-m-d', strtotime($m[1]));
        }
    ];

    $date = date('Y-m-d'); // Default to today
    foreach ($formats as $pattern => $callback) {
        if (preg_match($pattern, $input, $matches)) {
            $date = $callback($matches);
            break;
        }
    }

    // Time parsing (default to 10:00)
    $time = '10:00';
    if (preg_match('/\b(\d{1,2}:\d{2}\s*(?:am|pm)?)\b/i', $input, $timeMatches)) {
        $time = date('H:i', strtotime($timeMatches[1]));
    }

    return ['date' => $date, 'time' => $time];
}

try {
    // Get raw input
    $json = file_get_contents('php://input');

    // Decode JSON
    $input = json_decode($json, true);

    // Validate input
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    if (empty($input['datetime'])) {
        throw new Exception('Missing datetime parameter');
    }

    // Parse the datetime
    $result = parseDateTimeInput($input['datetime']);

    // Return success
    echo json_encode([
        'success' => true,
        'date' => $result['date'],
        'time' => $result['time']
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}