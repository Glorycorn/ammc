<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = null;

    // Query to check login in tenants table
    $sql = "SELECT tenant_id, password FROM tenants_tbl WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($tenant_id, $tenant_password);
        $stmt->fetch();
        
        if (password_verify($password, $tenant_password)) {
            $_SESSION['tenant_id'] = $tenant_id;
            $_SESSION['user_type'] = 'tenant';
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        }
    }

    // Query to check login in owners table if tenant login fails
    $sql = "SELECT owner_number, password FROM owners_tbl WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($owner_number, $owner_password);
        $stmt->fetch();
        
        if (password_verify($password, $owner_password)) {
            $_SESSION['owner_number'] = $owner_number;
            $_SESSION['user_type'] = 'owner';
            $_SESSION['email'] = $email; // Store the email in the session
            header("Location: owner_dashboard.php");
            exit();
        }
    }

    // Close the statement
    $stmt->close();
    
    // Handle login failure
    echo "<p style='color:red;'>Invalid email or password.</p>";
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" type="x-icon" href="./images/ammc.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #6a11cb, #2575fc);
        }
        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 400px;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-size: 14px;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .password-container {
            position: relative;
        }
        .password-container input {
            width: 90%;
            padding-right: 50px; /* Space for the toggle button */
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }
        button {
            width: 100%;
            background: #6a11cb;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s ease, background-color 0.3s ease;
        }
        button:hover {
            background-color: #2575fc;
            transform: scale(1.05);
        }
        p {
            margin-top: 20px;
            font-size: 14px;
        }
        p a {
            text-decoration: none;
            color: #2575fc;
            font-weight: bold;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>

        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('password')">Show</span>
            </div>

            <button type="submit">Login</button>
        </form>
        <p>New here? <a href="signup.php">Register now!</a></p>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                toggle.textContent = "Hide";
            } else {
                input.type = "password";
                toggle.textContent = "Show";
            }
        }
    </script>
</body>
</html>
