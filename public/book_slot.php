<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Slot | Book My Slot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                        <a class="nav-link active" href="book_slot.php"><i class="fas fa-calendar-plus me-2"></i>Book a Hall</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link" href="view_bookings.php"><i class="fas fa-history me-2"></i>Booking History</a>
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
                <h2 class="h3 text-primary"><i class="fas fa-calendar-plus me-2"></i>Book a Slot</h2>
                <a href="view_bookings.php" class="btn btn-outline-info">
                    <i class="fas fa-history me-2"></i>View Your Bookings
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="../backend/book_slot.php" method="POST" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <label for="booker_name" class="form-label">Your Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="booker_name" name="booker_name"
                                           value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>"
                                           placeholder="Enter your full name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="booker_email" class="form-label">Your Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="booker_email" name="booker_email"
                                           value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>"
                                           placeholder="Enter your email" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="booker_phone" class="form-label">Your Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" id="booker_phone" name="booker_phone"
                                           placeholder="Enter your phone number" pattern="[0-9]{10}" required>
                                </div>
                                <div class="form-text">10-digit number without spaces or dashes</div>
                            </div>

                            <div class="col-md-6">
                                <label for="booking_purpose" class="form-label">Purpose of Booking</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                    <input type="text" class="form-control" id="booking_purpose" name="booking_purpose"
                                           placeholder="Seminar, Meeting, Event, etc." required>
                                </div>
                            </div>

                            <!-- Room Selection -->
                            <div class="col-md-6">
                                <label for="num_students" class="form-label">Number of Attendees</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <input type="number" class="form-control" id="num_students" name="capacity"
                                           min="1" placeholder="Enter number of attendees" required>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="checkRoom">
                                    <i class="fas fa-lightbulb me-1"></i>Get AI Room Suggestion
                                </button>
                                <div id="room_result" class="text-info small mt-2"></div>
                            </div>

                            <div class="col-md-6">
                                <label for="hall_name" class="form-label">Hall Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <select class="form-select" id="hall_name" name="hall_name" required>
                                        <option value="" selected disabled>Select a hall...</option>
                                        <option value="Auditorium">Auditorium (Capacity: 200)</option>
                                        <option value="Classroom 301">Classroom 301 (Capacity: 50)</option>
                                        <option value="Classroom 302">Classroom 302 (Capacity: 70)</option>
                                        <option value="Classroom 401">Classroom 401 (Capacity: 100)</option>
                                        <option value="Classroom 402">Classroom 402 (Capacity: 20)</option>
                                        <option value="Seminar hall">Seminar hall  (Capacity: 150)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Date and Time -->
                            <div class="col-md-4">
                                <label for="date" class="form-label">Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                    <input type="text" class="form-control flatpickr-date" id="date" name="date"
                                           placeholder="Select date" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="start_time" class="form-label">Start Time</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="text" class="form-control flatpickr-time" id="start_time" name="start_time"
                                           placeholder="Select start time" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="end_time" class="form-label">End Time</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="text" class="form-control flatpickr-time" id="end_time" name="end_time"
                                           placeholder="Select end time" required>
                                </div>
                            </div>

                            <!-- Requirements -->
                            <div class="col-md-6">
                                <label for="chairs" class="form-label">Number of Chairs</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-chair"></i></span>
                                    <input type="number" class="form-control" id="chairs" name="chairs"
                                           min="1" placeholder="Enter required chairs" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="requirements" class="form-label">Additional Requirements</label>
                                <textarea class="form-control" id="requirements" name="requirements"
                                          rows="3" placeholder="Projector, Whiteboard, Microphone, etc."></textarea>
                                <div class="form-text">Separate items with commas</div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-4">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-calendar-check me-2"></i>Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/main.js"></script>

<script>
    // Initialize Flatpickr
    document.addEventListener('DOMContentLoaded', function() {
        // Date picker
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            minDate: "today",
            maxDate: new Date().fp_incr(60) // 60 days from now
        });

        // Time picker
        flatpickr(".flatpickr-time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });

        // Form validation
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    });

    // AI Room Suggestion
    document.getElementById("checkRoom").addEventListener("click", function() {
        var numStudents = document.getElementById("num_students").value;

        if (!numStudents || numStudents <= 0) {
            alert("Please enter a valid number of students.");
            return;
        }

        document.getElementById("room_result").innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Analyzing requirements...';

        fetch("http://127.0.0.1:5000/recommend", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ students: parseInt(numStudents) })
        })
            .then(response => response.json())
            .then(data => {
                if (data.recommended_room) {
                    document.getElementById("room_result").innerHTML =
                        '<i class="fas fa-lightbulb text-warning me-2"></i>Recommended Room: <strong>' +
                        data.recommended_room + '</strong>';
                    document.getElementById("hall_name").value = data.recommended_room.split(" (")[0];
                } else {
                    document.getElementById("room_result").innerHTML =
                        '<i class="fas fa-exclamation-triangle text-danger me-2"></i>' +
                        (data.error || "AI couldn't process the request.");
                }
            })
            .catch(error => {
                document.getElementById("room_result").innerHTML =
                    '<i class="fas fa-exclamation-triangle text-danger me-2"></i>Error: AI service unavailable';
                console.error("Request failed:", error);
            });
    });
</script>

<?php include('include/footer.php'); ?>
</body>
</html>