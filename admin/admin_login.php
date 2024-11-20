<?php
session_start();
require '../includes/db.php';  // Database connection file

$error = '';  // Variable to store error messages

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Input validation
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        // SQL query to fetch admin by email
        $sql = "SELECT * FROM admin_tbl WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // Verify hashed password
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['user_type'] = 'admin';  // For authorization checks
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Admin account not found.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" type="x-icon" href="../images/ammc.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Reset basic margin and padding */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to right, #4a90e2, #4f98e2); /* Gradient background */
        }

        .login-container {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out; /* Animation for fade in */
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        /* Error Message Style */
        .error {
            color: #d9534f;  /* Red color for error */
            margin-bottom: 15px;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.6);
        }

        /* Button Style */
        .btn {
            width: 100%;
            padding: 14px;
            background-color: #28a745;  /* Green color */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background-color: #218838;  /* Darker Green */
            transform: scale(1.05);
        }

        /* Animation for form fade-in */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Password container for show/hide functionality */
        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: #4a90e2;
            cursor: pointer;
        }

        /* Responsive design for mobile devices */
        @media (max-width: 768px) {
            .login-container {
                padding: 20px;
                width: 90%;
            }

            h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <!-- Display error message if set -->
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="admin_login.php">
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword('password')">Show</span>
            </div>
        </div>

        <div class="input-group">
            <button type="submit" class="btn">Login</button>
        </div>
    </form>
</div>

<!-- Script to toggle password visibility -->
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
