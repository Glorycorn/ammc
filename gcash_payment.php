<?php
// Get the amount from the URL or default to 0 if not set
$amount = isset($_GET['amount']) ? $_GET['amount'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCash Payment</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .payment-container {
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

        p {
            font-size: 18px;
            color: #333;
        }

        .btn {
            background-color: #006400;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #005000;
        }

        .payment-form {
            margin-top: 20px;
        }

        .payment-form input[type="number"] {
            padding: 10px;
            width: 80%;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .payment-form button {
            background-color: #006400;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            margin-top: 10px;
        }

        .payment-form button:hover {
            background-color: #005000;
        }
    </style>
</head>
<body>

<div class="payment-container">
    <h1>GCash Payment</h1>
    <p>Total amount: ₱<?php echo htmlspecialchars($amount); ?></p>

    <!-- Payment Form for entering the amount -->
    <form class="payment-form" action="payment_success.php" method="post">
        <label for="payment_amount">Enter Payment Amount:</label>
        <input type="number" name="payment_amount" id="payment_amount" value="<?php echo $amount; ?>" min="0" required>

        <button type="submit">Confirm Payment</button>
    </form>
</div>

</body>
</html>
