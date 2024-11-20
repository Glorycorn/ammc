    <?php
    session_start();
    require '../includes/db.php';

    // Ensure only admin can access this page
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
        header("Location: admin_login.php");
        exit();
    }
    $adminDetailsQuery = "SELECT * FROM admin_tbl WHERE admin_id = ?";
    $stmt = $conn->prepare($adminDetailsQuery);
    $stmt->bind_param("i", $_SESSION['admin_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $adminDetails = $result->fetch_assoc();

    if (!$adminDetails) {
        error_log("No admin details found for admin_id: " . $_SESSION['admin_id']);
    }
    $stmt->close();

    // Fetch unapproved owners
    $unapprovedOwnersQuery = "SELECT * FROM owners_tbl WHERE approved = 0";
    $unapprovedOwners = $conn->query($unapprovedOwnersQuery);

    // Fetch approved owners
    $approvedOwnersQuery = "SELECT * FROM owners_tbl WHERE approved = 1";
    $approvedOwners = $conn->query($approvedOwnersQuery);

    // Fetch owner postings
    $postingsQuery = "SELECT * FROM accomodation_tbl";
    $postings = $conn->query($postingsQuery);

    // Fetch messages and reports
    $messagesQuery = "SELECT * FROM messages_tbl";
    $messages = $conn->query($messagesQuery);

    // Approve owner
    if (isset($_GET['approve_owner'])) {
        $ownerId = $_GET['approve_owner'];
        $approveQuery = "UPDATE owners_tbl SET approved = 1 WHERE owner_id = ?";
        $stmt = $conn->prepare($approveQuery);
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php?approval_success=1");
        exit();
    }



    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <link rel="shortcut icon" type="x-icon" href="../images/Ammc.png">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="../css/style.css">
        <style>
            /* Basic reset */
            body, html {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }

            /* Sidebar */
            .sidebar {
                height: 100%;
                width: 220px;
                position: fixed;
                top: 0;
                left: 0;
                background-color: #333;
                padding-top: 20px;
                color: white;
                transition: 0.3s;
                display: flex;
                flex-direction: column;
            }

            .sidebar a {
                padding: 12px 16px;
                text-decoration: none;
                font-size: 18px;
                color: white;
                display: block;
                transition: 0.3s;
                margin-bottom: 10px;
            }

            .sidebar a:hover {
                background-color: #575757;
                border-radius: 5px;
            }

            .content-container {
                margin-left: 240px;
                padding: 20px;
                width: calc(100% - 240px);
                transition: margin-left 0.3s;
            }

            h1, h2 {
                text-align: center;
                color: #333;
            }

            h2 {
                margin-top: 30px;
            }

            /* Success Message */
            .message {
                color: green;
                font-size: 16px;
                margin-top: 20px;
                text-align: center;
                display: block;
            }

            /* Button styles */
            .btn-approve {
                padding: 8px 15px;
                background-color: #28a745;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .btn-approve:hover {
                background-color: #218838;
                transform: scale(1.05);
            }

            /* Table styles */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            table th, table td {
                padding: 12px;
                text-align: center;
                border: 1px solid #ddd;
            }

            table th {
                background-color: #ff758c;
                color: white;
            }

            table tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            table tr:hover {
                background-color: #f5f5f5;
            }

            /* Animation for fade-in */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }

            /* Responsive design */
            @media (max-width: 768px) {
                .sidebar {
                    width: 200px;
                }
                .content-container {
                    margin-left: 200px;
                    padding: 15px;
                }
            }

            /* Admin Profile Styles */
    .admin-profile {
        text-align: center;
        color: white;
        border-bottom: 1px solid #575757;
        padding-bottom: 20px;
    }

    .admin-profile img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    .admin-profile h3 {
        margin: 0;
        font-size: 18px;
    }

    .admin-profile p {
        margin: 0;
        font-size: 14px;
        color: #ddd;
    }
    .btn-edit, .btn-delete {
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 14px;
    margin-right: 10px;
    display: inline-block;
}

.btn-edit {
    background-color: #007bff;
    color: white;
}

.btn-edit:hover {
    background-color: #0056b3;
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-delete:hover {
    background-color: #c82333;
}


        </style>
    </head>
    <body>

    <div class="sidebar">
        <!-- Admin Profile -->
        <div class="admin-profile" style="text-align: center; margin-bottom: 20px;">
            <img src="../images/profile.png" alt="Admin Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px;">
            <?php
    // Provide fallback values for admin name and email
    $adminName = isset($adminDetails['name']) ? htmlspecialchars($adminDetails['name']) : 'Unknown Admin';
    $adminEmail = isset($adminDetails['email']) ? htmlspecialchars($adminDetails['email']) : 'Not Set';
    ?>

    <h3 style="margin: 0; font-size: 18px;"><?php echo $adminName; ?></h3>
    <p style="margin: 0; font-size: 14px; color: #ddd;"><?php echo $adminEmail; ?></p>

        </div>

        <!-- Sidebar Links -->
        <a href="?view=approve_owners">Approve Owners</a>
        <a href="?view=approved_owners">Approved Owners</a>
        <a href="?view=owner_postings">Owner Postings</a>
        <a href="?view=messages_reports">Messages & Reports</a>

    </div>

    <div class="content-container">

        <h1>Admin Dashboard</h1>

        <?php if (isset($_GET['approval_success'])): ?>
            <p class="message">Owner approved successfully.</p>
        <?php endif; ?>
        <?php if (isset($_GET['profile_update'])): ?>
            <p class="message">Profile updated successfully.</p>
        <?php endif; ?>

        <?php 
        // Show content based on the selected view
        if (isset($_GET['view'])) {
            $view = $_GET['view'];

        // Approve Owners View
    if ($view == 'approve_owners') {
        echo "<h2>Approve New Owner Sign-Ups</h2>";
        if ($unapprovedOwners->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Owner Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>";
            while ($row = $unapprovedOwners->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['owner_name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>
                            <a href='?approve_owner=" . $row['owner_id'] . "' class='btn-approve'>Approve</a>
                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No unapproved owners found.</p>";
        }
    }


// For Approved Owners
if ($view == 'approved_owners') {
    echo "<h2>Approved Owners</h2>";
    // Fetch approved owners
        $approvedOwnersQuery = "SELECT firstname, lastname, email, owner_number, business_permit 
        FROM owners_tbl 
        WHERE approved = 1";
        $approvedOwners = $conn->query($approvedOwnersQuery);

    if ($approvedOwners->num_rows > 0) {
        echo "<table>
                <tr>    
                    <th>Owner Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Business Permit</th>
                </tr>";
        while ($row = $approvedOwners->fetch_assoc()) {
            // Check if firstname and lastname are set, else provide fallback
            $ownerName = isset($row['firstname']) && isset($row['lastname']) 
                ? htmlspecialchars($row['firstname'] . " " . $row['lastname']) 
                : 'Unknown Owner';
            
            // Check if the business_permit is set, else provide fallback
            $businessPermit = isset($row['business_permit']) 
            ? "<a href='uploads/permits/" . htmlspecialchars($row['business_permit']) . "' target='_blank'>View Permit</a>" 
            : 'No Permit Provided';
        

            echo "<tr>
                    <td>" . $ownerName . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['owner_number']) . "</td>
                    <td>" . $businessPermit . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No approved owners found.</p>";
    }
}


elseif ($view == 'owner_postings') {
    echo "<h2>Owner Postings</h2>";

    // Join the accommodation table with the owners table to get owner details
    $postingsQuery = "SELECT a.*, o.firstname, o.lastname 
                      FROM accomodation_tbl a
                      LEFT JOIN owners_tbl o ON a.owner_id = o.owner_id";
    $postings = $conn->query($postingsQuery);

    if ($postings->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Accommodation Title</th>
                    <th>Owner Name</th>
                    <th>Location</th>
                    <th>Price</th>
                </tr>";
        while ($row = $postings->fetch_assoc()) {
            // Concatenate firstname and lastname to form full name
            $ownerName = isset($row['firstname']) && isset($row['lastname']) 
                         ? htmlspecialchars($row['firstname'] . " " . $row['lastname']) 
                         : 'Unknown Owner';

            echo "<tr>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td>" . $ownerName . "</td>
                    <td>" . htmlspecialchars($row['address']) . "</td>
                    <td>" . htmlspecialchars($row['price']) . "</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No postings found.</p>";
    }
}



elseif ($view == 'owner_postings') {
    echo "<h2>Owner Postings</h2>";
    if ($postings->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Accommodation Title</th>
                    <th>Owner Name</th>
                    <th>Location</th>
                    <th>Price</th>
                  
                </tr>";
        while ($row = $postings->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['title']) . "</td>
                    <td>" . htmlspecialchars($row['firstname'] . " " . $row['lastname']) . "</td>
                    <td>" . htmlspecialchars($row['location']) . "</td>
                    <td>" . htmlspecialchars($row['price']) . "</td>
                    
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No postings found.</p>";
    }
}


            // Messages & Reports View
            elseif ($view == 'messages_reports') {
                echo "<h2>Messages & Reports</h2>";
                // Messages table
            }

        
        }
        ?>

    </div>

    </body>
    </html>