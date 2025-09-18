<?php
// Start session at the top of the file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL (change folder name if different)
$base_url = "http://localhost/BookMySlot/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Book seminar halls and meeting rooms with ease through our convenient online booking system">
    <meta name="keywords" content="seminar hall booking, meeting room reservation, event space booking, classroom scheduling">
    <meta name="author" content="Book My Slot Team">
    <meta name="theme-color" content="#123C69">

    <title>Book My Slot | Seminar Hall Booking System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_url; ?>assets/images/logo_seminar_hall.PNG">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>public/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $base_url; ?>public/index.php">
            <img src="<?php echo $base_url; ?>public/assets/images/logo_seminar_hall.PNG" alt="Hall Booking Logo" height="40" class="me-2">
            <span class="fw-bold">BOOK MY SLOT</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>public/index.php"><i class="fas fa-home me-1"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>public/index.php#about"><i class="fas fa-info-circle me-1"></i> About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>public/contact.php"><i class="fas fa-envelope me-1"></i> Contact</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Show when user is logged in -->
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>public/dashboard.php"><i class="fas fa-user me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="<?php echo $base_url; ?>/backend/logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                <?php else: ?>
                    <!-- Show when user is not logged in -->
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>public/login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $base_url; ?>public/register.php"><i class="fas fa-user-plus me-1"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
