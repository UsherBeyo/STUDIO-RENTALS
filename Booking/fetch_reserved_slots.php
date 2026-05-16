<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get studio_id and date
$studio_id = isset($_GET['studio_id']) ? intval($_GET['studio_id']) : 0;
$booking_date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$studio_id || !$booking_date) {
    echo json_encode(['error' => 'Missing studio ID or booking date']);
    exit();
}

// Query to fetch reserved slots
$query = "SELECT start_time, end_time FROM bookings WHERE studio_id = ? AND booking_date = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'SQL error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("is", $studio_id, $booking_date);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = [
        'start_time' => (int)date('G', strtotime($row['start_time'])), // Hour in 24h format
        'end_time' => (int)date('G', strtotime($row['end_time']))
    ];
}

// Always return a JSON response
if (empty($slots)) {
    // Send a response indicating no reservations (still valid JSON)
    echo json_encode([]);
} else {
    echo json_encode($slots);
}

$stmt->close();
$conn->close();
?>
