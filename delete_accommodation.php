<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accomodation_id'])) {
    $id = intval($_POST['accomodation_id']);
    $stmt = $conn->prepare("DELETE FROM accomodation_tbl WHERE accomodation_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Accommodation deleted successfully.";
        header("Location: owner_dashboard.php");
    } else {
        echo "Error deleting accommodation.";
    }

    $stmt->close();
}
$conn->close();
?>
