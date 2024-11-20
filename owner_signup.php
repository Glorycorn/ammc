<?php
session_start();
require 'includes/db.php';

// Admin email (for notification purposes)
$adminEmail = 'admin@example.com';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $owner_number = $_POST['owner_number'];
    $description = $_POST['description']; // Retrieve description

    // Handle file upload for the business permit
    if (isset($_FILES['business_permit']) && $_FILES['business_permit']['error'] == 0) {
        $permitFile = $_FILES['business_permit'];
        $permitFileName = basename($permitFile['name']);
        $permitFilePath = 'uploads/' . $permitFileName;

        // Ensure the uploads directory exists
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Move the uploaded file to the server's directory
        if (move_uploaded_file($permitFile['tmp_name'], $permitFilePath)) {
            // Check if the email is already registered
            $checkEmail = "SELECT * FROM owners_tbl WHERE email = '$email'";
            $result = $conn->query($checkEmail);

            if ($result->num_rows > 0) {
                $error = "Email already registered!";
            } else {
                // Insert new owner with description, business permit, and initial approval set to 0
                $sql = "INSERT INTO owners_tbl (email, password, lastname, firstname, middlename, owner_number, description, business_permit, approved)
                        VALUES ('$email', '$password', '$lastname', '$firstname', '$middlename', '$owner_number', '$description', '$permitFilePath', 0)";

                if ($conn->query($sql) === TRUE) {
                    // Send a notification to the admin
                    $subject = "New Owner Sign-Up Notification";
                    $message = "A new owner has signed up and is awaiting approval.\n\nDetails:\nName: $firstname $lastname\nEmail: $email\nContact: $owner_number\nDescription: $description\nBusiness Permit: $permitFilePath";
                    mail($adminEmail, $subject, $message);

                    // Inform the user that they need to wait for approval
                    $_SESSION['email'] = $email;
                    $_SESSION['user_type'] = 'owner';
                    $message = "You have signed up successfully. Please wait for admin approval before accessing your dashboard.";
                    header("Location: owner_dashboard.php?message=" . urlencode($message)); // Redirect to owner dashboard with message
                    exit();
                } else {
                    $error = "Error: " . $conn->error;
                }
            }
        } else {
            $error = "Failed to upload the business permit.";
        }
    } else {
        $error = "Please upload a valid business permit.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Sign Up</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #ff7eb3, #ff758c);
        }
        .signup-container {
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
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            width: 100%;
            background: #ff758c;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s ease, background-color 0.3s ease;
        }
        button:hover {
            background-color: #ff406c;
            transform: scale(1.05);
        }
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .file-upload input[type="file"] {
            opacity: 0;
            width: 100%;
            height: 40px;
            position: absolute;
            cursor: pointer;
        }
        .file-upload label {
            display: block;
            background: #eee;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 14px;
            cursor: pointer;
        }
        p {
            margin-top: 20px;
            font-size: 14px;
        }
        p a {
            text-decoration: none;
            color: #ff406c;
            font-weight: bold;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="signup-container">
    <h1>Owner Sign Up</h1>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="owner_signup.php" method="POST" enctype="multipart/form-data">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" required>

        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" required>

        <label for="middlename">Middle Name</label>
        <input type="text" id="middlename" name="middlename">

        <label for="owner_number">Contact Number</label>
        <input type="text" id="owner_number" name="owner_number" required>

        <label for="business_permit">Business Permit (PDF only)</label>
        <div class="file-upload">
            <label for="business_permit">Upload PDF</label>
            <input type="file" id="business_permit" name="business_permit" accept="application/pdf" required>
        </div>


        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
