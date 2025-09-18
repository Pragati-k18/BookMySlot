<?php
include('config/config.php');

// Fetch form data
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt the password
$role = $_POST['role'];

// Insert into the database
$sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    header("Location: ../public/login.php?success=1");
} else {
    echo "Error: " . $stmt->error;
}
$conn->close();
?>
