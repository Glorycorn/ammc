<?php
session_start();
require 'includes/db.php';

// Check if the tenant is logged in
if (!isset($_SESSION['tenant_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch tenant data
$tenant_id = $_SESSION['tenant_id'];
$sql = "SELECT * FROM tenants_tbl WHERE tenant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $tenant = $result->fetch_assoc();
} else {
    die("Tenant data not found.");
}

// Edit profile handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['firstname'] ?? '';
    $email = $_POST['email'] ?? '';
    $profile_picture = $_FILES['profile_picture'] ?? null;

    // Check if a new profile picture is uploaded
    if ($profile_picture && $profile_picture['error'] == 0) {
        $target_dir = "uploads/profile_pics/";
        $target_file = $target_dir . basename($profile_picture['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($profile_picture['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($profile_picture['tmp_name'], $target_file)) {
                $profile_picture_path = $target_file;
            } else {
                $profile_picture_path = $tenant['profile_picture']; // Keep old picture if upload fails
            }
        } else {
            $profile_picture_path = $tenant['profile_picture']; // Keep old picture if not an image
        }
    } else {
        $profile_picture_path = $tenant['profile_picture']; // Keep old picture if no new one is uploaded
    }

    // Update the tenant's profile in the database
    $update_sql = "UPDATE tenants_tbl SET firstname = ?, email = ?, profile_picture = ? WHERE tenant_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $name, $email, $profile_picture_path, $tenant_id);
    if ($stmt->execute()) {
        $_SESSION['tenant_name'] = $name; // Update session data
        header("Location: tenant_dashboard.php?view=profile"); // Redirect back to the profile page
        exit();
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="css/stle.css">
    <title>Edit Profile</title>
    <style>
        /* Same styles as before */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #1d3557, #457b9d);
            color: #f1faee;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }

        h1 {
            color: #f4a261;
            margin-bottom: 20px;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 50%;
            text-align: center;
        }

        .form-container input,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .form-container input[type="text"],
        .form-container input[type="email"] {
            background: rgba(255, 255, 255, 0.2);
            color: #f1faee;
            border: 1px solid #f4a261;
        }

        .form-container input[type="file"] {
            background: rgba(255, 255, 255, 0.2);
            color: #f1faee;
            border: 1px solid #f4a261;
        }

        .form-container button {
            background: #f4a261;
            color: #1d3557;
            border: none;
            font-weight: bold;
        }

        .form-container button:hover {
            background: #e76f51;
        }

        .form-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Edit Profile</h1>
    <?php if (isset($error_message)): ?>
        <p class="error-message"><?= $error_message ?></p>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-field">
            <img src="<?= htmlspecialchars($tenant['profile_picture']) ?>" alt="Profile Picture">
        </div>
        <div class="form-field">
            <label for="firstname">Full Name</label>
            <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($tenant['firstname']) ?>" required>
        </div>
        <div class="form-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($tenant['email']) ?>" required>
        </div>
        <div class="form-field">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture">
        </div>
        <div class="form-field">
            <button type="submit" name="update_profile">Update Profile</button>
        </div>
    </form>
</div>

</body>
</html>
