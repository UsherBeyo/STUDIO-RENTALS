<?php
session_start();
include '../dbcon.php'; // Database connection

if (!isset($_SESSION['authenticated'])) {
    header("Location: ../index.php");
    exit();
}
// Fetch user data
$user_id = $_SESSION['auth_user']['id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Ensure directory exists
    }

    $fileName = basename($_FILES['profile_picture']['name']);
    $targetPath = $uploadDir . time() . '_' . $fileName; // Unique file name

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetPath)) {
        $updateQuery = "UPDATE users SET profile_picture = ? WHERE id = ?";
        $stmt = $con->prepare($updateQuery);
        $stmt->bind_param("si", $targetPath, $user_id);
        $stmt->execute();

        $_SESSION['status'] = "Profile picture updated successfully.";
        header("Location: user_profile.php");
        exit();
    } else {
        $_SESSION['status'] = "Failed to upload profile picture.";
    }
}

// Function to fetch past, pending, and upcoming reservations
function fetchReservations($con, $email, $type) {
    $currentTimestamp = date('Y-m-d H:i:s');
    $data = [];
    if ($type == 'past') {
        $query = "SELECT * FROM bookings WHERE email = ? AND CONCAT(booking_date, ' ', start_time) < ?";
    } elseif ($type == 'pending') {
        $query = "SELECT * FROM pending_bookings WHERE email = ?";
    } elseif ($type == 'upcoming') {
        $query = "SELECT * FROM bookings WHERE email = ? AND CONCAT(booking_date, ' ', start_time) > ?";
    }

    $stmt = $con->prepare($query);
    if ($type == 'pending') {
        $stmt->bind_param("s", $email);
    } else {
        $stmt->bind_param("ss", $email, $currentTimestamp);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

$pastReservations = fetchReservations($con, $user['email'], 'past');
$pendingReservations = fetchReservations($con, $user['email'], 'pending');
$upcomingReservations = fetchReservations($con, $user['email'], 'upcoming');
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="../logo.png" type="image/x-icon"/>
        <title>K2 Band Rehearsal Studios</title>

        <!-- CSS and Font Awesome links -->
        <link rel="stylesheet" type="text/css" href="../allstyle.css">
        <link rel="stylesheet" type="text/css" href="profile.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/boxicons/2.0.7/css/boxicons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">

        <!-- Login form CSS -->
        <link rel="stylesheet" href="../LoginStyle.css">

        <!-- JavaScript for menu toggle and logout confirmation -->
        <script>
            // Toggle navigation menu for mobile view
            document.addEventListener("DOMContentLoaded", function() {
                const menuIcon = document.getElementById('menu-icon');
                if (menuIcon) {
                    menuIcon.addEventListener('click', function() {
                        const navlist = document.querySelector('.navlist');
                        navlist.classList.toggle('active');
                    });
                }
            });

            // Confirm logout action
            function confirmLogout() {
                const confirmed = confirm("Are you sure you want to log out?");
                if (confirmed) {
                    window.location.href = "../logout.php"; // Redirects to logout if confirmed
                }
            }
        </script>
    </head>

    <body>
        <!-- Popup Message -->
        <div class="popup-message" id="popupMessage">
            <span id="popupText"></span>
            <button onclick="closePopupNotif()">✖</button>
        </div>

        <header>
            <a href="../index.php">
                <img src="../logo.png" alt="Description of the image" class="logo">
            </a>
            <ul class="navlist">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../studfinal.php">Studio</a></li>
                <li><a href="../contsfinal.php">Contacts</a></li>
                <li><a href="../abtfinal.php">About Us</a></li>

                <!-- Check if the user is logged in -->
                <?php if (isset($_SESSION['authenticated'])): ?>
                    <li><a class="profile-btn" href="user_profile.php">Profile</a></li>
                    <li><a href="javascript:void(0);" onclick="confirmLogout()">Logout</a></li>
                    <li></li>
                <?php else: ?>
                    <div class="login-container">
                        <input type="checkbox" id="show" class="show-checkbox">
                        <label for="show" class="show">Login</label>
                        <div class="wrapper">
                            <label class="close-btn" for="show"><i class="fas fa-times"></i></label>
                            <form action="login.php" method="POST">
                                <h1>Login</h1>
                                <div class="input-box">
                                    <input type="text" name="username" placeholder="Username" required>
                                    <i class="bx bxs-user-circle"></i>
                                </div>
                                <div class="input-box">
                                    <input type="password" name="password" placeholder="Password" required>
                                    <i class="bx bxs-lock-alt"></i>
                                </div>
                                <div class="remember-forgot">
                                    <label>
                                        <input type="checkbox" id="remember"> Remember me
                                    </label>
                                    <a href="#">Forgot password?</a>
                                </div>
                                <button type="submit" name="login-btn" class="btn">Login</button>
                                <div class="register-link">
                                    <p>Don't have an account? <a href="Registration/Registration.php">Register</a></p> 
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </ul>
        </header>
                    
        <div class="profile-container">
        <div class="profile-header">
            <div class="profile-pic">
                <img src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'profile-placeholder.jpg'; ?>" alt="Profile Picture" id="profileImage" width="150">
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="profile_picture" accept="image/*" required>
                    <button type="submit" class="btn">Upload Picture</button>
                </form>
            </div>
            <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
        </div>

        <div class="profile-info">
            <h3>Profile Information</h3>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Password: </strong> 
                    <span id="password" class="hidden-password"><?php echo htmlspecialchars($user['password']); ?></span>
                    <i class="fas fa-eye" id="togglePassword" onclick="togglePasswordVisibility()"></i>
                </p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            
        </div>

        <div class="profile-actions">
            <button class="btn" onclick="showPopup('past')">View Past Reservations</button>
            <button class="btn" onclick="showPopup('pending')">View Pending Reservations</button>
            <button class="btn" onclick="showPopup('upcoming')">View Upcoming Confirmed Reservations</button>
            <button class="btn" onclick="showEditPopup()">Edit Profile</button>
        </div>
    </div>

    <!-- Popup Windows -->
    <div class="overlay" id="overlay" onclick="closePopup()"></div>
    <div class="popup" id="pastPopup">
        <h3>Past Reservations</h3>
        <ul>
            <?php foreach ($pastReservations as $reservation): ?>
                <li>
                    Date: <?php echo $reservation['booking_date']; ?>,
                    Time: <?php echo $reservation['start_time'] . ' - ' . $reservation['end_time']; ?>,
                    <a href="<?php echo '../Booking/' . htmlspecialchars($reservation['receipt_path']); ?>" target="_blank">View Receipt</a>

                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="popup" id="pendingPopup">
        <h3>Pending Reservations</h3>
        <ul>
            <?php foreach ($pendingReservations as $reservation): ?>
                <li>
                    Date: <?php echo $reservation['booking_date']; ?>,
                    Time: <?php echo $reservation['start_time'] . ' - ' . $reservation['end_time']; ?>,
                    <a href="<?php echo '../Booking/' . htmlspecialchars($reservation['receipt_path']); ?>" target="_blank">View Receipt</a>
                    <button class="btn" onclick="cancelPending(<?php echo $reservation['id']; ?>)">Cancel</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="popup" id="upcomingPopup">
        <h3>Upcoming Confirmed Reservations</h3>
        <ul>
            <?php foreach ($upcomingReservations as $reservation): ?>
                <li>
                    Date: <?php echo $reservation['booking_date']; ?>,
                    Time: <?php echo $reservation['start_time'] . ' - ' . $reservation['end_time']; ?>,
                    <a href="<?php echo '../Booking/' . htmlspecialchars($reservation['receipt_path']); ?>" target="_blank">View Receipt</a>
                    <?php 
                    $current_time = new DateTime();
                    $reservation_time = new DateTime($reservation['booking_date'] . ' ' . $reservation['start_time']);
                    $interval = $current_time->diff($reservation_time);
                    if ($interval->days >= 1): ?>
                        <button class="btn" onclick="cancelUpcoming(<?php echo $reservation['booking_id']; ?>)">Cancel</button>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    </div>

    <div class="overlay" id="editOverlay" onclick="closeEditPopup()"></div>
        <div class="popup" id="editPopup">
            <h3>Edit Profile</h3>
            <form method="POST" action="edit_profile.php">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter new password (optional)">
                
                <button type="submit" class="btn">Update Profile</button>
                <button type="button" class="btn cancel-btn" onclick="closeEditPopup()">Cancel</button>
            </form>
        </div>

    <script>
    // Store the actual password in a JavaScript variable
    const actualPassword = "<?php echo addslashes($user['password']); ?>";

    // Password toggle logic
    function togglePasswordVisibility() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');

        if (passwordField.textContent === "********") {
            // Show the actual password
            passwordField.textContent = actualPassword;
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            // Hide the password
            passwordField.textContent = "********";
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    function showPopup(type) {
        document.getElementById('overlay').classList.add('active');
        document.getElementById(type + 'Popup').classList.add('active');
    }
    function closePopup() {
        document.getElementById('overlay').classList.remove('active');
        document.querySelectorAll('.popup').forEach(popup => popup.classList.remove('active'));
    }

    function cancelPending(id) {
        if (confirm('Are you sure you want to cancel this pending reservation?')) {
            window.location.href = 'cancel_pending.php?id=' + id;
        }
    }

    function cancelUpcoming(id) {
        if (confirm('Are you sure you want to cancel this upcoming reservation?')) {
            window.location.href = 'cancel_upcoming.php?id=' + id;
        }
    }

    function showPopupNotif(message) {
                const popup = document.getElementById('popupMessage');
                document.getElementById('popupText').innerText = message;
                popup.classList.add('show');

                setTimeout(() => {
                    popup.classList.remove('show');
                }, 4000);
            }

            function closePopupNotif() {
                document.getElementById('popupMessage').classList.remove('show');
            }

            <?php if (isset($_SESSION['status'])):?>
                // Show popup with session message
                showPopupNotif("<?php echo $_SESSION['status']; ?>");
                <?php unset($_SESSION['status']);?>
            <?php endif; ?>

    function showEditPopup() {
        document.getElementById('editOverlay').classList.add('active');
        document.getElementById('editPopup').classList.add('active');
    }

    // Close Edit Profile Popup
    function closeEditPopup() {
        document.getElementById('editOverlay').classList.remove('active');
        document.getElementById('editPopup').classList.remove('active');
    }
</script>
    </body>
</html>
