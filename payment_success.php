<?php
// Simulate payment processing

// Get the entered payment amount
$payment_amount = isset($_POST['payment_amount']) ? $_POST['payment_amount'] : 0;

// Redirect back to homepage (index.php)
header("Location: index.php?payment_status=success");
exit();
?>
