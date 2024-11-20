<?php
session_start();
require 'includes/db.php';

// Ensure owner is logged in
if (!isset($_SESSION['owner_number']) || $_SESSION['user_type'] != 'owner') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $price = $_POST['price'];
    $owner_number = $_SESSION['owner_number'];
    $status = $_POST['status'];
    $recommended = isset($_POST['recommended']) ? 1 : 0;

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert accommodation details into `accomodation_tbl`
        $insert_sql = "INSERT INTO accomodation_tbl (category_id, description, address, price, status, owner_number) 
               VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("issdsi", $category_id, $description, $address, $price, $status, $owner_number);
        $stmt->execute();

        // Get the inserted accommodation ID
        $accommodation_id = $conn->insert_id;

        // Debugging line to check the accommodation ID
        echo "Accommodation ID: " . $accommodation_id; // Debug line

        // Handle multiple image uploads
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (isset($_FILES['images']['name']) && count($_FILES['images']['name']) > 0) {
            foreach ($_FILES['images']['name'] as $key => $filename) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['images']['tmp_name'][$key];
                    $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $new_filename = uniqid("img_", true) . "." . $file_ext;
                    $file_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($file_tmp, $file_path)) {
                        // Insert image path into `accommodation_images` table
                        $img_sql = "INSERT INTO accommodation_images (accommodation_id, image_path) VALUES (?, ?)";
                        $img_stmt = $conn->prepare($img_sql);
                        $img_stmt->bind_param("is", $accommodation_id, $file_path);
                        $img_stmt->execute();
                    } else {
                        throw new Exception("Failed to upload file: $filename");
                    }
                }
            }
        } else {
            throw new Exception("No images uploaded.");
        }

        // Commit transaction
        $conn->commit();
        header("Location: owner_dashboard.php?success=Accommodation added successfully");
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log($e->getMessage(), 3, "error_log.txt");
        echo "Error: " . $e->getMessage();
    }
}
?>
