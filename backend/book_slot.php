<?php
session_start();
include('config/config.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

// Get form data
$user_id = $_SESSION['user_id'];
$booker_name = $_POST['booker_name'];
$booker_email = $_POST['booker_email'];
$booker_phone = $_POST['booker_phone'];
$booking_purpose = $_POST['booking_purpose'];
$hall_name = $_POST['hall_name'];
$date = $_POST['date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$capacity = isset($_POST['capacity']) ? (int)$_POST['capacity'] : 0;
$chairs = $_POST['chairs'];
$requirements = $_POST['requirements'];
$status = isset($_POST['status']) ? $_POST['status'] : 'pending';

// Validate time slots
if (strtotime($end_time) <= strtotime($start_time)) {
    echo "<script>
        alert('End time must be after start time.');
        window.history.back();
    </script>";
    exit;
}

// Enhanced conflict detection
$check_sql = "SELECT * FROM bookings 
             WHERE hall_name = ? 
             AND date = ? 
             AND (
                 (start_time < ? AND end_time > ?) OR  -- New booking inside existing
                 (start_time >= ? AND start_time < ?) OR  -- New booking overlaps start
                 (end_time > ? AND end_time <= ?) OR  -- New booking overlaps end
                 (start_time <= ? AND end_time >= ?)  -- Existing inside new booking
             )";

$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ssssssssss",
    $hall_name, $date,
    $end_time, $start_time,   // Case 1
    $start_time, $end_time,   // Case 2
    $start_time, $end_time,   // Case 3
    $start_time, $end_time    // Case 4
);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    // Get conflicting bookings for detailed message
    $conflicts = [];
    while ($row = $result->fetch_assoc()) {
        $conflicts[] = [
            'time' => date("g:i A", strtotime($row['start_time'])) . " to " .
                date("g:i A", strtotime($row['end_time'])),
            'purpose' => $row['booking_purpose']
        ];
    }

    $conflict_message = "This time slot conflicts with existing booking(s):\n";
    foreach ($conflicts as $conflict) {
        $conflict_message .= "- {$conflict['time']} ({$conflict['purpose']})\n";
    }
    $conflict_message .= "\nPlease choose a different time slot.";

    echo "<script>
        alert(`" . addslashes($conflict_message) . "`);
        window.history.back();
    </script>";
    exit;
}
$check_stmt->close();

// Insert the booking
$sql = "INSERT INTO bookings 
        (user_id, booker_name, booker_email, booker_phone, booking_purpose, 
         hall_name, date, start_time, end_time, capacity, chairs, requirements, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssssisss",
    $user_id, $booker_name, $booker_email, $booker_phone, $booking_purpose,
    $hall_name, $date, $start_time, $end_time, $capacity, $chairs, $requirements, $status
);

if ($stmt->execute()) {
    // Send confirmation email
    $subject = "Booking Confirmation";
    $message = "Your booking for $hall_name on $date from $start_time to $end_time has been received.";
    mail($booker_email, $subject, $message);

    echo "<script>
        alert('Booking successful! Confirmation sent to your email.');
        window.location='../public/view_bookings.php';
    </script>";
} else {
    echo "<script>
        alert('Booking failed. Error: " . addslashes($conn->error) . "');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>