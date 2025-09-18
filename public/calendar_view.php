<?php
session_start();
include('../backend/config/config.php');

// Fetch booked slots
$sql = "SELECT id, hall_name, date, start_time, end_time, booking_purpose, booker_name FROM bookings WHERE status='approved'";
$result = $conn->query($sql);

$events = [];
$hallColors = [
    'Auditorium' => '#28a745',
    'Classroom 301' => '#dc3545',
    'Classroom 302' => '#fd7e14',
    'Classroom 401' => '#6f42c1',
    'Classroom 402' => '#20c997'
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['hall_name'] . ' - ' . $row['booking_purpose'],
            'start' => $row['date'] . 'T' . $row['start_time'],
            'end'   => $row['date'] . 'T' . $row['end_time'],
            'color' => $hallColors[$row['hall_name']] ?? '#28a745',
            'extendedProps' => [
                'purpose' => $row['booking_purpose'],
                'hall' => $row['hall_name'],
                'booker' => $row['booker_name']
            ]
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Calendar | Book My Slot</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-container {
            padding: 20px;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            font-size: 0.85em;
            padding: 2px 4px;
        }
        .sidebar {
            background-color: #f8f9fa;
            height: 100vh;
            position: sticky;
            top: 0;
        }
        .content-area {
            padding: 20px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<!-- Navbar -->
<?php include('include/header.php'); ?>

<div class="container-fluid flex-grow-1">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 col-md-3 p-0">
            <div class="sidebar p-3">
                <h4 class="text-primary mb-4"><i class="fas fa-link me-2"></i>Quick Links</h4>
                <ul class="nav flex-column">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="book_slot.php"><i class="fas fa-calendar-plus me-2"></i>Book a Hall</a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link" href="view_bookings.php"><i class="fas fa-history me-2"></i>Booking History</a>
                        </li>

                        <li class="nav-item mb-2">
                            <a class="nav-link active" href="calendar_view.php"><i class="fas fa-calendar me-2"></i>View Calendar</a>
                        </li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item mb-2">
                                <a class="nav-link" href="../admin/manage_users.php"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="../backend/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-10 col-md-9 content-area">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="text-muted">Hall Name</h6>
                    <p id="modalHallName" class="fw-bold"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Date</h6>
                        <p id="modalDate" class="fw-bold"></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Time</h6>
                        <p id="modalTime" class="fw-bold"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Booked By</h6>
                    <p id="modalBooker" class="fw-bold"></p>
                </div>
                <div class="mb-3">
                    <h6 class="text-muted">Purpose</h6>
                    <p id="modalPurpose" class="fw-bold"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include('include/footer.php'); ?>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: <?php echo json_encode($events); ?>,
            eventClick: function(info) {
                var modal = new bootstrap.Modal(document.getElementById('bookingModal'));

                var startDate = new Date(info.event.start);
                var endDate = new Date(info.event.end);

                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                var formattedDate = startDate.toLocaleDateString('en-US', options);

                var startTime = startDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                var endTime = endDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                document.getElementById('modalHallName').textContent = info.event.extendedProps.hall;
                document.getElementById('modalDate').textContent = formattedDate;
                document.getElementById('modalTime').textContent = startTime + ' - ' + endTime;
                document.getElementById('modalBooker').textContent = info.event.extendedProps.booker || 'Not specified';
                document.getElementById('modalPurpose').textContent = info.event.extendedProps.purpose || 'Not specified';

                modal.show();
            },
            eventDisplay: 'block'
        });

        calendar.render();
    });
</script>
</body>
</html>