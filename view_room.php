<?php
require 'includes/db.php';

// Get the room ID from the URL
$room_id = $_GET['id'];

// Fetch the room details
$sql = "SELECT * FROM accomodation_tbl WHERE accomodation_id = $room_id";
$result = $conn->query($sql);
$room = $result->fetch_assoc();

// Check if tenant is logged in
session_start();
if (!isset($_SESSION['tenant_id'])) {
    header("Location: login.php");
    exit();
}

$tenant_id = $_SESSION['tenant_id'];

// Handle reservation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $reservation_date = date('Y-m-d H:i:s');
    $status = 'pending'; // Reservation is pending until payment is made

    // Insert reservation into reservation_tbl
    $stmt = $conn->prepare("INSERT INTO reservation_tbl (tenant_id, accomodation_id, reservation_date, status) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $tenant_id, $room_id, $reservation_date, $status);
    
    if ($stmt->execute()) {
        // Get the newly created reservation ID
        $reservation_id = $stmt->insert_id;
        $_SESSION['reservation_id'] = $reservation_id;

        $success_message = "Your reservation has been made. Please proceed with the payment.";
    } else {
        $error_message = "There was an error making your reservation. Please try again.";
    }
}

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $payment_code = $_POST['payment_code'];
    $reservation_id = $_SESSION['reservation_id'];
    $payment_date = date('Y-m-d');

    // Insert payment into payment_tbl
    $stmt = $conn->prepare("INSERT INTO payments_tbl (code_numb, reservation_id, payment_date) 
                            VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $payment_code, $reservation_id, $payment_date);
    
    if ($stmt->execute()) {
        // Update reservation status to confirmed
        $stmt_update = $conn->prepare("UPDATE reservation_tbl SET status = 'confirmed' WHERE reservation_id = ?");
        $stmt_update->bind_param("i", $reservation_id);
        $stmt_update->execute();
        
        $payment_success_message = "Payment confirmed. Your reservation is now confirmed.";
    } else {
        $payment_error_message = "Payment confirmation failed. Please check your payment details.";
    }
}

// Fetch all images for the selected room
$images_sql = "SELECT image_path FROM accommodation_images WHERE accommodation_id = $room_id";
$images_result = $conn->query($images_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Room Details</title>
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
        /* Animation for fade-in effect */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .back-btn {
            float: left;
            width: 40%;
            font-size: 1rem;
            margin-top: 20px;
        }
        .room-description {
            margin-top: 20px;
        }
        .amenities {
            margin-top: 20px;
        }
        .card-body .btn {
            margin-top: 10px;
            
        }
        .carousel-inner img {
        width: 100%;
        height: 400px; /* Fixed height for the images */
        object-fit: cover; /* Ensures images fill the container without distortion */
    }
    </style>
</head>
<body>
    <div class="container mt-5">
        <a href="index.php" class="btn btn-secondary mb-4">Back to Homepage</a>

        <div class="card">
            <div class="row no-gutters">
                <!-- Images Section -->
                <div class="col-md-7">
                <div id="roomCarousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <?php 
        $isFirst = true;
        if ($images_result->num_rows > 0) {
            while ($image = $images_result->fetch_assoc()) { 
                $activeClass = $isFirst ? 'active' : '';
                $isFirst = false;
            ?>
                <div class="carousel-item <?php echo $activeClass; ?>">
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" class="d-block w-100" alt="Room Image">
                </div>
            <?php 
            } 
        } else { 
            // Display default image if no images are found
            ?>
            <div class="carousel-item active">
                <img src="images/default-room.jpg" class="d-block w-100" alt="Default Room Image">
            </div>
        <?php } ?>
    </div>

    <!-- Carousel controls -->
    <a class="carousel-control-prev" href="#roomCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#roomCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

                </div>

                <!-- Details Section -->
<div class="col-md-5">
    <div class="card-body bg-light border rounded shadow-lg">
        <h5 class="card-title text-primary font-weight-bold">
            <?php echo htmlspecialchars($room['description']); ?>
        </h5>
        <p class="card-text text-dark">
            <strong>Price:</strong> 
            <span class="text-success">₱<?php echo htmlspecialchars($room['price']); ?></span> per month
        </p>
        <p class="card-text text-dark">
            <strong>Location:</strong> 
            <span class="text-secondary"><?php echo htmlspecialchars($room['address']); ?></span>
        </p>
        <p class="card-text text-dark">
            <strong>Contact:</strong> 
            <span class="text-info"><?php echo htmlspecialchars($room['owner_number']); ?></span>
        </p>
        <p class="card-text text-dark">
            <strong>Status:</strong> 
            <span class="badge badge-<?php echo ($room['status'] === 'Available') ? 'success' : 'danger'; ?>">
                <?php echo htmlspecialchars($room['status']); ?>
            </span>
        </p>

        <!-- Amenities Section -->
        <div class="amenities d-flex justify-content-between mt-3 py-2 px-3 bg-white border rounded">
            <span class="text-muted"><i class="fas fa-wifi text-primary"></i> Free Wifi</span>
            <span class="text-muted"><i class="fas fa-parking text-warning"></i> Private Parking</span>
        </div>

        <!-- Reserve Button -->
        <a href="reservation_form.php?id=<?php echo $room_id; ?>" class="btn btn-success mt-4 btn-block font-weight-bold shadow">
            Reserve This Room
        </a>
    </div>
</div>
            
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

