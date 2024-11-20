<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userType = $_POST['user_type'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];

    if ($userType === 'tenant') {
        $contactnum = $_POST['contactnum'];

        // Check if the email already exists
        $checkEmail = "SELECT * FROM tenants_tbl WHERE email = '$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $sql = "INSERT INTO tenants_tbl (email, password, lastname, firstname, middlename, contactnum)
                    VALUES ('$email', '$password', '$lastname', '$firstname', '$middlename', '$contactnum')";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['email'] = $email;
                $_SESSION['user_type'] = 'tenant';
                header("Location: tenant_dashboard.php");
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    } elseif ($userType === 'owner') {
        $ownerNumber = $_POST['owner_number'];
        $description = $_POST['description'];

        if (isset($_FILES['business_permit']) && $_FILES['business_permit']['error'] == 0) {
            $permitFile = $_FILES['business_permit'];
            $permitFileName = basename($permitFile['name']);
            $permitFilePath = 'uploads/' . $permitFileName;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($permitFile['tmp_name'], $permitFilePath)) {
                $checkEmail = "SELECT * FROM owners_tbl WHERE email = '$email'";
                $result = $conn->query($checkEmail);

                if ($result->num_rows > 0) {
                    $error = "Email already registered!";
                } else {
                    $sql = "INSERT INTO owners_tbl (email, password, lastname, firstname, middlename, owner_number, description, business_permit, approved)
                            VALUES ('$email', '$password', '$lastname', '$firstname', '$middlename', '$ownerNumber', '$description', '$permitFilePath', 0)";

                    if ($conn->query($sql) === TRUE) {
                        $_SESSION['email'] = $email;
                        $_SESSION['user_type'] = 'owner';
                        $message = "Sign up successful. Await admin approval.";
                        header("Location: owner_dashboard.php?message=" . urlencode($message));
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #ff7eb3, #ff758c);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .signup-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
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
            font-weight: bold;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            color: #333;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background: #ff758c;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: #ff406c;
            transform: scale(1.05);
        }

        p {
            margin-top: 15px;
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

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }

        #tenant-fields, #owner-fields {
            display: none;
        }

        .file-upload {
            position: relative;
            text-align: left;
        }

        .file-upload input[type="file"] {
            opacity: 0;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload label {
            display: inline-block;
            width: 100%;
            padding: 10px;
            background: #eee;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            transition: background 0.3s ease;
        }

        .file-upload label:hover {
            background: #ddd;
        }
    </style>
    <script>
        function toggleFields() {
            const userType = document.getElementById('user_type').value;
            const tenantFields = document.getElementById('tenant-fields');
            const ownerFields = document.getElementById('owner-fields');

            tenantFields.style.display = userType === 'tenant' ? 'block' : 'none';
            ownerFields.style.display = userType === 'owner' ? 'block' : 'none';
        }
    </script>
</head>
<body>
<div class="signup-container">
    <h1>Sign Up</h1>

    <?php if (isset($error)): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="signup.php" method="POST" enctype="multipart/form-data">
        <label for="user_type">User Type</label>
        <select id="user_type" name="user_type" onchange="toggleFields()" required>
            <option value="" disabled selected>Select user type</option>
            <option value="tenant">Tenant</option>
            <option value="owner">Owner</option>
        </select>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>

        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" required>

        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" required>

        <label for="middlename">Middle Name</label>
        <input type="text" id="middlename" name="middlename" placeholder="Enter your middle name (optional)">

        <div id="tenant-fields">
            <label for="contactnum">Contact Number</label>
            <input type="text" id="contactnum" name="contactnum" placeholder="Enter your contact number">
        </div>

        <div id="owner-fields">
            <label for="owner_number">Contact Number</label>
            <input type="text" id="owner_number" name="owner_number" placeholder="Enter your contact number">

            <label for="business_permit">Business Permit (PDF only)</label>
            <div class="file-upload">
                <label for="business_permit">Upload Business Permit</label>
                <input type="file" id="business_permit" name="business_permit" accept="application/pdf">
            </div>
        </div>

        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>

