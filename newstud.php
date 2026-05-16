<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM studios";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="logo.png" type="image/x-icon"/>
        <title>Studios</title>

        <!-- CSS and Font Awesome links -->
        <link rel="stylesheet" type="text/css" href="allstyle.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/boxicons/2.0.7/css/boxicons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">

        <!-- Login form CSS -->
        <link rel="stylesheet" href="LoginStyle.css">
        <link rel="stylesheet" href="studstyle.css">
    
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
                    window.location.href = "logout.php"; // Redirects to logout if confirmed
                }
            }
        </script>
</head>
<body>
<header>
<a href="index.php">
                <img src="logo.png" alt="Description of the image" class="logo">
            </a>
            <ul class="navlist">
                <li><a href="index.php">Home</a></li>
                <li><a href="studfinal.php">Studio</a></li>    
                <li><a href="contsfinal.php">Contacts</a></li>
                <li><a href="abtfinal.php">About Us</a></li>

                <!-- Check if the user is logged in -->
                <?php if (isset($_SESSION['authenticated'])): ?>
                    <li><a class="profile-btn" href="Profile/user_profile.html">Profile</a></li>
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
<div class="card-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <img src="Admin/Studio/<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="card__image">
            <div class="card__content">
                <p class="card__title"><?= htmlspecialchars($row['name']) ?></p>
                <p class="card__description">
                    Rate per hour: <?= htmlspecialchars($row['price']) ?> PHP<br>
                    Maximum occupancy: <?= htmlspecialchars($row['max_occupancy']) ?> persons<br>
                    <?= nl2br(htmlspecialchars($row['description'])) ?>
                </p>
                <button onclick="window.location.href='studio_details.php?id=<?= $row['id'] ?>'" class="card__button">More Details</button>
                <button onclick="window.location.href='studio_details.php?id=<?= $row['id'] ?>'" class="card__button">Book Now</button>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<div id="footer">
    &copy; K2 Band Rehearsal Studio. All rights reserved.
</div>
</body>
</html>
<?php $conn->close(); ?>
