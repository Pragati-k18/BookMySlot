<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../backend/config/config.php';

$user_id = $_SESSION['user_id'];

// Function to get total bookings
function getTotalBookings($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

// Function to get upcoming bookings
function getUpcomingBookings($conn, $user_id) {
    $current_date = date('Y-m-d H:i:s');
    $sql = "SELECT COUNT(*) as count FROM bookings 
            WHERE user_id = ? AND status = 'approved'
              AND STR_TO_DATE(CONCAT(date, ' ', start_time), '%Y-%m-%d %H:%i:%s') > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $current_date);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

// Function to get pending bookings
function getPendingBookings($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

// Function to get next upcoming booking
function getNextBooking($conn, $user_id) {
    $current_date = date('Y-m-d H:i:s');
    $sql = "SELECT hall_name, date, start_time FROM bookings 
            WHERE user_id = ? AND status = 'approved'
              AND STR_TO_DATE(CONCAT(date, ' ', start_time), '%Y-%m-%d %H:%i:%s') > ?
            ORDER BY date ASC, start_time ASC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $current_date);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to get denied bookings
function getDeniedBookings($conn, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ? AND status = 'denied'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

// Get counts
$total_count = getTotalBookings($conn, $user_id);
$upcoming_count = getUpcomingBookings($conn, $user_id);
$pending_count = getPendingBookings($conn, $user_id);
$next_booking = getNextBooking($conn, $user_id);
$denied_count = getDeniedBookings($conn, $user_id);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Book My Slot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<?php include('include/header.php'); ?>


<main class="flex-grow-1 py-4">
    <div class="container">

        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-primary"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                        <p class="lead">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
                        <span class="badge bg-secondary"><i class="fas fa-user-tag me-1"></i><?php echo ucfirst($_SESSION['role']); ?></span>
                    </div>
                    <div>
                        <a href="book_slot.php" class="btn btn-primary me-2"><i class="fas fa-calendar-plus me-1"></i>New Booking</a>
                        <a href="view_bookings.php" class="btn btn-outline-primary"><i class="fas fa-history me-1"></i>View Bookings</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card h-100 booking-card text-center">
                    <div class="card-body">
                        <i class="fas fa-calendar-check display-4 text-primary mb-3"></i>
                        <h3 class="h5">Upcoming Bookings</h3>
                        <p class="display-6 fw-bold"><?php echo $upcoming_count; ?></p>
                        <a href="view_bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100 booking-card text-center">
                    <div class="card-body">
                        <i class="fas fa-clock-rotate-left display-4 text-warning mb-3"></i>
                        <h3 class="h5">Pending Approvals</h3>
                        <p class="display-6 fw-bold"><?php echo $pending_count; ?></p>
                        <a href="view_bookings.php" class="btn btn-sm btn-outline-warning">Review</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100 booking-card text-center">
                    <div class="card-body">
                        <i class="fas fa-calendar-day display-4 text-info mb-3"></i>
                        <h3 class="h5">Next Booking</h3>
                        <?php if ($next_booking): ?>
                            <p class="fw-bold">
                                <?php echo $next_booking['hall_name']; ?><br>
                                <?php echo date("d M Y, h:i A", strtotime($next_booking['date'].' '.$next_booking['start_time'])); ?>
                            </p>
                        <?php else: ?>
                            <p class="fw-bold">No Upcoming Booking</p>
                        <?php endif; ?>
                        <a href="view_bookings.php" class="btn btn-sm btn-outline-info">View</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Booking Summary -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 booking-card">
                    <div class="card-header  text-white">
                        <h3 class="h5 mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="book_slot.php" class="btn btn-outline-info text-start"><i class="fas fa-calendar-plus me-2"></i>Book a Seminar Hall</a>
                            <a href="view_bookings.php" class="btn btn-outline-info text-start"><i class="fas fa-list-ul me-2"></i>View Your Bookings</a>
                            <a href="schedule.php" class="btn btn-outline-info text-start"><i class="fas fa-calendar-alt me-2"></i>View Schedule</a>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="../admin/manage_users.php" class="btn btn-outline-danger text-start"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Summary Card -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 booking-card">
                    <div class="card-header  text-white">
                        <h3 class="h5 mb-0"><i class="fas fa-chart-pie me-2"></i>Booking Summary</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-list text-info me-2"></i>Total Bookings</span>
                                <span class="badge bg-info rounded-pill"><?php echo $total_count; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-times-circle text-danger me-2"></i>Denied Bookings</span>
                                <span class="badge bg-danger rounded-pill"><?php echo $denied_count; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js"></script>

<?php include('include/footer.php'); ?>
</body>
</html>
