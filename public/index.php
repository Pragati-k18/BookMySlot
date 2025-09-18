<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seminar Hall Booking System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Navbar -->
<?php include('include/header.php'); ?>

<main class="flex-grow-1 py-4">
    <div class="container">
        <!-- Hero Section -->
        <section class="homepage-header text-center py-5 my-4 bg-light rounded-3 shadow-sm">
            <h1 class="display-4 fw-bold text-primary mb-3">Experience Excellence with "Book My Slot"</h1>
            <h2 class="fs-3 text-secondary">Your Ideal Hall Booking Partner</h2>
            <div class="mt-4">
                <a href="book_slot.php" class="btn btn-primary btn-lg px-4 me-3">
                    <i class="fas fa-calendar-plus me-2"></i>Book Now
                </a>
                <a href="#facilities" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fas fa-search me-2"></i>Explore Facilities
                </a>
            </div>
        </section>

        <!-- Facilities Gallery -->
        <section id="facilities" class="homepage-images mb-5">
            <h2 class="text-center mb-4 fw-bold">Explore Our Facilities</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden">
                        <img src="assets/images/seminar_hall_1.webp" class="card-img-top" alt="Seminar Hall 1" style="height: 200px; object-fit: cover;">
                        <div class="card-body text-center bg-light">
                            <h5 class="card-title fw-bold">Modern Seminar Hall</h5>
                            <p class="card-text">Fully equipped with latest AV technology</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden">
                        <img src="assets/images/auditorium.webp" class="card-img-top" alt="Auditorium" style="height: 200px; object-fit: cover;">
                        <div class="card-body text-center bg-light">
                            <h5 class="card-title fw-bold">Spacious Auditorium</h5>
                            <p class="card-text">Perfect for large conferences and events</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0 overflow-hidden">
                        <img src="assets/images/classroom.webp" class="card-img-top" alt="Classroom" style="height: 200px; object-fit: cover;">
                        <div class="card-body text-center bg-light">
                            <h5 class="card-title fw-bold">Training Rooms</h5>
                            <p class="card-text">Ideal for workshops and small meetings</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Quick Actions for Logged In Users -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 border-primary">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-calendar-plus display-4 text-primary mb-3"></i>
                            <h3 class="h4 mb-3">Book a New Slot</h3>
                            <p class="mb-4">Reserve our facilities for your upcoming event</p>
                            <a href="book_slot.php" class="btn btn-primary px-4">
                                <i class="fas fa-plus me-2"></i>Create Booking
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 border-success">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-list-alt display-4 text-success mb-3"></i>
                            <h3 class="h4 mb-3">Manage Bookings</h3>
                            <p class="mb-4">View and modify your existing reservations</p>
                            <a href="view_bookings.php" class="btn btn-success px-4">
                                <i class="fas fa-eye me-2"></i>View All
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Call to Action for Guests -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 border-info">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-user-plus display-4 text-info mb-3"></i>
                            <h3 class="h4 mb-3">New to the system?</h3>
                            <p class="mb-4">Register now to book our facilities</p>
                            <a href="register.php" class="btn btn-info text-white px-4">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 border-warning">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-sign-in-alt display-4 text-warning mb-3"></i>
                            <h3 class="h4 mb-3">Already have an account?</h3>
                            <p class="mb-4">Login to access your dashboard</p>
                            <a href="login.php" class="btn btn-warning px-4">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js" defer></script>

<?php include('include/footer.php'); ?>
</body>
</html>