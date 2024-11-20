    <?php
    session_start();
    require 'includes/db.php';

    // Ensure owner is logged in
    if (!isset($_SESSION['owner_number']) || $_SESSION['user_type'] != 'owner') {
        header("Location: login.php");
        exit();
    }

    // Fetch owner information
    $email = $_SESSION['email'];
    $sql = "SELECT * FROM owners_tbl WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $owner = $result->fetch_assoc();

    // Check if owner is approved
    if ($owner['approved'] == 0) {
        echo "<p>Your account is pending approval by the admin. Please check back later.</p>";
        exit();
    }


    $owner_number = $_SESSION['owner_number'];

    $sql = "
    SELECT a.*, c.category_name 
    FROM accomodation_tbl a
    JOIN category_tbl c ON a.category_id = c.category_id
    WHERE a.owner_number = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $owner_number);
    $stmt->execute();
    $accommodations_result = $stmt->get_result();


    // Fetch reservations
    $reservations_sql = "SELECT r.*, t.firstname, t.lastname, a.description, a.price
                        FROM reservation_tbl r
                        JOIN tenants_tbl t ON r.tenant_id = t.tenant_id
                        JOIN accomodation_tbl a ON r.accomodation_id = a.accomodation_id
                        WHERE a.owner_number = ?";
    $stmt = $conn->prepare($reservations_sql);
    $stmt->bind_param("i", $owner['owner_number']);
    $stmt->execute();
    $reservations_result = $stmt->get_result();

    // Fetch payments
    $payments_sql = "SELECT p.*, t.firstname, t.lastname, a.description
                    FROM payments_tbl p
                    JOIN reservation_tbl r ON p.reservation_id = r.reservation_id
                    JOIN tenants_tbl t ON r.tenant_id = t.tenant_id
                    JOIN accomodation_tbl a ON r.accomodation_id = a.accomodation_id
                    WHERE a.owner_number = ?";
    $stmt = $conn->prepare($payments_sql);
    $stmt->bind_param("i", $owner['owner_number']);
    $stmt->execute();
    $payments_result = $stmt->get_result();

    $categories_sql = "SELECT category_id, category_name FROM category_tbl WHERE category_name IN ('Apartment', 'Boarding House')";
    $categories_result = $conn->query($categories_sql);
    ?>

    <!DOCTYPE html>
    <html>
    <head>
    <link rel="shortcut icon" type="x-icon" href="./images/ammc.png">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Owner Dashboard</title>
        <style>
            .image-row {
    display: flex; /* Enables flexbox */
    flex-wrap: wrap; /* Allows wrapping if images exceed the container width */
    gap: 10px; /* Adds space between images */
    justify-content: center; /* Centers images horizontally in the parent container */
    align-items: center; /* Centers images vertically (if necessary) */
}

.image-row img {
    display: block; /* Ensures proper image rendering */
    max-width: 100px; /* Set a max-width for images */
    height: auto; /* Maintain aspect ratio */
}
.action-buttons {
    display: flex; /* Arrange buttons side-by-side */
    gap: 10px; /* Add spacing between buttons */
}

.action-buttons form {
    margin: 0; /* Remove default form margin */
}

.action-buttons button {
    padding: 5px 10px; /* Add padding for better appearance */
    font-size: 14px; /* Adjust font size */
    border: none; /* Remove border for cleaner look */
    border-radius: 5px; /* Add rounded corners */
    cursor: pointer; /* Change cursor to pointer on hover */
}

.action-buttons .btn-primary {
    background-color: #007bff; /* Update button color for "Update" */
    color: white; /* Ensure text is readable */
}

.action-buttons .btn-primary:hover {
    background-color: #0056b3; /* Darker shade on hover */
}

.action-buttons .btn-danger {
    background-color: #dc3545; /* Update button color for "Delete" */
    color: white;
}

.action-buttons .btn-danger:hover {
    background-color: #c82333; /* Darker shade on hover */
}

        </style>
    </head>
    <body>

    <h1>Welcome, <?php echo htmlspecialchars($owner['email']); ?></h1>

    <section>
        <h2>Your Accommodations</h2>
        <?php if ($accommodations_result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Address</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Image</th> 
                    <th>Actions</th>
                </tr>
                <?php while ($accommodation = $accommodations_result->fetch_assoc()) { ?>
                    <tr>
                    <td><?php echo htmlspecialchars($accommodation['category_name']); ?></td>
                <td><?php echo htmlspecialchars($accommodation['description']); ?></td>
                <td><?php echo htmlspecialchars($accommodation['address']); ?></td>
                <td><?php echo htmlspecialchars(number_format($accommodation['price'], 2)); ?></td>
                <td><?php echo htmlspecialchars($accommodation['status'] === 'Available' ? 'Available' : 'Unavailable'); ?></td>
                <td>
                    <?php
                    $image_sql = "SELECT image_path FROM accommodation_images WHERE accommodation_id = ?";
                    $image_stmt = $conn->prepare($image_sql);
                    $image_stmt->bind_param("i", $accommodation['accomodation_id']);
                    $image_stmt->execute();
                    $images = $image_stmt->get_result();
                    ?>
                    <div class="image-row">
                        <?php while ($image = $images->fetch_assoc()) { ?>
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Accommodation Image">
                        <?php } ?>
                    </div>
                </td>
                <td>
                        <div class="action-buttons">
                            <form action="update_accommodation.php" method="get" style="display:inline;">
                                <input type="hidden" name="accomodation_id" value="<?php echo htmlspecialchars($accommodation['accomodation_id']); ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                            <form action="delete_accommodation.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this accommodation?');">
                                <input type="hidden" name="accomodation_id" value="<?php echo htmlspecialchars($accommodation['accomodation_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>

                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>You have no accommodations listed at the moment.</p>
        <?php } ?>
    </section>


    <section>
    <h2>Add New Accommodation</h2>
    <form action="upload_accommodation.php" method="POST" enctype="multipart/form-data">
        <label>Category:</label>
        <select name="category_id" required>
        <option value="" disabled selected>Select category</option>
            <?php while ($category = $categories_result->fetch_assoc()) { ?>
                <option value="<?php echo htmlspecialchars($category['category_id']); ?>">
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </option>
            <?php } ?>
        </select><br>

        <label>Description:</label>
        <textarea id="description" name="description" rows="4" placeholder="Provide details about your accommodation..."></textarea><br>

        <label>Address:</label>
        <input type="text" name="address" required><br>

        <label>Price:</label>
        <input type="number" name="price" required><br>

        <label>Status:</label>
        <select name="status">
            <option value="Available">Available</option>
            <option value="Unavailable">Unavailable</option>
        </select><br>

        <label>Recommended:</label>
        <input type="checkbox" name="recommended" value="1"><br>

        <label>Images:</label>
        <input type="file" name="images[]" accept="image/*" multiple required><br>

        <button type="submit">Add Accommodation</button>
    </form>
</section>


    <section class="payments">
        <h2>Payments for Your Accommodations</h2>
        <?php if ($payments_result->num_rows > 0) { ?>
            <table>
                <tr>
                    <th>Tenant Name</th>
                    <th>Accommodation</th>
                    <th>Payment Code</th>
                    <th>Payment Date</th>
                </tr>
                <?php while ($payment = $payments_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['firstname']) . ' ' . htmlspecialchars($payment['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($payment['description']); ?></td>
                        <td><?php echo htmlspecialchars($payment['code_numb']); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <p>No payments have been made yet for your accommodations.</p>
        <?php } ?>
    </section>

    <section class="messages-reports">
        <h2>Send Message or Report an Issue</h2>
        <form action="send_message_report.php" method="POST">
            <label for="message_type">Message Type:</label>
            <select name="message_type" id="message_type">
                <option value="message">Message</option>
                <option value="report">Report</option>
            </select><br>

            <label for="content">Message/Report Content:</label><br>
            <textarea name="content" id="content" rows="4" cols="50" required></textarea><br>
            
            <button type="submit">Send</button>
        </form>
    </section>


    </body>
    </html>
