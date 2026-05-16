<?php
session_start();
include '../dbcon.php'; // Include your database connection file

// Check if user is authenticated
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['status'] = "Please log in to access this page.";
    header("Location: ../index.php");
    exit(0);
}

// Handle Back button action
if (isset($_POST['back-btn'])) {
    unset($_SESSION['booking_date'], $_SESSION['start_time'], $_SESSION['end_time']);
    header("Location: booking.php?id=" . $_POST['studio_id']);
    exit();
}

// Fetch user details
$user = $_SESSION['auth_user'];

// Validate and fetch POST data
if (!isset($_POST['booking_date'], $_POST['start_time'], $_POST['end_time'], $_POST['studio_id'])) {
    $_SESSION['status'] = "Invalid booking details. Please try again.";
    header("Location: ../index.php");
    exit();
}
$booking_date = $_POST['booking_date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$studio_id = $_POST['studio_id'];

// Fetch studio details to calculate price
$studio_query = $con->prepare("SELECT price, name FROM studios WHERE id = ?");
$studio_query->bind_param("i", $studio_id);
$studio_query->execute();
$studio_result = $studio_query->get_result();

if ($studio_row = $studio_result->fetch_assoc()) {
    $price_per_hour = $studio_row['price'];
    $studio_name = $studio_row['name'];

    // Calculate duration in hours and total price
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $duration = $end->diff($start)->h; // Get the duration in hours
    $total_price = ($price_per_hour * $duration) / 2; // Total price (half downpayment)
} else {
    die("Studio details not found.");
}

// Fetch terms and conditions from the database
$terms_query = $con->query("SELECT description FROM terms_conditions LIMIT 1");
$terms = $terms_query->fetch_assoc()['description'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .gcash-qr img { max-width: 100%; height: auto; }
        .btn { display: block; width: 100%; margin: 10px 0; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-primary { background-color: #4CAF50; color: white; }
        .btn-danger { background-color: #f44336; color: white; }
        #termsModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); }
        #termsModal .modal-content { background: white; margin: 10% auto; padding: 20px; width: 80%; border-radius: 8px; }
    </style>
    <script>
        // Show and close Terms and Conditions popup
        function showTerms() {
            document.getElementById('termsModal').style.display = 'block';
        }
        function closeTerms() {
            document.getElementById('termsModal').style.display = 'none';
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Booking Confirmation</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
    <p><strong>Studio:</strong> <?= htmlspecialchars($studio_name); ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($booking_date); ?></p>
    <p><strong>Time:</strong> <?= htmlspecialchars($start_time . ' - ' . $end_time); ?></p>
    <p><strong>Total Downpayment Price:</strong> <?= number_format($total_price, 2); ?> PHP</p>

    <div class="gcash-qr">
        <h3>Payment Instructions</h3>
        <p>Scan the QR code below to send your payment via GCash:</p>
        <img src="gcash_qr_code.png" alt="GCash QR Code">
    </div>

    <form id="paymentForm" action="process_booking.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="studio_id" value="<?= htmlspecialchars($studio_id); ?>">
        <input type="hidden" name="booking_date" value="<?= htmlspecialchars($booking_date); ?>">
        <input type="hidden" name="start_time" value="<?= htmlspecialchars($start_time); ?>">
        <input type="hidden" name="end_time" value="<?= htmlspecialchars($end_time); ?>">
        <input type="hidden" name="total_price" value="<?= $total_price; ?>">

        <label for="receipt">Upload GCash Receipt:</label>
        <input type="file" name="gcash_receipt" accept="image/*" required>

        <!-- Terms Agreement -->
        <div>
            <input type="checkbox" id="agreeTerms" required>
            <label for="agreeTerms">I agree to the <a href="javascript:void(0);" onclick="showTerms()">Terms and Conditions</a></label>
        </div>

        <button type="submit" class="btn btn-primary">Submit Payment</button>
    </form>

    <form method="POST">
        <input type="hidden" name="studio_id" value="<?= htmlspecialchars($studio_id); ?>">
        <button type="submit" name="back-btn" class="btn btn-danger">Back</button>
    </form>
</div>

<!-- Terms and Conditions Modal -->
<div id="termsModal">
    <div class="modal-content">
        <h3>Terms and Conditions</h3>
        <p><?= nl2br(htmlspecialchars($terms)); ?></p>
        <button onclick="closeTerms()" class="btn btn-danger">Close</button>
    </div>
</div>
</body>
</html>
