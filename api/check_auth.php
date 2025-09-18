<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../backend/config/config.php';

session_start();

try {
    if (empty($_SESSION['user_id'])) {
        throw new Exception('Not logged in');
    }

    // Get user details
    $stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        throw new Exception('User account not found');
    }

    echo json_encode([
        'logged_in' => true,
        'user' => $user
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'logged_in' => false,
        'error' => $e->getMessage()
    ]);
}