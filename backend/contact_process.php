<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Example: Save to a database or send an email
    // In this example, we'll just display a success message.
    if (!empty($name) && !empty($email) && !empty($message)) {
        echo "<p style='color: green;'>Thank you, $name! Your message has been received. We'll get back to you soon.</p>";
    } else {
        echo "<p style='color: red;'>All fields are required. Please try again.</p>";
    }
} else {
    header("Location: ../public/contact.php");
    exit;
}
?>
