<?php
session_start();
require 'includes/db.php';

// Check if the tenant is logged in
if (!isset($_SESSION['tenant_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch tenant data
$tenant_id = $_SESSION['tenant_id'];
$sql = "SELECT * FROM tenants_tbl WHERE tenant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $tenant = $result->fetch_assoc();
} else {
    die("Tenant data not found.");
}

// Fetch data based on the view
$view = $_GET['view'] ?? 'profile';
if ($view === 'reservations') {
    $reservations_sql = "SELECT r.*, a.description, a.price, a.address, a.owner_number 
                         FROM reservation_tbl r
                         JOIN accomodation_tbl a ON r.accomodation_id = a.accomodation_id
                         WHERE r.tenant_id = ?";
    $stmt = $conn->prepare($reservations_sql);
    $stmt->bind_param("i", $tenant['tenant_id']);
    $stmt->execute();
    $reservations_result = $stmt->get_result();
} elseif ($view === 'payments') {
    $payments_sql = "SELECT p.*, r.start_date, a.description 
                     FROM payments_tbl p
                     JOIN reservation_tbl r ON p.reservation_id = r.reservation_id
                     JOIN accomodation_tbl a ON r.accomodation_id = a.accomodation_id
                     WHERE r.tenant_id = ? AND r.status = 'reserved'";
    $stmt = $conn->prepare($payments_sql);
    $stmt->bind_param("i", $tenant['tenant_id']);
    $stmt->execute();
    $payments_result = $stmt->get_result();
} elseif ($view === 'messages') {
    $messages_sql = "SELECT * FROM messages_tbl WHERE sender_id = ?";
    $stmt = $conn->prepare($messages_sql);
    $stmt->bind_param("i", $tenant['tenant_id']);
    $stmt->execute();
    $messages_result = $stmt->get_result();
}
?>


<!DOCTYPE html>
<html>
<head>
    <!-- FontAwesome CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="x-icon" href="./images/Ammc.png">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Tenant Dashboard</title>
    <style>
/* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(to bottom right, #1d3557, #457b9d);
    color: #f1faee;
    display: flex;
    flex-direction: row;
    height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: 250px;
    background: #1d3557;
    height: 100vh;
    padding: 20px;
    box-sizing: border-box;
    position: fixed;
    box-shadow: 3px 0 5px rgba(0, 0, 0, 0.2);
}

.sidebar h2 {
    color: #f4a261;
    margin-bottom: 20px;
    text-align: center;
}

.sidebar a {
    display: block;
    color: #f1faee;
    text-decoration: none;
    padding: 12px 15px;
    margin: 8px 0;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.sidebar a:hover {
    background: #f4a261;
    color: #1d3557;
    font-weight: bold;
}

/* Content Area */
.content {
    margin-left: 270px;
    padding: 40px;
    width: calc(100% - 270px);
    box-sizing: border-box;
    overflow-y: auto;
}

h1, h2 {
    color: #f4a261;
    margin-bottom: 20px;
}

.tenant-info, .reservations, .payments, .messages {
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.tenant-info p, .reservations table, .payments table {
    color: #f1faee;
}

/* Profile Section */
.tenant-info {
    display: flex;
    align-items: center;
}

.profile-picture img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 15px;
}

/* Style for Settings Icon Button */
.edit-profile-button {
    display: inline-block;
    margin-top: 15px;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: transform 0.3s ease;
    padding: 5px 15px;
    text-decoration: none;
}

.edit-profile-button:hover {
    transform: scale(1.2);  /* Scale effect when hovered */
}

.edit-profile-button i {
    font-size: 14px;
    color: #f4a261;
}

.edit-profile-button:hover i {
    color: #e76f51;  /* Change icon color on hover */
}
/* Tables */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table th, table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #457b9d;
}

table th {
    background: #457b9d;
    color: #f1faee;
}

table td {
    background: rgba(255, 255, 255, 0.1);
}

/* Messages Section */
textarea {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    color: #f1faee;
    font-size: 14px;
    margin-bottom: 10px;
}

textarea::placeholder {
    color: #f1faee;
    opacity: 0.8;
}

button {
    padding: 10px 20px;
    background: #f4a261;
    color: #1d3557;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: #e76f51;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 200px;
    }
    .content {
        margin-left: 220px;
        width: calc(100% - 220px);
    }
    h1, h2 {
        font-size: 1.5em;
    }
}
    </style>
</head>
<body>
   <!-- Sidebar -->
<div class="sidebar">
    <!-- Tenant Profile -->
    <div class="tenant-profile" style="text-align: center; margin-bottom: 20px;">
        <img src="./images/profile.png" alt="Tenant Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px;">
        <?php
        // Provide fallback values for tenant name and email
        $tenantName = isset($tenant['firstname']) ? htmlspecialchars($tenant['firstname']) : 'Unknown Tenant';
        $tenantEmail = isset($tenant['email']) ? htmlspecialchars($tenant['email']) : 'Not Set';
        ?>
        <h3 style="margin: 0; font-size: 18px; color: #f4a261;"><?php echo $tenantName; ?></h3>
        <p style="margin: 0; font-size: 14px; color: #ddd;"><?php echo $tenantEmail; ?></p>
        <a href="edit_profile.php" class="edit-profile-button">
            <i class="fas fa-cog" style="font-size: 14px; color: #f4a261;"></i> <!-- Gear Icon -->
        </a>

    </div>

    <!-- Sidebar Links -->
    <a href="?view=reservations">Reservations</a>
    <a href="?view=payments">Payments</a>
    <a href="?view=messages">Messages</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Content Area -->
<div class="content">
    <?php if ($view === 'reservations'): ?>
        <!-- Reservations Section -->
        <h2>Your Reservations</h2>
        <?php if ($reservations_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Owner Number</th>
                    <th>Price (per month)</th>
                    <th>Address</th>
                    <th>Reservation Date</th>
                </tr>
                <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($reservation['owner_number']) ?></td>
                        <td><?= htmlspecialchars($reservation['price']) ?></td>
                        <td><?= htmlspecialchars($reservation['address']) ?></td>
                        <td><?= htmlspecialchars($reservation['start_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You have no reservations at the moment.</p>
        <?php endif; ?>
    <?php elseif ($view === 'payments'): ?>
        <!-- Payments Section -->
        <h2>Your Payments</h2>
        <?php if ($payments_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Payment Code</th>
                    <th>Reservation Date</th>
                    <th>Payment Date</th>
                </tr>
                <?php while ($payment = $payments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($payment['code_numb']) ?></td>
                        <td><?= htmlspecialchars($payment['start_date']) ?></td>
                        <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You have no payments at the moment.</p>
        <?php endif; ?>
    <?php elseif ($view === 'messages'): ?>
        <!-- Messages Section -->
        <h2>Messages</h2>
        <form method="POST" action="">
            <textarea name="message" rows="4" placeholder="Type your message or report to the admin..."></textarea><br>
            <button type="submit">Send Message</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
