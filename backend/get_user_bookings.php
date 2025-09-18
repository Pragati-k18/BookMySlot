<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$user_id = $_GET['user_id'] ?? 0;

try {
    $stmt = $conn->prepare("SELECT 
        b.id, 
        b.hall_name, 
        b.date, 
        b.start_time, 
        b.end_time, 
        b.capacity, 
        b.chairs, 
        b.booking_purpose, 
        b.requirements, 
        b.status
        FROM bookings b
        WHERE b.user_id = ?
        ORDER BY b.date DESC, b.start_time DESC
        LIMIT 5");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    echo json_encode([
        'success' => true,
        'bookings' => $bookings
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
