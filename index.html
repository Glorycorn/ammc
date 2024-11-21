<?php
// Start session
session_start();

// Include database connection
require 'includes/db.php';

// Fetch recommended accommodations
$recommended_sql = "
    SELECT accomodation_tbl.*, 
           accomodation_tbl.owner_number, 
           (SELECT image_path 
            FROM accommodation_images 
            WHERE accommodation_images.accommodation_id = accomodation_tbl.accomodation_id 
            LIMIT 1) AS first_image 
    FROM accomodation_tbl 
    WHERE recommended = 1 AND status = 'available'";  // Ensuring only available rooms are fetched

// Fetch all available accommodations with their first image
$all_accommodations_sql = "
    SELECT accomodation_tbl.*, 
           accomodation_tbl.owner_number, 
           (SELECT image_path 
            FROM accommodation_images 
            WHERE accommodation_images.accommodation_id = accomodation_tbl.accomodation_id 
            LIMIT 1) AS first_image 
    FROM accomodation_tbl 
    WHERE status = 'available'";  // Ensuring only available rooms are fetched

// Execute the recommended rooms query
$recommended_result = $conn->query($recommended_sql);

// Execute the all accommodations query
$all_accommodations_result = $conn->query($all_accommodations_sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" type="x-icon" href="./images/Ammc.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>AMMC Online Room Accommodation - Home</title>
</head>
<body>
    <header>

     <!-- Left Section: Logo and Title -->
     <div class="header-left">
            <img src="./images/Ammc.png" alt="Logo Profile Picture">
        </div>
       

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
    <h1>Welcome to AMMC Online Room Accommodation</h1>

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
        .header-left {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .header-left img {
            width: 145px;
            height: 140px;
            border-radius: 50%;
        }

        .header-left h1 {
            color: #fff;
            font-size: 20px;
            margin: 0;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 15px;
            margin: 0;
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

        .header-buttons {
            display: flex;
            gap: 15px;
        }

        .header-buttons a {
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .header-buttons a:hover {
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
        <input type="text" id="search-input" placeholder="Search by description or location and price" onkeyup="searchRooms()">
    </section>

    <!-- Recommended Rooms Section -->
    <section class="recommended-rooms">
        <h2>Recommended Rooms</h2>

        <div class="rooms-container">
            <?php
            // Check if recommended rooms exist
            if ($recommended_result && $recommended_result->num_rows > 0) {
                while ($row = $recommended_result->fetch_assoc()) {
                    ?>
                    <div class="room-card" data-description="<?php echo htmlspecialchars($row['description']); ?>" 
                    data-location="<?php echo htmlspecialchars($row['address']); ?>">
                    <a href="view_room.php?id=<?php echo htmlspecialchars($row['accomodation_id']); ?>">
                        <img src="<?php echo !empty($row['first_image']) ? htmlspecialchars($row['first_image']) : 'images/default-room.jpg'; ?>" alt="Room Image" class="room-image">
                    </a>
                    <h3><?php echo htmlspecialchars($row['description']); ?></h3>
                    <p>Price: ₱<?php echo htmlspecialchars($row['price']); ?> per month</p>
                    <p>Location: <?php echo htmlspecialchars($row['address']); ?></p>
                    <p>Owner Number: <?php echo htmlspecialchars($row['owner_number']); ?></p>
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
        <h2>Available Rooms</h2>
        <div class="rooms-container">
            <?php
            // Check if available rooms exist
            if ($all_accommodations_result && $all_accommodations_result->num_rows > 0) {
                while ($row = $all_accommodations_result->fetch_assoc()) {
                    ?>
                    <div class="room-card" data-description="<?php echo htmlspecialchars($row['description']); ?>" 
                    data-location="<?php echo htmlspecialchars($row['address']); ?>">
                    <a href="view_room.php?id=<?php echo htmlspecialchars($row['accomodation_id']); ?>">
                        <img src="<?php echo !empty($row['first_image']) ? htmlspecialchars($row['first_image']) : 'images/default-room.jpg'; ?>" alt="Room Image" class="room-image">
                    </a>
                    <h3><?php echo htmlspecialchars($row['description']); ?></h3>
                    <p>Price: ₱<?php echo htmlspecialchars($row['price']); ?> per month</p>
                    <p>Location: <?php echo htmlspecialchars($row['address']); ?></p>
                    <p>Owner Number: <?php echo htmlspecialchars($row['owner_number']); ?></p>
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
        function searchRooms() {
            const searchQuery = document.getElementById('search-input').value.toLowerCase();
            const rooms = document.querySelectorAll('.room-card');

            rooms.forEach(room => {
                const roomDescription = room.getAttribute('data-description').toLowerCase();
                const roomLocation = room.getAttribute('data-location').toLowerCase();
                const roomPrice = room.getAttribute('data-price').toLowerCase();

                if (roomDescription.includes(searchQuery) || roomLocation.includes(searchQuery)) {
                    room.style.display = 'block';
                } else {
                    room.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>