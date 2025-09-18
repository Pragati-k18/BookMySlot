<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>BookMySlot Admin</title>

    <!-- Favicon -->
    <link rel="icon" href="../public/assets/images/favicon.ico" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css" rel="stylesheet">

    <!-- DataTables CSS (optional) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="../css/admin.css">

    <!-- Meta Tags -->
    <meta name="description" content="BookMySlot Administration Panel">
    <meta name="author" content="Your Company Name">

    <!-- Preload Resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" as="script">
</head>
<body class="admin-panel">
<!-- Top Navigation Bar -->
<header class="admin-header navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container-fluid">
        <!-- Sidebar Toggle Button -->
        <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Brand Logo -->
        <a class="navbar-brand fw-bold" href="admin_dashboard.php">
            <i class="fas fa-calendar-check me-2"></i>BookMySlot
        </a>

        <!-- Top Navigation -->
        <div class="d-flex align-items-center">
            <!-- Notification Dropdown -->
            <div class="dropdown me-3">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><a class="dropdown-item" href="#">New booking request</a></li>
                    <li><a class="dropdown-item" href="#">System update available</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">View all</a></li>
                </ul>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i>
                    <span class="d-none d-lg-inline"><?php echo $_SESSION['admin_email'] ?? 'Admin'; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Signed in as Admin</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>