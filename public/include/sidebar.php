<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Sidebar Component -->
<div class="col-lg-2 col-md-3 bg-light p-0 sidebar-container">
    <div class="sidebar p-3 h-100">
        <div class="d-flex flex-column h-100">
            <!-- Brand Logo -->
            <div class="text-center mb-4">
                <img src="../assets/images/logo_seminar_hall.PNG" alt="Book My Slot Logo" height="40" class="me-2">
                <h4 class="mt-2 text-primary">BOOK MY SLOT</h4>
            </div>

            <!-- Quick Links -->
            <div class="mb-4">
                <h5 class="text-uppercase text-primary mb-3">
                    <i class="fas fa-link me-2"></i>Quick Links
                </h5>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="../book_slot.php">
                            <i class="fas fa-calendar-plus me-2"></i>Book a Hall
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="../view_bookings.php">
                            <i class="fas fa-history me-2"></i>My Bookings
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="../schedule.php">
                            <i class="fas fa-calendar-alt me-2"></i>Schedule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../report.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Admin Section (Conditional) -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="mb-4">
                    <h5 class="text-uppercase text-danger mb-3">
                        <i class="fas fa-user-shield me-2"></i>Admin Panel
                    </h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-danger" href="../../admin/manage_users.php">
                                <i class="fas fa-users-cog me-2"></i>Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="../../admin/manage_bookings.php">
                                <i class="fas fa-calendar-check me-2"></i>Manage All Bookings
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- User Profile & Logout -->
            <div class="mt-auto">
                <div class="card bg-light border-0">
                    <div class="card-body text-center">
                        <?php if (isset($_SESSION['name'])): ?>
                            <div class="mb-2">
                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                                <p class="mb-0 mt-2 fw-bold"><?php echo htmlspecialchars($_SESSION['name']); ?></p>
                                <small class="text-muted"><?php echo ucfirst($_SESSION['role']); ?></small>
                            </div>
                        <?php endif; ?>
                        <a href="../../backend/logout.php" class="btn btn-sm btn-outline-danger w-100">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sidebar-container {
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        z-index: 100;
    }

    .sidebar {
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
    }

    .sidebar .nav-link {
        color: #495057;
        border-radius: 5px;
        padding: 8px 12px;
        transition: all 0.3s;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background-color: rgba(18, 60, 105, 0.1);
        color: #123C69;
    }

    .sidebar .nav-link i {
        width: 20px;
        text-align: center;
    }
</style>