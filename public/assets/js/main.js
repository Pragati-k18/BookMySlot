document.addEventListener('DOMContentLoaded', function () {
    console.log("Page loaded successfully");

    // Login Form Validation
    const loginForm = document.querySelector('form#login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (email === '' || password === '') {
                alert("Please fill in both email and password.");
                event.preventDefault();
            }
        });
    }


    // Register Form Validation
    const registerForm = document.querySelector('form#register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (name === '' || email === '' || password === '') {
                alert("All fields are required.");
                event.preventDefault();
            }
        });
    }

    // Book Slot Form Validation
    const bookForm = document.querySelector('form#book-slot-form');
    if (bookForm) {
        bookForm.addEventListener('submit', function (event) {
            const hallName = document.getElementById('hall_name').value;
            const date = document.getElementById('date').value;
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (!hallName || !date || !startTime || !endTime) {
                alert("Please fill all fields.");
                event.preventDefault();
            }
        });
    }




    // Admin Booking History AJAX Example
    const bookingHistoryLink = document.getElementById('view-booking-history');
    if (bookingHistoryLink) {
        bookingHistoryLink.addEventListener('click', function (event) {
            event.preventDefault();
            fetch('fetch_booking_history.php')
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error('Error fetching booking history:', error));
        });
    }

    // FullCalendar Initialization
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: 'fetch_events.php',
            eventColor: '#123C69',
            eventTextColor: '#fff'
        });
        calendar.render();
    }

    // Custom Calendar Example
    if (calendarEl && !calendarEl.classList.contains('full-calendar')) {
        const currentMonth = new Date();
        const bookings = [
            { date: '2024-12-08', timeSlots: ['9:00 AM - 11:00 AM', '2:00 PM - 4:00 PM'] },
            { date: '2024-12-09', timeSlots: ['11:30 AM - 1:30 PM'] }
        ];

        function generateCalendar(month, year) {
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const calendarHtml = [];

            for (let i = 1; i <= daysInMonth; i++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                const isBooked = bookings.some(booking => booking.date === dateStr);
                const slots = isBooked ? getBookedSlots(dateStr) : ['Available'];

                calendarHtml.push(`
                    <div class="day">
                        <span class="date">${i}</span>
                        <ul class="time-slots">
                            ${slots.map(slot => `<li class="${slot === 'Available' ? 'available' : 'booked'}">${slot}</li>`).join('')}
                        </ul>
                    </div>
                `);
            }

            calendarEl.innerHTML = calendarHtml.join('');
        }

        function getBookedSlots(date) {
            const booking = bookings.find(b => b.date === date);
            return booking ? booking.timeSlots : [];
        }

        generateCalendar(currentMonth.getMonth(), currentMonth.getFullYear());
    }

    // Dynamic Header, Footer, and Sidebar Loading
    ['header', 'footer', 'sidebar'].forEach(section => {
        const sectionElement = document.getElementById(section);
        if (sectionElement) {
            fetch(`${section}.php`)
                .then(response => response.text())
                .then(data => sectionElement.innerHTML = data)
                .catch(error => console.error(`Error loading ${section}:`, error));
        }
    });
});
