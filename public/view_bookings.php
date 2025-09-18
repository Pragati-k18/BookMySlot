<?php
session_start();
include('../backend/config/config.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch bookings for the logged-in user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings | Book My Slot</title>
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

<div class="container-fluid flex-grow-1">
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
                        <a class="nav-link active" href="view_bookings.php"><i class="fas fa-history me-2"></i>Booking History</a>
                    </li>

                    <li class="nav-item mb-2">
                        <a class="nav-link btn btn-primary text-white" href="calendar_view.php"><i class="fas fa-calendar me-2"></i>View Booking Calendar</a>
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
                <h2 class="h3 text-info"><i class="fas fa-calendar-check me-2"></i>Your Bookings</h2>
                <a href="book_slot.php" class="btn btn-info">
                    <i class="fas fa-plus me-2"></i>New Booking
                </a>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-primary">
                                <tr>
                                    <th><i class="fas fa-building me-1"></i>Hall Name</th>
                                    <th><i class="fas fa-calendar-day me-1"></i>Date</th>
                                    <th><i class="fas fa-clock me-1"></i>Start Time</th>
                                    <th><i class="fas fa-clock me-1"></i>End Time</th>
                                    <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['hall_name']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($row['date'])); ?></td>
                                        <td><?php echo date('g:i A', strtotime($row['start_time'])); ?></td>
                                        <td><?php echo date('g:i A', strtotime($row['end_time'])); ?></td>
                                        <td>
                                            <?php
                                            $status = strtolower($row['status']);
                                            $badgeClass = '';
                                            if ($status === 'confirmed') {
                                                $badgeClass = 'bg-success';
                                            } elseif ($status === 'pending') {
                                                $badgeClass = 'bg-warning text-dark';
                                            } elseif ($status === 'rejected') {
                                                $badgeClass = 'bg-danger';
                                            } else {
                                                $badgeClass = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times display-4 text-muted mb-4"></i>
                        <h3 class="h4 text-muted mb-3">No Bookings Found</h3>
                        <p class="text-muted">You haven't made any bookings yet.</p>
                        <a href="book_slot.php" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-2"></i>Make Your First Booking
                        </a>
                    </div>
                </div>
            <?php endif; ?>
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