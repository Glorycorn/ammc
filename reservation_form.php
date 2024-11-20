<?php
// Start session
session_start();

// Include database connection
require 'includes/db.php';

// Get room ID from URL
$roomId = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch room details based on the room ID
if ($roomId) {
    $room_sql = "SELECT * FROM accomodation_tbl WHERE accomodation_id = ?";
    $stmt = $conn->prepare($room_sql);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $room_result = $stmt->get_result();
    $room = $room_result->fetch_assoc();
}

$reservation_success = false; // Initialize reservation_success

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process reservation form submission
    $tenant_id = $_SESSION['tenant_id'];
    $room_id = $_POST['room_id'];
    $start_date = $_POST['start_date'];
    $people = $_POST['people'];
    $total_price = $_POST['total_price'];

    // Check if the room is available before allowing reservation
    $check_availability_sql = "SELECT status FROM accomodation_tbl WHERE accomodation_id = ?";
    $stmt = $conn->prepare($check_availability_sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['status'] != 'available') {
        // Notify user that the room is no longer available
        echo "<p>This room is no longer available for reservation.</p>";
        exit();
    }

    // Insert reservation into reservation_tbl
    $insert_sql = "INSERT INTO reservation_tbl (tenant_id, accomodation_id, start_date, people, total_price) 
                   VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iissi", $tenant_id, $room_id, $start_date, $people, $total_price);
    $stmt->execute();

    // After insertion, set $reservation_success to true
    if ($stmt->affected_rows > 0) {
        $reservation_success = true;

        // Update the room status to unavailable once reserved
        $update_status_sql = "UPDATE accomodation_tbl SET status = 'unavailable' WHERE accomodation_id = ?";
        $stmt = $conn->prepare($update_status_sql);
        $stmt->bind_param("i", $room_id);
        $stmt->execute();

        // Redirect to GCash payment page
        header("Location: gcash_payment.php?amount=" . urlencode($total_price));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Form</title>
    <style>
        <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Body styling */
        body {
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container for the form */
        .form-container {
            background-color: #fff;
            padding: 20px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 15px;
        }

        /* Room info styling */
        .room-info {
            margin-bottom: 20px;
            color: #555;
        }

        /* Input fields */
        label {
            display: block;
            text-align: left;
            color: #333;
            margin: 10px 0 5px;
        }

        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
        }

        /* Button styling */
        button[type="submit"] {
            background-color: #006400;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color: #005000;
        }

        /* Error messages */
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        .total-price {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }
    </style>
    </style>
</head>
<body>

<div class="form-container">
    <h1>Reservation Form</h1>

    <?php if ($room): ?>
        <form id="reservationForm" action="reservation_form.php" method="post">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['accomodation_id']); ?>">

            <div class="room-info">
                <p>: <?php echo htmlspecialchars($room['description']); ?></p>
                <p>Price: ₱<?php echo htmlspecialchars($room['price']); ?> per month</p>
            </div>

            <label for="start_date">Reservation Date:</label>
            <input type="date" name="start_date" id="start_date" required>

            <label for="people">Person:</label>
            <input type="number" name="people" id="people" min="1" required>

            <div class="total-price" id="totalPrice">Total: ₱0</div>

            <button type="submit">Confirm Reservation</button>
            <p class="error" id="error-message"></p>
        </form>
    <?php else: ?>
        <p>Room not found.</p>
    <?php endif; ?>
</div>

<script>
    document.getElementById('people').addEventListener('input', function() {
        const people = document.getElementById('people').value;
        const pricePerMonth = <?php echo $room ? $room['price'] : 0; ?>;
        const totalPrice = people * pricePerMonth;
        
        // Update the displayed total price
        document.getElementById('totalPrice').textContent = "Total: ₱" + totalPrice;

        // Include total price in the form
        document.getElementById('reservationForm').addEventListener('submit', function() {
            // Add the total price to the form
            const totalPriceField = document.createElement('input');
            totalPriceField.type = 'hidden';
            totalPriceField.name = 'total_price';
            totalPriceField.value = totalPrice;
            document.getElementById('reservationForm').appendChild(totalPriceField);
        });
    });
</script>

</body>
</html>
