<?php
include 'db_connection.php';

// Fetch properties with their first image
$sql = "
    SELECT 
        p.id, 
        p.title, 
        p.price, 
        p.location,
        p.address,
        p.garage,
        p.bedrooms,
        p.size, 
        p.status, 
        p.created_at, 
        p.property_type,  
        p.selling_type,  
        (
            SELECT image_path 
            FROM property_images 
            WHERE property_id = p.id 
            ORDER BY id ASC LIMIT 1
        ) AS main_image,
        (
            SELECT COUNT(*) 
            FROM property_images 
            WHERE property_id = p.id
        ) AS image_count
    FROM 
        properties p
    ORDER BY 
        p.created_at DESC
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
    <title>Property Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script>
        // JavaScript to display a success message if the URL has ?insert=success
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('insert') === 'success') {
                alert('Property added successfully!');
            }
        };
    </script>
</head>

<?php if (isset($_SESSION['message'])): ?>
    <script>alert('<?php echo $_SESSION['message']; ?>');</script>
    <?php unset($_SESSION['message']); // Clear the message so it doesn't display again ?>
<?php endif; ?>

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
                <li><a href="agents.php" class="active"><i class="fas fa-user-tie"></i> Agents</a></li>
                <li><a href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="view_contact_messages.php"><i class="fas fa-envelope"></i> View Contact Messages</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h1>Property Management</h1>
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
    <h2>Manage Properties</h2>
    <button class="add-btn" onclick="openAddPropertyModal()">Add Property</button>
    <div class="property-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="property-item" data-property-id="<?php echo $row['id']; ?>">
                    <div class="property-image">
                        <?php if ($row['main_image']): ?>
                            <img src="<?php echo htmlspecialchars($row['main_image']); ?>" alt="Property Image">
                        <?php else: ?>
                            <i class="fas fa-folder"></i> <!-- Folder icon for no image -->
                        <?php endif; ?>

                        <?php if ($row['image_count'] > 1): ?>
                            <span class="image-count"><?php echo $row['image_count']; ?> Images</span>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Price:</strong> R<?php echo htmlspecialchars($row['price']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                    <p><strong>Bedrooms:</strong> <?php echo htmlspecialchars($row['bedrooms']); ?></p>
                    <p><strong>Garages:</strong> <?php echo htmlspecialchars($row['garage']); ?></p>
                    <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                    <p><strong>Property Type:</strong> <?php echo htmlspecialchars($row['property_type']); ?></p>
                    <p><strong>Selling Type:</strong> <?php echo htmlspecialchars($row['selling_type']); ?></p>
                    <p><small>Added on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                    <div class="property-actions">
                        <button class="edit-btn" onclick="openEditPropertyModal(<?php echo $row['id']; ?>)">Edit</button>
                        <form action="delete_property.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this property?');" style="display: inline;">
                            <input type="hidden" name="property_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No properties found.</p>
        <?php endif; ?>
    </div>
</section>

        </main>
    </div>

    <!-- Modals for adding and editing properties -->
    <div id="addPropertyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddPropertyModal()">&times;</span>
            <h2>Add Property</h2>
            <form id="addPropertyForm" method="POST" action="add_property.php" enctype="multipart/form-data">
                <input type="hidden" id="editPropertyId" name="editPropertyId">
                <label for="propertyImages">Upload Images:</label>
                <input type="file" id="propertyImages" name="propertyImages[]" multiple>
                <label for="propertyTitle">Title:</label>
                <input type="text" id="propertyTitle" name="propertyTitle" required>
                <label for="propertyDescription">Description:</label>
                <textarea id="propertyDescription" name="propertyDescription" required></textarea>
                <label for="propertyPrice">Price:</label>
                <input type="number" id="propertyPrice" name="propertyPrice" required>
                <label for="propertyLocation">Location:</label>
                <input type="text" id="propertyLocation" name="propertyLocation" required>
                <label for="propertyAddress">Address:</label>
                <input type="text" id="propertyAddress" name="propertyAddress" required>
                <label for="propertyBedrooms">Bedrooms:</label>
                <input type="text" id="propertyBedrooms" name="propertyBedrooms" required>
                <label for="propertyGarage">Garages:</label>
                <input type="text" id="propertyGarages" name="propertyGarages" required>
                <label for="propertySize">Size:</label>
                <input type="text" id="propertySize" name="propertySize" required>
                <label for="propertyStatus">Status:</label>
                <select id="propertyStatus" name="propertyStatus" required>
                    <option value="available">Available</option>
                    <option value="sold">Sold</option>
                    <option value="pending">Pending</option>
                </select>
                <label for="propertySellingType">Selling Type:</label>
                <select id="propertySellingType" name="propertySellingType" required>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
                <label for="propertyType">Property Type:</label>
                <select id="propertyType" name="propertyType" required>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                </select>

                <button type="submit">Add Property</button>
            </form>
        </div>
    </div>


    
    <!-- Edit Property Modal -->
    <div id="editPropertyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditPropertyModal()">&times;</span>
            <h2>Edit Property</h2>
            <form id="editPropertyForm" method="POST" action="edit_property.php" enctype="multipart/form-data">
            <div class="form-section">
                <input type="hidden" id="editPropertyId" name="editPropertyId">
                <label for="editPropertyImages">Upload New Images (Optional):</label>
                <input type="file" id="editPropertyImages" name="editPropertyImages[]" multiple>
                <label for="editPropertyTitle">Title:</label>
                <input type="text" id="editPropertyTitle" name="editPropertyTitle" >
                <label for="editPropertyDescription">Description:</label>
                <textarea id="editPropertyDescription" name="editPropertyDescription" ></textarea>
                <label for="editPropertyPrice">Price:</label>
                <input type="number" id="editPropertyPrice" name="editPropertyPrice" >
                <label for="editPropertyLocation">Location:</label>
                <input type="text" id="editPropertyLocation" name="editPropertyLocation" >
                <label for="editPropertyAddress">Address:</label>
                <input type="text" id="editPropertyAddress" name="editPropertyAddress" >
                <label for="editPropertyBedrooms">Bedrooms:</label>
                <input type="text" id="editPropertyBedrooms" name="editPropertyBedrooms" >
                <label for="editPropertyGarage">Garages:</label>
                <input type="text" id="editPropertyGarages" name="editPropertyGarages" >
                <label for="editPropertySize">Size:</label>
                <input type="text" id="editPropertySize" name="editPropertySize" >
                <label for="editPropertyStatus">Status:</label>
                <select id="editPropertyStatus" name="editPropertyStatus" >
                    <option value="available">Available</option>
                    <option value="sold">Sold</option>
                    <option value="pending">Pending</option>
                </select>
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
                </div>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>



