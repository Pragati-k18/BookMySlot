<?php
session_start();
include('config/config.php');

// Ensure admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit;
}

// Fetch booking ID from query string
$id = $_GET['id'];

// Delete booking from database
$sql = "DELETE FROM bookings WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Booking deleted successfully!');window.location='../admin/manage_bookings.php';</script>";
} else {
    echo "<script>alert('Failed to delete booking.');window.location='../admin/manage_bookings.php';</script>";
}
$conn->close();
?>
