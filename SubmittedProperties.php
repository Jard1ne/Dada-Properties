<?php
include 'db_connection.php';

// Fetch submitted properties from the database
$sql = "
    SELECT 
        property_submissions.id, 
        property_submissions.full_name, 
        property_submissions.email, 
        property_submissions.phone_number, 
        property_submissions.title, 
        property_submissions.location, 
        property_submissions.price, 
        property_submissions.selling_type, 
        property_submissions.property_type, 
        property_submissions.bedrooms, 
        property_submissions.bathrooms,
        property_submissions.garage,
        property_submissions.size, 
        property_submissions.description, 
        property_submissions.images, 
        property_submissions.created_at
    FROM 
        property_submissions 
    ORDER BY 
        created_at DESC
";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Properties Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="properties.php"><i class="fas fa-building"></i> Properties</a></li>
                <li><a href="SubmittedProperties.php"><i class="fas fa-file-alt"></i> Submitted Properties</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="agents.php"><i class="fas fa-user-tie"></i> Agents</a></li>
                <li><a href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="view_contact_messages.php"><i class="fas fa-envelope"></i> View Contact Messages</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h1>Submitted Properties Management</h1>
                </div>
                <div class="topbar-right">
                    <div class="notifications">
                        
                    </div>
                    <div class="user-profile">
                        <img src="profile.jpg" alt="User">
                        <span>Admin</span>
                        <a href="logout.php" class="logout-btn">Logout</a> <!-- Add a logout button -->
                    </div>
                </div>
            </header>
            <section class="properties-section">
                <h2>Manage Submitted Properties</h2>
                <div class="property-list">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="property-item" data-property-id="<?php echo $row['id']; ?>">
                                <div class="property-image">
                                    <?php
                                    $images = explode(",", $row['images']);
                                    if (count($images) > 0 && !empty($images[0])): ?>
                                        <img src="C:/wamp64/www/uploads<?php echo htmlspecialchars($images[0]); ?>" alt="Property Image">
                                    <?php else: ?>
                                        <i class="fas fa-folder"></i> <!-- Folder icon for no image -->
                                    <?php endif; ?>
                                    <?php if (count($images) > 1): ?>
                                        <span class="image-count"><?php echo count($images); ?> Images</span>
                                    <?php endif; ?>
                                </div>
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p><strong>Submitted by:</strong> <?php echo htmlspecialchars($row['full_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone_number']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                <p><strong>Property Type:</strong> <?php echo htmlspecialchars($row['property_type']); ?></p>
                                <p><strong>Selling Type:</strong> <?php echo htmlspecialchars($row['selling_type']); ?></p>
                                <p><strong>Price:</strong> R<?php echo htmlspecialchars($row['price']); ?></p>
                                <p><strong>Bedrooms:</strong> <?php echo htmlspecialchars($row['bedrooms']); ?></p>
                                <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($row['bathrooms']); ?></p>
                                <p><strong>Garage:</strong> <?php echo htmlspecialchars($row['garage']); ?></p>
                                <p><strong>Property Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>
                                <p><small>Submitted on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>

                                <div class="property-actions">

                                <!-- Add to Properties Button -->
                                    <form action="add_to_properties.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="submission_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="add-btn">Add to Properties</button>
                                    </form>

                               <!-- <button class="edit-btn" onclick="openEditPropertyModal(<?php echo $row['id']; ?>)">Edit</button>-->

                                    <form action="delete_submitted_property.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this property submission?');" style="display: inline;">
                                        <input type="hidden" name="property_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>

                                </div>


                                
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No submitted properties found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Modals for editing properties -->
    <div id="editPropertyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditPropertyModal()">&times;</span>
            <h2>Edit Submitted Property</h2>
            <form id="editPropertyForm" method="POST" action="edit_submitted_property.php">
                <input type="hidden" id="editPropertyId" name="editPropertyId">
                <label for="editPropertyTitle">Title:</label>
                <input type="text" id="editPropertyTitle" name="editPropertyTitle" required>
                <label for="editPropertyDescription">Description:</label>
                <textarea id="editPropertyDescription" name="editPropertyDescription" required></textarea>
                <label for="editPropertyPrice">Price:</label>
                <input type="number" id="editPropertyPrice" name="editPropertyPrice" required>
                <label for="editPropertyLocation">Location:</label>
                <input type="text" id="editPropertyLocation" name="editPropertyLocation" required>
                <label for="editPropertyBedrooms">Bedrooms:</label>
                <input type="number" id="editPropertyBedrooms" name="editPropertyBedrooms" required>
                <label for="editPropertyBathrooms">Bathrooms:</label>
                <input type="number" id="editPropertyBathrooms" name="editPropertyBathrooms" required>
                <label for="editPropertySellingType">Selling Type:</label>
                <select id="editPropertySellingType" name="editPropertySellingType" required>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
                <label for="editPropertyType">Property Type:</label>
                <select id="editPropertyType" name="editPropertyType" required>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                </select>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
