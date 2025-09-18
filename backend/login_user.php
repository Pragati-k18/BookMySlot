<?php
session_start(); // Start a session
include('config/config.php');

// Fetch form data
$email = $_POST['email'];
$password = $_POST['password'];

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/admin_dashboard.php");
        } else {
            header("Location: ../public/dashboard.php");
        }
    } else {
        echo "<script>alert('Invalid email or password. Try again.');window.location='../public/login.php';</script>";
    }
} else {
    echo "<script>alert('No account found with this email. Please register.');window.location='../public/register.php';</script>";
}
$conn->close();
?>
