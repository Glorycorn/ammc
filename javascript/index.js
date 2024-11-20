<?php
// Start session
session_start();

// Include database connection
require 'includes/db.php';
// Initialize variables for search filters
$location = isset($_GET['location']) ? $_GET['location'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$adults = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
$children = isset($_GET['children']) ? intval($_GET['children']) : 0;
$roomsNeeded = isset($_GET['rooms_needed']) ? intval($_GET['rooms_needed']) : 1;

// Base SQL query for available accommodations with filters
$all_accommodations_sql = "
    SELECT * FROM accomodation_tbl 
    WHERE status = 'available'
    AND address LIKE ?
    AND capacity >= ?
    AND available_rooms >= ?
    AND (availability_start <= ? AND availability_end >= ?)
";

// Prepare statement with placeholders
$stmt = $conn->prepare($all_accommodations_sql);
$stmt->bind_param("siiss", $locationParam, $totalGuests, $roomsNeeded, $startDate, $endDate);

// Set parameters for search
$locationParam = '%' . $location . '%';
$totalGuests = $adults + $children;
$stmt->execute();
$all_accommodations_result = $stmt->get_result();

// Fetch recommended accommodations
$recommended_sql = "SELECT * FROM accomodation_tbl WHERE recommended = 1 AND status = 'available'";
$recommended_result = $conn->query($recommended_sql);
// Fetch recommended accommodations
$recommended_sql = "SELECT * FROM accomodation_tbl WHERE recommended = 1 AND status = 'available'";
$recommended_result = $conn->query($recommended_sql);

// Fetch all available accommodations
$all_accommodations_sql = "SELECT * FROM accomodation_tbl WHERE status = 'available'";
$all_accommodations_result = $conn->query($all_accommodations_sql);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Online Room Accommodation - Home</title>
</head>
<body>
    <header>
        <!-- Navigation Bar -->
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="rooms.php">Accommodation</a></li>
                <li><a href="categories.php">Category</a></li>
            </ul>
        </nav>

        <div class="header-buttons">
            <?php if (isset($_SESSION['tenant_id'])) { ?>
                <a href="tenant_dashboard.php">My Profile</a>
                <a href="logout.php">Logout</a>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php } ?>
        </div>
    </header>

    <h1>Welcome to Our Online Room Accommodation</h1>

    <!-- Search Section -->
    <section class="search-section">
        <h2>Search Rooms</h2>
        <form id="search-form" onsubmit="event.preventDefault(); searchRooms();">
            <input type="text" id="location-input" placeholder="Location" required>
            <input type="date" id="checkin-date" required>
            <input type="date" id="checkout-date" required>
            <input type="number" id="people-count" placeholder="Number of People" min="1" required>
            <input type="number" id="room-count" placeholder="Number of Rooms" min="1" required>
            <button type="submit">Search</button>
        </form>
    </section>

    <!-- Recommended Rooms Section -->
    <section class="recommended-rooms">
        <h2>Recommended Rooms</h2>
        <div class="rooms-container">
            <?php
            if ($recommended_result->num_rows > 0) {
                while ($row = $recommended_result->fetch_assoc()) {
                    ?>
                    <div class="room-card" data-description="<?php echo htmlspecialchars($row['description']); ?>" 
                         data-location="<?php echo htmlspecialchars($row['address']); ?>">
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Room Image" class="room-image">
                        <h3><?php echo htmlspecialchars($row['description']); ?></h3>
                        <p>Price: $<?php echo htmlspecialchars($row['price']); ?> per month</p>
                        <p>Location: <?php echo htmlspecialchars($row['address']); ?></p>
                        <a href="view_room.php?id=<?php echo htmlspecialchars($row['accomodation_id']); ?>" class="view-btn">View Room</a>
                        <a href="reservation_form.php?id=<?php echo htmlspecialchars($row['accomodation_id']); ?>" class="reserve-btn">Reserve</a>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No recommended rooms available at the moment.</p>";
            }
            ?>
        </div>
    </section>

    <!-- All Available Rooms Section -->
    <section class="all-available-rooms">
        <h2>All Available Rooms</h2>
        <div id="rooms-container" class="rooms-container">
            <?php
            // Output all available rooms
            while ($row = $all_accommodations_result->fetch_assoc()) {
                ?>
                <div class="room-card" data-description="<?php echo htmlspecialchars($row['description']); ?>" 
                     data-location="<?php echo htmlspecialchars($row['address']); ?>">
                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Room Image" class="room-image">
                    <h3><?php echo htmlspecialchars($row['description']); ?></h3>
                    <p>Price: $<?php echo htmlspecialchars($row['price']); ?> per month</p>
                    <p>Location: <?php echo htmlspecialchars($row['address']); ?></p>
                    <a href="view_room.php?id=<?php echo htmlspecialchars($row['accomodation_id']); ?>" class="view-btn">View Room</a>
                    <a href="reservation_form.php?id=<?php echo htmlspecialchars($row['accomodation_id']); ?>" class="reserve-btn">Reserve</a>
                </div>
                <?php
            }
            ?>
        </div>
    </section>

    <!-- JavaScript for Search Functionality -->
    <script>
        function searchRooms() {
            const locationQuery = document.getElementById('location-input').value.toLowerCase();
            const checkinDate = document.getElementById('checkin-date').value;
            const checkoutDate = document.getElementById('checkout-date').value;
            const peopleCount = document.getElementById('people-count').value;
            const roomCount = document.getElementById('room-count').value;

            const rooms = document.querySelectorAll('.room-card');

            rooms.forEach(room => {
                const roomLocation = room.getAttribute('data-location').toLowerCase();
                const isLocationMatch = roomLocation.includes(locationQuery);

                if (isLocationMatch) {
                    room.style.display = 'block';
                } else {
                    room.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
