<?php
session_start();
require 'includes/db.php';

// Ensure owner is logged in
if (!isset($_SESSION['owner_id']) || $_SESSION['user_type'] != 'owner') {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['owner_id'];
$message_type = $_POST['message_type'];
$content = $_POST['content'];

// Insert the message or report into the database
$sql = "INSERT INTO messages_tbl (sender_id, recipient_id, content, type, reported) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$recipient_id = 1; // Admin ID (assuming admin ID is 1, adjust accordingly)
$reported = ($message_type == 'report') ? 1 : 0; // Reports are marked as 'reported' = 1
$stmt->bind_param("iissi", $owner_id, $recipient_id, $content, $message_type, $reported);
$stmt->execute();

$stmt->close();

// Redirect back to the dashboard with a success message
header("Location: owner_dashboard.php?message_sent=1");
exit();
?>
