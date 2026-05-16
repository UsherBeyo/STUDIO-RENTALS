<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user'])) {
    die("You are not logged in. Please log in again.");
}

$name = $_SESSION['auth_user']['first_name'] . ' ' . $_SESSION['auth_user']['last_name'];
$email = $_SESSION['auth_user']['email'];

if (isset($_GET['id'])) {
    $studio_id = $_GET['id'];

    // Fetch studio details
    $query = "SELECT * FROM studios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $studio = $result->fetch_assoc();
} else {
    die('Error: Studio ID not provided.');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Calendar</title>
    <link href="bookingstyles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<body>

<h2>Booking Calendar for <?php echo htmlspecialchars($studio['name']); ?></h2>
<a href="../studfinal.php" class="btn" style="background-color: #4CAF50; color: white; padding: 10px; border-radius: 5px; text-decoration: none;">← Back</a>

<div id="calendar"></div>

<!-- Popup for Selecting Time Slots -->
<div id="popupContainer" class="popup-container" style="display: none;">
    <div class="popup-content">
        <h3>Select Time Slot</h3>
        
        <!-- Timeline Display -->
        <div id="timeline">
            <h4 id="timeline-date">Loading timeline...</h4>
            <div id="time-slots" style="max-height: 200px; overflow-y: auto;"></div>
        </div>

        <!-- Dropdowns for Start/End Time -->
        <label for="startTime">Start Time:</label>
        <select id="startTime" class="time-dropdown"></select>

        <label for="endTime">End Time:</label>
        <select id="endTime" class="time-dropdown"></select>

        <button id="bookNowBtn" class="btn">Book Now</button>
        <button onclick="closePopup()" class="btn cancel-btn">Cancel</button>
    </div>
</div>

<div id="conflictPopup" class="popup-container" style="display: none;">
    <div class="popup-content">
        <h3>Selected Time Conflicts</h3>
        <p>Some or all parts of your selected time are already reserved. Please choose an option:</p>
        <p>
            <strong>Date:</strong> <span id="waiting-date"></span><br>
            <strong>Start Time:</strong> <span id="waiting-start"></span><br>
            <strong>End Time:</strong> <span id="waiting-end"></span>
        </p>
        <button onclick="showWaitingRoom()" class="btn">Waiting Room</button>
        <button onclick="closeConflictPopup()" class="btn cancel-btn">Schedule Another Time</button>
    </div>
</div>

<!-- Waiting Room Rules Popup -->
<div id="waitingRoomPopup" class="popup-container" style="display: none;">
    <div class="popup-content">
        <h3>Waiting Room Rules</h3>
        <p id="waiting-room-rules">Loading...</p>
        <button onclick="proceedToWaitingRoom()" class="btn">Proceed to Waiting Room</button>
        <button onclick="closeWaitingRoomPopup()" class="btn cancel-btn">Schedule Another Time</button>
    </div>
</div>

<form id="waitingRoomForm" action="add_to_waiting_room.php" method="POST">
    <input type="hidden" name="studio_id" value="<?php echo $studio_id; ?>">
    <input type="hidden" id="waiting_name" name="name" value="<?php echo htmlspecialchars($name); ?>">
    <input type="hidden" id="waiting_email" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <input type="hidden" id="waiting_date" name="booking_date">
    <input type="hidden" id="waiting_start_time" name="start_time">
    <input type="hidden" id="waiting_end_time" name="end_time">
</form>

<script>
// Initialize FullCalendar
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        dateClick: function(info) {
            const selectedDate = new Date(info.dateStr);
            const today = new Date();
            const maxDate = new Date();
            maxDate.setDate(today.getDate() + 30);

            if (selectedDate < today) {
                alert('This date has already passed and is no longer available.');
                return;
            }
            if (selectedDate > maxDate) {
                alert('The selected date is too far ahead. Please select a date within the next 30 days.');
                return;
            }

            fetchTimeline(info.dateStr);
        }
    });
    calendar.render();
});

function fetchTimeline(date) {
    const studioId = <?php echo json_encode($studio_id); ?>;
    const popupContainer = document.getElementById('popupContainer');
    const timeSlotsDiv = document.getElementById('time-slots');
    const bookNowBtn = document.getElementById('bookNowBtn');
    const startTimeDropdown = document.getElementById('startTime');
    const endTimeDropdown = document.getElementById('endTime');

    popupContainer.style.display = 'flex';
    timeSlotsDiv.innerHTML = 'Loading...';

    fetch(`fetch_reserved_slots.php?studio_id=${studioId}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            timeSlotsDiv.innerHTML = '';
            for (let hour = 10; hour < 22; hour++) {
                const isReserved = data.some(slot => slot.start_time <= hour && slot.end_time > hour);
                const slotDiv = document.createElement('div');
                slotDiv.className = isReserved ? 'reserved' : '';
                slotDiv.textContent = `${hour}:00 - ${hour + 1}:00`;
                timeSlotsDiv.appendChild(slotDiv);
            }
            generateTimeOptions();
            bookNowBtn.onclick = () => handleBookNow(studioId, date);
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to fetch timeline.');
        });
}

function generateTimeOptions() {
    const startTimeDropdown = document.getElementById('startTime');
    const endTimeDropdown = document.getElementById('endTime');
    startTimeDropdown.innerHTML = '';
    endTimeDropdown.innerHTML = '';

    for (let hour = 10; hour < 22; hour++) {
        const optionStart = document.createElement('option');
        optionStart.value = `${hour}:00`;
        optionStart.textContent = `${hour}:00`;
        startTimeDropdown.appendChild(optionStart);

        const optionEnd = document.createElement('option');
        optionEnd.value = `${hour + 1}:00`;
        optionEnd.textContent = `${hour + 1}:00`;
        endTimeDropdown.appendChild(optionEnd);
    }
}

function handleBookNow(studioId, date) {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;

    if (startTime >= endTime) {
        alert('End time must be later than start time.');
        return;
    }

    // Check for conflicts before proceeding
    fetch('check_conflicts.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            studio_id: studioId,
            date: date,
            start_time: startTime,
            end_time: endTime
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.conflict) {
                // Show the conflict popup if a conflict exists
                showConflictPopup(date, startTime, endTime);
            } else {
                // Proceed to booking confirmation if no conflict
                proceedToBookingConfirmation(studioId, date, startTime, endTime);
            }
        })
        .catch(err => {
            console.error('Error:', err);
        });
}

function proceedToBookingConfirmation(studioId, date, startTime, endTime) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'booking_confirmation.php';

    const inputs = [
        { name: 'studio_id', value: studioId },
        { name: 'booking_date', value: date },
        { name: 'start_time', value: startTime },
        { name: 'end_time', value: endTime }
    ];

    inputs.forEach(inputData => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = inputData.name;
        input.value = inputData.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}


function showConflictPopup(date, startTime, endTime) {
    // Set the values for date, start time, and end time
    document.getElementById('waiting-date').textContent = date;
    document.getElementById('waiting-start').textContent = startTime;
    document.getElementById('waiting-end').textContent = endTime;

    // Hide the booking popup and show conflict popup
    document.getElementById('popupContainer').style.display = 'none';
    document.getElementById('conflictPopup').style.display = 'flex';
}


function closePopup() {
    document.getElementById('popupContainer').style.display = 'none';
}

function showWaitingRoom() {
    document.getElementById('conflictPopup').style.display = 'none';
    document.getElementById('waitingRoomPopup').style.display = 'flex';

    fetch('fetch_waiting_room_rules.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('waiting-room-rules').innerHTML = data;
        })
        .catch(() => {
            document.getElementById('waiting-room-rules').innerHTML = 'Failed to load rules.';
        });
}

function proceedToWaitingRoom() {
    // Fill the form fields with selected date and time
    document.getElementById('waiting_date').value = document.getElementById('waiting-date').textContent;
    document.getElementById('waiting_start_time').value = document.getElementById('waiting-start').textContent;
    document.getElementById('waiting_end_time').value = document.getElementById('waiting-end').textContent;

    // Submit the form to add_to_waiting_room.php
    document.getElementById('waitingRoomForm').submit();
}


function closeConflictPopup() {
    document.getElementById('conflictPopup').style.display = 'none';
    document.getElementById('popupContainer').style.display = 'flex';
}

function closeWaitingRoomPopup() {
    document.getElementById('waitingRoomPopup').style.display = 'none';
    document.getElementById('popupContainer').style.display = 'flex';
}

function generateTimeOptions() {
    const startTimeDropdown = document.getElementById('startTime');
    const endTimeDropdown = document.getElementById('endTime');
    startTimeDropdown.innerHTML = '';
    endTimeDropdown.innerHTML = '';

    for (let hour = 10; hour < 22; hour++) {
        const optionStart = document.createElement('option');
        optionStart.value = `${hour}:00`;
        optionStart.textContent = `${hour}:00`;
        startTimeDropdown.appendChild(optionStart);

        const optionEnd = document.createElement('option');
        optionEnd.value = `${hour + 1}:00`;
        optionEnd.textContent = `${hour + 1}:00`;
        endTimeDropdown.appendChild(optionEnd);
    }
}
</script>
</body>
</html>
