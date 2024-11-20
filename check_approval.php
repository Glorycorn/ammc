<?php
session_start();
require 'includes/db.php';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql = "SELECT approved FROM owners_tbl WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($approved);
    $stmt->fetch();
    $stmt->close();

    echo $approved ? 'approved' : '';
}
?>
