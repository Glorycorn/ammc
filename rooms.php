<?php
// Start session
session_start();

// Include database connection
require 'includes/db.php';


// Fetch all available accommodations
$all_accommodations_sql = "
    SELECT accomodation_tbl.*, 
           (SELECT image_path 
            FROM accommodation_images 
            WHERE accommodation_images.accommodation_id = accomodation_tbl.accomodation_id 
            LIMIT 1) AS first_image 
    FROM accomodation_tbl 
    WHERE status = 'available'";
$all_accommodations_result = $conn->query($all_accommodations_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" type="x-icon" href="./images/ammc.png">
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

     <!-- CSS for Enhanced Background and Animation -->
     <style>
        /* Background styling */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('images/realistic-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            animation: backgroundMove 20s infinite alternate;
            font-family: Arial, sans-serif;
        }

        /* Subtle animation for background */
        @keyframes backgroundMove {
            0% { background-position: center; }
            100% { background-position: top; }
        }

        /* Header fade-in effect */
        header {
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: fadeIn 2s ease;
        }

        /* Navigation and header links styling */
        .nav-links a {
            color: #fff;
            font-weight: bold;
            padding: 10px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #ffcc00;
        }

        .header-buttons a {
            margin: 0 10px;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .header-buttons a:hover {
            color: #ffcc00;
        }

        /* Room card styling with fade-in animation */
        .rooms-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .room-card {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border-radius: 10px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: fadeIn 1.5s ease forwards;
            opacity: 0;
        }

        .room-card img {
            border-radius: 10px;
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        /* Scale effect on hover */
        .room-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        /* Animation for fade-in effect */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Search bar styling */
        .search-section input {
            padding: 10px;
            width: 100%;
            max-width: 300px;
            border-radius: 5px;
            border: none;
            margin-top: 10px;
            font-size: 16px;
        }

        .search-section {
            text-align: center;
            padding: 20px;
        }
         /* General header styling */
    #main-header {
        background-color: #333;
        color: #fff;
        padding: 10px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    /* Navbar styling */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Navigation links styling */
    .nav-links {
        list-style: none;
        display: flex;
        gap: 20px;
    }

    .nav-links li {
        display: inline;
    }

    .nav-links a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        padding: 8px 12px;
        transition: color 0.3s ease;
    }

    .nav-links a:hover {
        color: #ffcc00;
    }

    /* Header buttons styling */
    .header-buttons {
        display: flex;
        gap: 15px;
    }

    .btn {
        color: #fff;
        background-color: #444;
        border: none;
        padding: 8px 16px;
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #555;
    }

    /* Primary button for Sign Up */
    .btn-primary {
        background-color: #ffcc00;
        color: #333;
    }

    .btn-primary:hover {
        background-color: #e6b800;
    }
    </style>

     <!-- Search Section -->
     <section class="search-section">
        <h2>Search Rooms</h2>
        <input type="text" id="search-input" placeholder="Search by description or location" onkeyup="searchRooms()">
    </section>

    <!-- All Available Rooms Section -->
    <section class="all-available-rooms">
    <h2>Available Rooms</h2>
    <div class="rooms-container">
        <?php
        if ($all_accommodations_result->num_rows > 0) {
            while ($row = $all_accommodations_result->fetch_assoc()) {
                ?>
                <div class="room-card" 
                     data-description="<?php echo htmlspecialchars($row['description']); ?>" 
                     data-location="<?php echo htmlspecialchars($row['address']); ?>">
                    <img src="<?php echo !empty($row['first_image']) ? htmlspecialchars($row['first_image']) : 'images/default-room.jpg'; ?>" 
                         alt="Room Image" class="room-image">
                    <h3><?php echo htmlspecialchars($row['description']); ?></h3>
                    <p>Price: ₱<?php echo htmlspecialchars($row['price']); ?> per month</p>
                    <p>Location: <?php echo htmlspecialchars($row['address']); ?></p>
                    <a href="view_room.php?id=<?php echo htmlspecialchars($row['accomodation_id']); ?>" class="view-btn">View Room</a>
                </div>
                <?php
            }
        } else {
            echo "<p>No rooms available at the moment.</p>";
        }
        ?>
    </div>
</section>

<!-- JavaScript for Search Functionality -->
<script>
    // JavaScript function to search rooms based on user input
    function searchRooms() {
        // Get the search input value and convert to lowercase
        const searchQuery = document.getElementById('search-input').value.toLowerCase();

        // Get all room cards
        const rooms = document.querySelectorAll('.room-card');

        rooms.forEach(room => {
            // Get room data attributes
            const roomDescription = room.getAttribute('data-description').toLowerCase();
            const roomLocation = room.getAttribute('data-location').toLowerCase();

            // Check if the search query matches the description or location
            if (roomDescription.includes(searchQuery) || roomLocation.includes(searchQuery)) {
                room.style.display = 'block'; // Show room if matches
            } else {
                room.style.display = 'none'; // Hide room if not matches
            }
        });
    }
</script>
</body>
</html>
