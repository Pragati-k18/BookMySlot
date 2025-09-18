<?php
session_start();
header('Content-Type: application/json');
require_once '../backend/config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Add this at the beginning of the try block
$requiredFields = [
    'user_id', 'hall_name', 'date',
    'start_time', 'end_time', 'duration', 'capacity'
];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        throw new InvalidArgumentException("Missing required field: $field", 400);
    }
}

// Validate date format
if (!DateTime::createFromFormat('Y-m-d', $data['date'])) {
    throw new InvalidArgumentException("Invalid date format", 400);
}

// Validate time format
if (!DateTime::createFromFormat('H:i:s', $data['start_time']) ||
    !DateTime::createFromFormat('H:i:s', $data['end_time'])) {
    throw new InvalidArgumentException("Invalid time format", 400);
}

try {
    // Calculate end_time
    $end_time = date('H:i:s', strtotime($data['start_time']) + ($data['duration'] * 3600));




    // Check room availability
    $stmt = $conn->prepare("
        SELECT id FROM bookings 
        WHERE hall_name = ? AND date = ?
        AND (
            (start_time <= ? AND end_time >= ?) OR 
            (start_time <= ? AND end_time >= ?)
        )
    ");

    // Correct bind parameters
    $stmt->bind_param("ssss",
        $data['hall_name'],
        $data['date'],
        $data['end_time'],  // New booking's end time
        $data['start_time'] // New booking's start time
    );

    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("This room is already booked for the selected time");
    }

    // Create booking
    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (user_id, hall_name, date, start_time, end_time)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("issss",
        $_SESSION['user_id'],
        $data['hall_name'],
        $data['date'],
        $data['start_time'],
        $end_time
    );

    // Update INSERT statement
    $stmt = $conn->prepare("
    INSERT INTO bookings 
    (user_id, hall_name, date, start_time, end_time, duration, capacity, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending') -- Add missing fields
");

// Update bind_param
    $stmt->bind_param("issssii",
        $_SESSION['user_id'],
        $data['hall_name'],
        $data['date'],
        $data['start_time'],
        $data['end_time'],
        $data['duration'], // From request
        $data['capacity'] // From request
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'booking_id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

