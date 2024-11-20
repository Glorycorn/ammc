<?php
require '../includes/db.php';  // Include your database connection file

// Define the admin email and password
$email = "admin@example.com";  // Replace with your admin email
$password = "admin";           // Replace with the desired password

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert the admin record
$sql = "INSERT INTO admin_tbl (email, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('ss', $email, $hashed_password);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        echo "Admin account created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
