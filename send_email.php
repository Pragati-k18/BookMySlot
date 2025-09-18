<?php
//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;
//
//require 'vendor/autoload.php';  // Ensure PHPMailer is installed
//
//$mail = new PHPMailer(true);
//
//try {
//    // SMTP Configuration (Use Mailtrap credentials)
//    $mail->isSMTP();
//    $mail->Host       = 'sandbox.smtp.mailtrap.io';  // Mailtrap SMTP server
//    $mail->SMTPAuth   = true;
//    $mail->Username   = 'efe109148c194f';  // Replace with Mailtrap username
//    $mail->Password   = '99c908dac73a76';  // Replace with Mailtrap password
//    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//    $mail->Port       = 587;
//
//    // Email Details
//    $mail->setFrom('noreply@bookmyslot.com', 'BookMySlot');  // Sender email
//    $mail->addAddress('pragatikhobragade76@gmail.com', 'Test User');  // Receiver's email
//    $mail->Subject = 'Booking Confirmation';
//    $mail->Body    = 'Your booking has been confirmed!';
//
//    // Send Email
//    if ($mail->send()) {
//        echo "✅ Email sent successfully (Check Mailtrap Inbox)!";
//    } else {
//        echo "❌ Email failed: " . $mail->ErrorInfo;
//    }
//} catch (Exception $e) {
//    echo "❌ Email failed: {$mail->ErrorInfo}";
//}
//?>
