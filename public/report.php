<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Report | Hall Booking</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Navbar -->
<?php include('include/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 col-md-3 bg-light p-0">
            <div class="sidebar p-3 h-100">
                <h4 class="text-primary mb-4"><i class="fas fa-link me-2"></i>Quick Links</h4>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="book_slot.php"><i class="fas fa-calendar-plus me-2"></i>Book a Hall</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="view_bookings.php"><i class="fas fa-history me-2"></i>Booking History</a>
                    </li>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="../admin/manage_users.php"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="view_bookings.php"><i class="fas fa-calendar-check me-2"></i>Manage Bookings</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item mt-4">
                        <a class="nav-link text-danger" href="../backend/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-10 col-md-9 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary"><i class="fas fa-file-alt me-2"></i>Booking Report</h1>
                <button class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Export Report
                </button>
            </div>
            <p class="text-muted mb-4">Below is the detailed report of seminar hall bookings.</p>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-primary">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>Booking ID</th>
                                <th><i class="fas fa-building me-1"></i>Hall Name</th>
                                <th><i class="fas fa-calendar-day me-1"></i>Date</th>
                                <th><i class="fas fa-clock me-1"></i>Time Slot</th>
                                <th><i class="fas fa-users me-1"></i>Attendees</th>
                                <th><i class="fas fa-chair me-1"></i>Chairs</th>
                                <th><i class="fas fa-tools me-1"></i>Requirements</th>
                                <th><i class="fas fa-user-tie me-1"></i>Booked By</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Sample data - replace with PHP loop -->
                            <tr>
                                <td>1</td>
                                <td>Seminar Hall</td>
                                <td>2024-12-10</td>
                                <td>9:00 AM - 11:00 AM</td>
                                <td>50</td>
                                <td>50</td>
                                <td>Projector, Microphone</td>
                                <td>Prof. Mukul Kulkarni</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Auditorium</td>
                                <td>2024-12-11</td>
                                <td>2:00 PM - 4:00 PM</td>
                                <td>100</td>
                                <td>100</td>
                                <td>Whiteboard</td>
                                <td>Prof. Apurva Barve</td>
                            </tr>
                            <!-- Add more rows dynamically with PHP -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>

<?php include('include/footer.php'); ?>
</body>
</html>