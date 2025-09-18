<?php
session_start();
include('../backend/config/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';  // Ensure PHPMailer is installed

// Ensure the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    // Determine status based on action
    if ($action === 'approve') {
        $status = 'approved';
    } elseif ($action === 'deny') {
        $status = 'denied';
    } else {
        header("Location: admin_dashboard.php");
        exit;
    }

    // Fetch booker's email
    $stmt = $conn->prepare("SELECT booker_name, booker_email FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        echo "<script>alert('Booking not found.');window.location='admin_dashboard.php';</script>";
        exit;
    }

    $booker_name = $booking['booker_name'];
    $booker_email = $booking['booker_email'];

    // Update booking status in the database
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);

    if ($stmt->execute()) {
        // ✅ Send Email Notification
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';  // Mailtrap SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'efe109148c194f';  // Replace with your Mailtrap username
            $mail->Password   = '99c908dac73a76';  // Replace with your Mailtrap password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email Details
            $mail->setFrom('noreply@bookmyslot.com', 'BookMySlot');
            $mail->addAddress($booker_email, $booker_name);  // Booker's email
            $mail->Subject = 'Booking Status Update';

            // Customize Email Body
            if ($status === 'approved') {
                $mail->Body = "Dear $booker_name,\n\nYour booking has been **approved**! 🎉\n\nThank you for using BookMySlot.";
            } else {
                $mail->Body = "Dear $booker_name,\n\nWe regret to inform you that your booking has been **denied**. ❌\n\nFor further inquiries, contact us.";
            }

            // Send Email
            $mail->send();
            echo "<script>alert('Booking status updated & email sent successfully!');window.location='admin_dashboard.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Booking updated, but email failed: {$mail->ErrorInfo}');window.location='admin_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Failed to update booking status.');window.location='admin_dashboard.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
