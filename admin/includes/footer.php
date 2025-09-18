<footer class="admin-footer mt-auto py-3 bg-light">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-muted small">
                &copy; <?= date('Y') ?> BookMySlot Admin Panel. All rights reserved.
            </div>
            <div class="text-end small">
                <span class="d-none d-sm-inline">Version 1.0.0</span>
                <span class="mx-1">|</span>
                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#systemInfoModal">
                    <i class="fas fa-info-circle"></i> System Info
                </a>
            </div>
        </div>
    </div>

    <!-- System Info Modal (optional) -->
    <div class="modal fade" id="systemInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">System Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body small">
                    <div class="mb-2">
                        <strong>PHP Version:</strong> <?= phpversion() ?>
                    </div>
                    <div class="mb-2">
                        <strong>Server:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?>
                    </div>
                    <div>
                        <strong>Database:</strong> MySQL
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Custom Admin Scripts -->
<script src="../public/assets/js/admin.js"></script>

<!-- Initialize tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize other common components as needed
    });
</script>
</body>
</html>