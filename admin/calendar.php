<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit;
}
include('includes/header.php');
include('../backend/config/config.php');
?>

<div class="container-fluid admin-calendar-page">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-calendar-alt fa-fw me-2"></i>Booking Calendar
        </h1>
        <div class="d-none d-sm-inline-block">
            <button class="btn btn-primary shadow-sm" id="refreshCalendar">
                <i class="fas fa-sync-alt fa-sm me-1"></i> Refresh
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Event Calendar</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="#" id="viewMonth">Month View</a></li>
                    <li><a class="dropdown-item" href="#" id="viewWeek">Week View</a></li>
                    <li><a class="dropdown-item" href="#" id="viewDay">Day View</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" id="printCalendar">Print</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div id="calendar" class="fc-calendar-container"></div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

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
            events: 'fetch_events.php',
            eventColor: '#123C69',
            eventTextColor: '#fff',
            eventDisplay: 'block',
            editable: true,
            selectable: true,
            eventDidMount: function(info) {
                // Add tooltip to events
                $(info.el).tooltip({
                    title: info.event.extendedProps.description || 'No description',
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });

        calendar.render();

        // View switchers
        document.getElementById('viewMonth').addEventListener('click', function() {
            calendar.changeView('dayGridMonth');
        });
        document.getElementById('viewWeek').addEventListener('click', function() {
            calendar.changeView('timeGridWeek');
        });
        document.getElementById('viewDay').addEventListener('click', function() {
            calendar.changeView('timeGridDay');
        });

        // Refresh button
        document.getElementById('refreshCalendar').addEventListener('click', function() {
            calendar.refetchEvents();
            toastr.success('Calendar refreshed');
        });

        // Print button
        document.getElementById('printCalendar').addEventListener('click', function() {
            window.print();
        });
    });
</script>