<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');

if ($conn->connect_error) {
    echo json_encode(['conflict' => false, 'error' => 'Database connection failed.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$studio_id = $data['studio_id'];
$booking_date = $data['date'];
$start_time = $data['start_time'];
$end_time = $data['end_time'];

// Validate inputs
if (empty($studio_id) || empty($booking_date) || empty($start_time) || empty($end_time)) {
    echo json_encode(['conflict' => false, 'error' => 'Invalid input data.']);
    exit();
}

// Check for conflicts in bookings and pending_bookings
$query = "SELECT * FROM (
    SELECT studio_id, booking_date, start_time, end_time FROM bookings
    UNION ALL
    SELECT studio_id, booking_date, start_time, end_time FROM pending_bookings
) AS combined
WHERE studio_id = ? AND booking_date = ? AND (start_time < ? AND end_time > ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("isss", $studio_id, $booking_date, $end_time, $start_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['conflict' => true]);
} else {
    echo json_encode(['conflict' => false]);
}

$conn->close();
?>
