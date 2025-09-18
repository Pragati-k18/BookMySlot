<?php
include('../backend/config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = intval($_POST['id']);

    // Log received ID for debugging
    error_log("User ID received: $userId");

    // Delete user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare statement failed: " . $conn->error);
        echo "error";
        exit;
    }

    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo "success";
    } else {
        error_log("Execute failed: " . $stmt->error);
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    error_log("Invalid request method or missing ID.");
    echo "error";
}
?>
