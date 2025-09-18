<?php //include('include/header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Book My Slot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Consistent Navbar -->
<?php include('include/header.php'); ?>

<main class="flex-grow-1 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo_seminar_hall.PNG" alt="Logo" width="80" class="mb-3">
                            <h2 class="h4 fw-bold">Create Your Account</h2>
                            <p class="text-muted">Join us to book seminar halls and facilities</p>
                        </div>

                        <form action="../backend/register_user.php" method="POST">
                            <div class="row g-3">
                                <!-- Name Field -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="name" name="name"
                                               placeholder="Enter your full name" required>
                                        <label for="name"><i class="fas fa-user me-2"></i>Full Name</label>
                                    </div>
                                </div>

                                <!-- Email Field -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email"
                                               placeholder="Enter your email" required>
                                        <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                    </div>
                                </div>

                                <!-- Password Field -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" id="password" name="password"
                                               placeholder="Create a password" required>
                                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                                    </div>
                                    <div class="form-text">Use 8 or more characters with a mix of letters, numbers & symbols</div>
                                </div>

                                <!-- Role Selection -->
                                <div class="col-12">
                                    <div class="form-floating">
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="student">Student</option>
                                            <option value="teacher">Teacher</option>
                                            <option value="staff">Office Staff</option>
                                        </select>
                                        <label for="role"><i class="fas fa-user-tag me-2"></i>Select Your Role</label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 mt-4">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-user-plus me-2"></i>Register Now
                                        </button>
                                    </div>
                                </div>

                                <!-- Login Link -->
                                <div class="col-12 text-center mt-3">
                                    <p class="mb-0">Already have an account?
                                        <a href="login.php" class="text-decoration-none">Login here</a>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/main.js" defer></script>

<?php include('include/footer.php'); ?>
</body>
</html>