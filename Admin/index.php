<!DOCTYPE html>
<?php
    session_start();

    // Check if the user is logged in and has the role of "admin"
    if (!isset($_SESSION['authenticated']) || $_SESSION['auth_user']['role'] !== 'admin') {
        $_SESSION['status'] = "You are trying to access a restricted part of the website. Please log in as an admin to continue.";
        header("Location: ../index.php");
        exit(0);
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png" type="image/x-icon"/>
    <title>Admin Interface</title>
    <link rel="stylesheet" href="styles.css">

    <script>
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
    <!-- Fixed Left Sidebar -->
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="Admin Navigation Sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
        </div>
        <ul class="menu">
            <li class="menu-item active-menu" data-section="Dashboard" role="button" tabindex="0" aria-label="Dashboard">
                <div class="menu-icon dashboard-icon"></div>
                <span>Dashboard</span>
            </li>
            <li class="menu-item" data-section="Studios" role="button" tabindex="0" aria-label="Studios">
                <div class="menu-icon studios-icon"></div>
                <span>Studios</span>
            </li>
            <li class="menu-item" data-section="Schedules" role="button" tabindex="0" aria-label="Schedules">
                <div class="menu-icon schedules-icon"></div>
                <span>Schedules</span>
            </li>
            <li class="menu-item" data-section="Waiting" role="button" tabindex="0" aria-label="Waiting">
                <div class="menu-icon waiting-icon"></div>
                <span>Waiting</span>
            </li>
            <li class="menu-item" data-section="History" role="button" tabindex="0" aria-label="History">
                <div class="menu-icon history-icon"></div>
                <span>History</span>
            </li>
            
            <!-- User Management Section -->
            <li class="menu-item" data-section="CRUD" role="button" tabindex="0" aria-label="CRUD">
                <div class="menu-icon user-management-icon"></div>
                <span>CRUD</span>
            </li>
        </ul>

        <!-- Static Logout Section at the Bottom -->
        <a href="javascript:void(0);">
            <div class="logout-section" onclick="confirmLogout()" role="button" tabindex="0" aria-label="Logout">
                <div class="menu-icon logout-icon"></div>
                <span>Logout</span>
            </div>
        </a>
    </aside>

    <!-- Top Bar -->
    <div class="top-bar">
        <span class="current-section-title" id="section-title">Dashboard</span>
        <button class="profile-button" onclick="location.href='admin-profile-setup.html'">Profile Setup</button>
    </div>

    <!-- Main Content Area -->
    <main class="main-content" id="main-content">
        <!-- Dashboard Section with clickable menu squares -->
        <section id="Dashboard" class="content-section active">
            <h1>Dashboard</h1>
            <div class="dashboard-grid">
                <div class="dashboard-block" data-section="Dashboard">
                    <div class="menu-icon dashboard-icon"></div>
                    <span>Dashboard</span>
                </div>
                <div class="dashboard-block" data-section="Studios">
                    <div class="menu-icon studios-icon"></div>
                    <span>Studios</span>
                </div>
                <div class="dashboard-block" data-section="Schedules">
                    <div class="menu-icon schedules-icon"></div>
                    <span>Schedules</span>
                </div>
                <div class="dashboard-block" data-section="Waiting">
                    <div class="menu-icon waiting-icon"></div>
                    <span>Waiting</span>
                </div>
                <div class="dashboard-block" data-section="History">
                    <div class="menu-icon history-icon"></div>
                    <span>History</span>
                </div>
                <div class="dashboard-block" data-section="CRUD">
                    <div class="menu-icon user-management-icon"></div>
                    <span>CRUD</span>
                </div>
            </div>
        </section>


        <!-- Studios Section -->
        <section id="Studios" class="content-section hidden">
    <h1>Manage Studios</h1>
    <p>Update studio details such as descriptions, prices, max occupancy, and images.</p>
    
    <a href="Studio/add_studio.php"><button class="add-btn">Add New Studio</button></a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price (PHP/hour)</th>
                <th>Max Occupancy</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'studio_rentals');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch studio details
            $sql = "SELECT id, image_path, name, description, price, max_occupancy FROM studios";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td><img src='Studio/" . htmlspecialchars($row['image_path']) . "' alt='Studio Image' width='80'></td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['max_occupancy']) . "</td>";
                    echo "<td>
                            <a href='Studio/edit_studio.php?id=" . $row['id'] . "'><button class='edit-btn'>Edit</button></a>
                            <a href='Studio/delete_studio.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\");'><button class='delete-btn'>Delete</button></a>
                            <a href='Studio/add_instrument.php?studio_id=" . $row['id'] . "'>Add Instrument</a>
                            <a href='Studio/edit_instrument.php?studio_id=" . $row['id'] . "'>Edit Instruments</a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No studios available</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>                                                                    
    </table>
</section>

        <!-- Schedules Section -->
        <section id="Schedules" class="content-section hidden">
            <h1>Schedules</h1>
            <div>
                <button onclick="showPending()" class="btn">Pending Schedules</button>
                <button onclick="showConfirmed()" class="btn">Confirmed Schedules</button>
            <!-- Table to display schedules -->
            <div id="schedule-content">
                <p>Select an option to view schedules.</p>
            </div>
        </section>

        <div id="editPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
            <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
                <h3>Edit Booking</h3>
                <form id="editForm" method="POST" action="process_edit_booking.php">
                    <input type="hidden" id="booking_id" name="booking_id">
                    <label for="booking_date">Date:</label>
                    <input type="date" id="booking_date" name="booking_date" required><br><br>
                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" required><br><br>
                    <label for="end_time">End Time:</label>
                    <input type="time" id="end_time" name="end_time" required><br><br>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" onclick="closeEditPopup()" class="btn btn-danger">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Waiting Section -->
        <section id="Waiting" class="content-section hidden">
            <h1>Waiting Reservations</h1>
            <p>Manage waiting reservations.</p>

            <table border="1" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Studio ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection
                    $conn = new mysqli('localhost', 'root', '', 'studio_rentals');
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch data from waiting_bookings table
                    $sql = "SELECT * FROM waiting_bookings";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['studio_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>
                                <a href='Waiting/delete_waiting.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this record?\");'>
                                    <button class='delete-btn'>Delete</button>
                                </a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No waiting reservations found.</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </section>


        <!-- History Section -->
        <section id="History" class="content-section hidden">
            <h1>Booking History</h1>
            <p>View the history of all reservations and actions.</p>

            <table border="1" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Studio ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Total Price</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection
                    $conn = new mysqli('localhost', 'root', '', 'studio_rentals');
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch data from bookings table
                    $sql = "SELECT * FROM bookings";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Multiply the total_price by 2
                            $multiplied_price = $row['total_price'] * 2;

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['booking_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['studio_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                            echo "<td>" . number_format($multiplied_price, 2) . " PHP</td>";

                            // Display receipt link if it exists
                            if (!empty($row['receipt_path'])) {
                                echo "<td><a href='../Booking/{$row['receipt_path']}' target='_blank'>View Receipt</a></td>";
                            } else {
                                echo "<td>No Receipt</td>";
                            }

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No booking history available.</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
        </section>


        <!-- CRUD Section -->
        <section id="CRUD" class="content-section hidden">
            <h1>User Management (CRUD)</h1>

            <!-- Add PHP to Display Data Dynamically -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Token Key</th>
                        <th>Verified</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database connection configuration
                    $host = 'localhost';  // Database host
                    $db = 'studio_rentals';  // Database name
                    $user = 'root';  // Database username
                    $pass = '';  // Database password

                    // Create a connection
                    $conn = new mysqli($host, $user, $pass, $db);

                    // Check the connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Query the database for users
                    $sql = "SELECT id, username, password, first_name, last_name, email, token_key, verified, role FROM users";
                    $result = $conn->query($sql);

                    // Display the data in the table
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['password']) . "</td>";  // Be careful with displaying passwords
                            echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['token_key']) . "</td>";
                            echo "<td>" . ($row['verified'] ? 'Yes' : 'No') . "</td>";
                            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                            // Edit and Delete buttons
                            echo "<td>
                            <a href='edit.php?id=" . $row['id'] . "'><button class='edit-btn'>Edit</button></a> 
                            <a href='delete.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this user?\");'><button class='delete-btn'>Delete</button></a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No users found</td></tr>";
                    }

                    // Close the connection
                    $conn->close();
                    ?>
                </tbody>
            </table>
            <a href="create.php"><button class="create-btn">Create New User</button></a>
        </section>
    </main>

    <!-- Link to Custom JavaScript -->
    <script src="script.js"></script>

    <script>
        // Fetch and display Pending Schedules
        function showPending() {
            fetch('fetch_pending.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('schedule-content').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }

        // Fetch and display Confirmed Schedules
        function showConfirmed() {
            fetch('fetch_confirmed.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('schedule-content').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }

        function showEditPopup(id, date, start, end) {
            console.log("Edit button clicked", {id, date, start, end});
            document.getElementById('booking_id').value = id;
            document.getElementById('booking_date').value = date;
            document.getElementById('start_time').value = start;
            document.getElementById('end_time').value = end;

            document.getElementById('editPopup').style.display = 'block';
        }

        function closeEditPopup() {
            document.getElementById('editPopup').style.display = 'none';
        }
    </script>
</body>
</html>
