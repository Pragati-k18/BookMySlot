<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
include('../backend/config/config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | BookMySlot Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="admin-users-page">
<!-- Reusing the same sidebar structure from dashboard -->
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
                <a class="nav-link active" href="manage_users.php">
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
                <i class="fas fa-user-cog me-2"></i>User Management
            </h1>
            <div class="admin-welcome">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_email']); ?></strong>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-table me-2"></i>All Users
                </h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-1"></i> Add User
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="userTable">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM users");
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='user-{$row['id']}'>
                                            <td>{$row['id']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['email']}</td>
                                            <td><span class='badge bg-".getRoleColor($row['role'])."'>".ucfirst($row['role'])."</span></td>
                                            <td>
                                                <button class='btn btn-sm btn-outline-danger delete-btn' data-id='{$row['id']}'>
                                                    <i class='fas fa-trash-alt me-1'></i> Delete
                                                </button>
                                            </td>
                                        </tr>";
                        }

                        function getRoleColor($role) {
                            switch(strtolower($role)) {
                                case 'admin': return 'danger';
                                case 'user': return 'primary';
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

<!-- Add User Modal (Placeholder - you'll need to implement this) -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>User creation form would go here.</p>
                <!-- You would implement a proper form here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save User</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function () {
        // Handle delete button click
        $(".delete-btn").on("click", function () {
            const userId = $(this).data("id");
            const $row = $(this).closest('tr');

            if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
                $.ajax({
                    url: "delete_users.php",
                    type: "POST",
                    data: { id: userId },
                    success: function (response) {
                        if (response.trim() === "success") {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                // You could add a toast notification here
                            });
                        } else {
                            alert("Error deleting user: " + response);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert("An error occurred. Please try again.");
                        console.error("AJAX error:", status, error);
                    }
                });
            }
        });
    });
</script>
</body>
</html>