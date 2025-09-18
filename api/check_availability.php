<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../backend/config/config.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Get input data
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    $required = ['date', 'time', 'room'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Calculate end time (1 hour duration)
    $startTime = $input['time'];
    $endTime = date('H:i:s', strtotime($startTime) + 3600);

    // Check availability
    $stmt = $conn->prepare("
        SELECT id FROM bookings 
        WHERE hall_name = ? 
        AND date = ? 
        AND (
            (start_time < ? AND end_time > ?) OR
            (start_time < ? AND end_time > ?) OR
            (start_time >= ? AND start_time < ?)
        )
        LIMIT 1
    ");

    $stmt->bind_param('ssssssss',
        $input['room'],
        $input['date'],
        $endTime, $startTime,
        $endTime, $startTime,
        $startTime, $endTime
    );

    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare response
    $response = [
        'available' => $result->num_rows === 0,
        'requested' => [
            'room' => $input['room'],
            'date' => $input['date'],
            'time' => $input['time']
        ]
    ];

    // If not available, suggest alternatives
    if (!$response['available']) {
        $response['alternatives'] = getAlternativeTimes($conn, $input);
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}

function getAlternativeTimes($conn, $input) {
    $alternatives = [];
    $baseDate = $input['date'];
    $baseTime = $input['time'];

    // Try same day +2 hours
    $newTime = date('H:i', strtotime($baseTime) + 7200);
    if (isAvailable($conn, $input['room'], $baseDate, $newTime)) {
        $alternatives[] = ['date' => $baseDate, 'time' => $newTime];
    }

    // Try next day same time
    $newDate = date('Y-m-d', strtotime($baseDate . ' +1 day'));
    if (isAvailable($conn, $input['room'], $newDate, $baseTime)) {
        $alternatives[] = ['date' => $newDate, 'time' => $baseTime];
    }

    // Try same time different room (smallest available first)
    $altRooms = getAlternativeRooms($conn, $input);
    foreach ($altRooms as $room) {
        $alternatives[] = [
            'date' => $baseDate,
            'time' => $baseTime,
            'room' => $room['name'],
            'capacity' => $room['capacity']
        ];
    }

    return $alternatives;
}

function isAvailable($conn, $room, $date, $time) {
    $endTime = date('H:i:s', strtotime($time) + 3600);

    $stmt = $conn->prepare("
        SELECT id FROM bookings 
        WHERE hall_name = ? 
        AND date = ? 
        AND (
            (start_time < ? AND end_time > ?) OR
            (start_time < ? AND end_time > ?) OR
            (start_time >= ? AND start_time < ?)
        )
        LIMIT 1
    ");

    $stmt->bind_param('ssssssss',
        $room, $date,
        $endTime, $time,
        $endTime, $time,
        $time, $endTime
    );

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0;
}

function getAlternativeRooms($conn, $input) {
    $stmt = $conn->prepare("
        SELECT name, capacity FROM rooms 
        WHERE name != ? AND capacity >= ?
        ORDER BY capacity ASC
    ");
    $stmt->bind_param('si', $input['room'], $input['capacity'] || 1);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}