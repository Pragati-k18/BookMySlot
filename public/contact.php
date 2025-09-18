<?php //include('include/header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Book My Slot</title>
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

<main class="flex-grow-1 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="text-center text-primary mb-5"><i class="fas fa-envelope-open-text me-2"></i>Contact Us</h1>

                        <div class="row g-4">
                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h2 class="h4 text-primary mb-4"><i class="fas fa-info-circle me-2"></i>About Us</h2>
                                        <div class="contact-info">
                                            <div class="d-flex mb-3">
                                                <div class="me-3 text-primary">
                                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-1">Address</h3>
                                                    <p class="mb-0">131, Mayur Colony, Kothrud, Pune 411 038 MH INDIA</p>
                                                </div>
                                            </div>

                                            <div class="d-flex mb-3">
                                                <div class="me-3 text-primary">
                                                    <i class="fas fa-phone fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-1">Phone</h3>
                                                    <p class="mb-0">020-2546 6271, 2546 6273</p>
                                                </div>
                                            </div>

                                            <div class="d-flex">
                                                <div class="me-3 text-primary">
                                                    <i class="fas fa-envelope fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h3 class="h6 mb-1">Email</h3>
                                                    <p class="mb-0">
                                                        <a href="mailto:info.imcc@mespune.in" class="text-decoration-none">info.imcc@mespune.in</a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-4 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h2 class="h4 text-primary mb-4"><i class="fas fa-lightbulb me-2"></i>Contribution</h2>
                                        <p>We value your contributions and feedback to help us grow. Please reach out using the form or contact us directly.</p>
                                        <div class="social-links mt-4">
                                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-facebook-f"></i></a>
                                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-twitter"></i></a>
                                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-linkedin-in"></i></a>
                                            <a href="#" class="btn btn-outline-primary"><i class="fab fa-instagram"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Form -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h2 class="h4 text-primary mb-4"><i class="fas fa-paper-plane me-2"></i>Get in Touch</h2>
                                        <form action="../backend/contact_process.php" method="POST" class="needs-validation" novalidate>
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Your Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="email" class="form-label">Your Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="message" class="form-label">Your Message</label>
                                                <textarea class="form-control" id="message" name="message" rows="6" placeholder="Write your message here" required></textarea>
                                            </div>

                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
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

<script>
    // Form validation
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

<?php include('include/footer.php'); ?>
</body>
</html>