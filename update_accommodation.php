<?php
require 'includes/db.php';

// Fetch all categories for the dropdown
$categories_sql = "SELECT category_id, category_name FROM category_tbl";
$categories_result = $conn->query($categories_sql);

// If an accommodation ID is provided, fetch its details
if (isset($_GET['accomodation_id'])) {
    $id = intval($_GET['accomodation_id']);
    $result = $conn->query("SELECT * FROM accomodation_tbl WHERE accomodation_id = $id");
    $accommodation = $result->fetch_assoc();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['accomodation_id']);
    $category_id = $_POST['category_id'];  // New category selection
    $description = $_POST['description'];
    $address = $_POST['address'];
    $price = $_POST['price'];  // Ensure the price is passed correctly
    $status = $_POST['status'];  // Ensure the status is passed correctly
    $image_path = $accommodation['image_path']; // Retain current image path by default

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $image_dir = 'uploads/';
    $image_name = basename($_FILES['image']['name']);
    $target_file = $image_dir . $image_name;

    // Move uploaded file to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $target_file; // Update the path only if upload succeeds
    } else {
        echo "Error uploading image.";
    }
}

    

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE accomodation_tbl SET category_id = ?, description = ?, address = ?, price = ?, status = ?, image_path = ? WHERE accomodation_id = ?");
    $stmt->bind_param("issdssi", $category_id, $description, $address, $price, $status, $image_path, $id);
    

    if ($stmt->execute()) {
        echo "Accommodation updated successfully."; // Debug message
        header("Location: owner_dashboard.php");
        exit;
    } else {
        echo "Error updating accommodation.";
    }

    $stmt->close();
}
?>

<form action="update_accommodation.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="accomodation_id" value="<?php echo htmlspecialchars($accommodation['accomodation_id']); ?>">

    <label>Category:</label>
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php while ($category = $categories_result->fetch_assoc()) { ?>
            <option value="<?php echo htmlspecialchars($category['category_id']); ?>"
                <?php echo ($accommodation['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['category_name']); ?>
            </option>
        <?php } ?>
    </select><br>

    <label>Description:</label>
    <input type="text" name="description" value="<?php echo htmlspecialchars($accommodation['description']); ?>"><br>

    <label>Address:</label>
    <input type="text" name="address" value="<?php echo htmlspecialchars($accommodation['address']); ?>"><br>

    <label>Price:</label>
    <input type="number" name="price" value="<?php echo htmlspecialchars($accommodation['price']); ?>" step="0.01" required><br>

    <label>Status:</label>
    <select name="status" required>
    <option value="Available" <?php echo (strtolower($accommodation['status']) === 'available') ? 'selected' : ''; ?>>Available</option>
<option value="Unavailable" <?php echo (strtolower($accommodation['status']) === 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>

    </select><br>

    <label>Current Image:</label><br>
    <?php if (!empty($accommodation['image_path'])) { ?>
        <img src="<?php echo htmlspecialchars($accommodation['image_path']); ?>" alt="Accommodation Image" width="100"><br>
    <?php } else { ?>
        <p>No image available.</p>
    <?php } ?>

    <label>Update Image:</label>
    <input type="file" name="image"><br>

    <button type="submit" name="update">Update</button>
</form>
