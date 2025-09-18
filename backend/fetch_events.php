<?php
include('config/config.php');

// Fetch all bookings
$sql = "SELECT hall_name, date, start_time, end_time FROM bookings";
$result = $conn->query($sql);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => $row['hall_name'],
        'start' => $row['date'] . 'T' . $row['start_time'],
        'end' => $row['date'] . 'T' . $row['end_time']
    ];
}

echo json_encode($events);
$conn->close();
?>
