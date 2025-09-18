<?php
session_start();

// Hardcoded credentials for the admin (Office Staff)
$admin_email = "admin@bookmyslot.com";
$admin_password = "Admin@123"; // Use a strong password

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the input matches the predefined credentials
    if ($email === $admin_email && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        header("Location: admin_dashboard.php"); // Redirect to dashboard
        exit;
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | BookMySlot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Panel CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-login-page d-flex align-items-center justify-content-center min-vh-100">

<div class="login-container w-100 px-3">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                <h1 class="h4 text-dark fw-bold">Admin Login</h1>
                <p class="text-muted small">Sign in to access your dashboard</p>
            </div>

            <!-- Error Alert -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="admin_login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter your password" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-white mt-4 small mb-0">
        &copy; <?= date("Y") ?> BookMySlot. All rights reserved.
    </p>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
