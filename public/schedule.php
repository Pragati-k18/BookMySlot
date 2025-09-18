<?php
session_start();
include('../backend/config/config.php'); // Make sure DB connection works


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch upcoming bookings for logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.id, b.hall_name, b.date, b.start_time, b.end_time, b.status, u.name as booked_by 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id
        WHERE b.user_id = ? AND b.date >= CURDATE()
        ORDER BY b.date ASC, b.start_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule | Seminar Hall Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Make sure path is correct -->
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Include Navbar -->
<?php include('include/header.php'); ?>

<div class="container-fluid flex-grow-1 mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 col-md-3 bg-light p-0">
            <div class="sidebar p-3 h-100">
                <h4 class="text-info mb-4"><i class="fas fa-link me-2"></i>Quick Links</h4>
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

        <div class="col-lg-10 col-md-9 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 text-primary"><i class="fas fa-calendar-alt me-2"></i>Upcoming Bookings</h2>
            </div>

            <div class="card shadow-sm" id="listView">
                <div class="card-header bg-info text-white">
                    <h3 class="h5 mb-0"><i class="fas fa-list me-2"></i>List of Upcoming Bookings</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Hall</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($bookings)): ?>
                                <?php foreach($bookings as $b):
                                    $status_class = ($b['status']=='approved') ? 'bg-success' : (($b['status']=='pending') ? 'bg-warning text-dark' : 'bg-danger');
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($b['hall_name']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($b['date'])); ?></td>
                                        <td><?php echo date('h:i A', strtotime($b['start_time'])) . ' - ' . date('h:i A', strtotime($b['end_time'])); ?></td>
                                        <td><span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">No upcoming bookings found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="assets/js/main.js"></script>
<?php include('include/footer.php'); ?>
</body>
</html>
