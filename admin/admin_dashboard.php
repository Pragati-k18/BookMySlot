<?php
session_start();
include('../backend/config/config.php');

// Ensure the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | BookMySlot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-dashboard">
<!-- Sidebar and Top Navigation -->
<div class="d-flex">
    <!-- Sidebar -->
    <div class="admin-sidebar bg-dark text-white p-3">
        <div class="sidebar-header text-center mb-4">
            <h4 class="mb-0">BookMySlot</h4>
            <small>Admin Panel</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="admin_dashboard.php">
                    <i class="fas fa-calendar-alt me-2"></i>Manage Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php">
                    <i class="fas fa-users me-2"></i>Manage Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php">
                    <i class="fas fa-chart-bar me-2"></i>View Reports
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="admin-main-content flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </h1>
            <div class="admin-welcome">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar me-2"></i>All Bookings
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Purpose</th>
                            <th>Hall</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM bookings");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                                <td>{$row['id']}</td>
                                                <td>{$row['user_id']}</td>
                                                <td>{$row['booker_name']}</td>
                                                <td>{$row['booker_email']}</td>
                                                <td>{$row['booker_phone']}</td>
                                                <td>{$row['booking_purpose']}</td>
                                                <td>{$row['hall_name']}</td>
                                                <td>{$row['date']}</td>
                                                <td>{$row['start_time']} - {$row['end_time']}</td>
                                                <td><span class='badge bg-".getStatusColor($row['status'])."'>".ucfirst($row['status'])."</span></td>
                                                <td>
                                                    <div class='btn-group btn-group-sm'>
                                                        <form action='update_booking.php' method='POST' class='d-inline'>
                                                            <input type='hidden' name='booking_id' value='{$row['id']}'>
                                                            <button type='submit' name='action' value='approve' class='btn btn-success btn-sm'>
                                                                <i class='fas fa-check'></i>
                                                            </button>
                                                            <button type='submit' name='action' value='deny' class='btn btn-danger btn-sm'>
                                                                <i class='fas fa-times'></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                              </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11' class='text-center'>No bookings available</td></tr>";
                        }

                        function getStatusColor($status) {
                            switch(strtolower($status)) {
                                case 'approved': return 'success';
                                case 'pending': return 'warning';
                                case 'denied': return 'danger';
                                default: return 'secondary';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>