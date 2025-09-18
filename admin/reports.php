<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include('../backend/config/config.php');

// Fetch booking status counts securely
$query = "SELECT status, COUNT(*) AS count FROM bookings GROUP BY status";
$result = $conn->query($query);

// Prepare data for chart
$statuses = [];
$counts = [];
$colors = [];
while ($row = $result->fetch_assoc()) {
    $statuses[] = $row['status'];
    $counts[] = $row['count'];
    $colors[] = getStatusColor($row['status']);
}

function getStatusColor($status) {
    switch(strtolower($status)) {
        case 'approved': return '#1cc88a';
        case 'pending': return '#f6c23e';
        case 'denied': return '#e74a3b';
        default: return '#858796';
    }
}

// Reset pointer for table display
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | BookMySlot Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-reports-page">
<!-- Reusing the same sidebar structure -->
<div class="d-flex">
    <!-- Sidebar -->
    <div class="admin-sidebar bg-dark text-white p-3">
        <div class="sidebar-header text-center mb-4">
            <h4 class="mb-0">BookMySlot</h4>
            <small>Admin Panel</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-calendar-alt me-2"></i>Manage Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php">
                    <i class="fas fa-users me-2"></i>Manage Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="reports.php">
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
                <i class="fas fa-chart-pie me-2"></i>Booking Reports
            </h1>
            <div class="admin-welcome">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <?php
            $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM bookings");
            $total = $totalQuery->fetch_assoc()['total'];
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Bookings</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pie Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Booking Status Distribution</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="bookingPieChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <?php foreach(array_combine($statuses, $colors) as $status => $color): ?>
                                <span class="mr-3">
                                        <i class="fas fa-circle" style="color: <?= $color ?>"></i> <?= ucfirst($status) ?>
                                    </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Table -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Status Counts</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= ucfirst($row['status']) ?></td>
                                        <td><?= $row['count'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Pie Chart
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('bookingPieChart').getContext('2d');
        var bookingPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_map('ucfirst', $statuses)) ?>,
                datasets: [{
                    data: <?= json_encode($counts) ?>,
                    backgroundColor: <?= json_encode($colors) ?>,
                    hoverBackgroundColor: <?= json_encode(array_map(function($c) {
                        return ColorLuminance($c, -0.2);
                    }, $colors)) ?>,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = Math.round((value / total) * 100);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    },
                },
                cutout: '70%',
            },
        });

        // Helper function to lighten/darken colors
        function ColorLuminance(hex, lum) {
            hex = String(hex).replace(/[^0-9a-f]/gi, '');
            if (hex.length < 6) {
                hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
            }
            lum = lum || 0;
            var rgb = "#", c, i;
            for (i = 0; i < 3; i++) {
                c = parseInt(hex.substr(i*2,2), 16);
                c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
                rgb += ("00"+c).substr(c.length);
            }
            return rgb;
        }
    });
</script>
</body>
</html>