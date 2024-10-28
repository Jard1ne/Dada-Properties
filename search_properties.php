<?php
include 'db_connection.php';

// Get search filters from the form
$property_type = isset($_GET['property_type']) ? $_GET['property_type'] : null;
$location = isset($_GET['location']) ? $_GET['location'] : null;
$size = isset($_GET['size']) ? intval($_GET['size']) : null;
$bedrooms = isset($_GET['bedrooms']) ? intval($_GET['bedrooms']) : null;
$bathrooms = isset($_GET['bathrooms']) ? intval($_GET['bathrooms']) : null;  // Added bathrooms
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : null;
$location = isset($_GET['location']) ? intval($_GET['location']) : null;

// Base query with JOIN to fetch one image for each property
$query = "
    SELECT 
        properties.*, 
        MIN(property_images.image_path) AS main_image -- Select the first image
    FROM 
        properties 
    LEFT JOIN 
        property_images ON properties.id = property_images.property_id
    WHERE 1=1
";

// Add conditions to the query based on filters
if (!empty($property_type)) {
    $query .= " AND properties.property_type = '" . $conn->real_escape_string($property_type) . "'";
}
if (!empty($location)) {
    $query .= " AND properties.location = '" . $conn->real_escape_string($location) . "'";
}
if (!empty($size)) {
    $query .= " AND properties.size >= " . intval($size);
}
if (!empty($bedrooms)) {
    $query .= " AND properties.bedrooms >= " . intval($bedrooms);
}
if (!empty($bathrooms)) {
    $query .= " AND properties.bathrooms >= " . intval($bathrooms);  // Added bathrooms condition
}
if (!empty($min_price)) {
    $query .= " AND properties.price >= " . intval($min_price);
}
if (!empty($max_price)) {
    $query .= " AND properties.price <= " . intval($max_price);
}
if (!empty($location)) {
    $query .= " AND properties.location <= " . intval($max_price);
}

// Group by property to ensure only one image is selected per property
$query .= " GROUP BY properties.id";

// Execute the query
$result = $conn->query($query);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

// If a single property is found, redirect to its details page
if ($result->num_rows == 1) {
    $property = $result->fetch_assoc();
    header("Location: PropertyDetails.php?id=" . $property['id']);
    exit;
}

// If multiple properties are found, display them
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Golden Tigers Realtors</title>
    <link rel="stylesheet" href="styles.css">

<style>
    .back-button,
        .inquire-button {
            margin: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
           
        }

        .back-button:hover,
        .inquire-button:hover {
            background-color: #555;
        }
</style>

    <header role="banner">
        <img src="_images/aunty sue.png" alt="Golden Tigers Realtors Logo" class="logo">
        <div class="nav-container">
            <nav role="navigation">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="AboutUs.html">About Us</a></li>
                    <li><a href="Properties.php">Properties</a></li>
                    <li><a href="Realtors.php">Our Realtors</a></li>
                    <li><a href="ContactUs.php">Contact Sue</a></li>
                    <li><a href="#">My Account <i class="fas fa-caret-down"></i></a>
                        <ul>
                            <li><a href="AccountSettings.php">Account Settings</a></li>
                            <li><a href="view_inquiry_replies.php">Check Inquiry replies</a></li>
                            <li><a href="view_agent_replies.php">Check messages from agents</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>       
    </header>

    <section class="hero">
        <h2>Discover Your Dream Property</h2>
        <p>Explore our curated listings and find the perfect property for you.</p>
    </section>
</head>
<button class="back-button" onclick="goBack()">Back</button>
<body>
    <h1>Search Results</h1>
    <div class="property-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($property = $result->fetch_assoc()): ?>
                <?php 
                    // Use the main image if available, otherwise set a default image
                    $image = isset($property['main_image']) && !empty($property['main_image']) ? htmlspecialchars($property['main_image']) : 'default_image.jpg';
                ?>
                <div class="property-listing" onclick="window.location.href='PropertyDetails.php?id=<?php echo $property['id']; ?>'">
                <img src="<?php echo $image; ?>" alt="Property Image">
                    <p>Title: <?php echo htmlspecialchars($property['title']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($property['location']); ?></p>
                    <p>Price: R<?php echo number_format($property['price'], 2); ?></p>
                    <p>Bedrooms: <?php echo htmlspecialchars($property['bedrooms']); ?></p>
                    <p>Bathrooms: <?php echo htmlspecialchars($property['bathrooms']); ?></p>  <!-- Display bathrooms -->
                    <p>Size: <?php echo htmlspecialchars($property['size']); ?> sq ft</p>
                    <p>Type: <?php echo htmlspecialchars($property['property_type']); ?></p> 
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No properties found matching your criteria.</p>
        <?php endif; ?>
    </div>
    

</body>



<script>
    function goBack() {
        if (document.referrer !== '') {
            // If there is a referrer (previous page), go back
            window.history.back();
        } else {
            // If there's no referrer, redirect to a fallback page (e.g., home page)
            window.location.href = 'index.php';
        }
    }
</script>
</html>

<?php
$conn->close();
?>