<?php
include 'db_connection.php';

// Fetch all properties with the first image for each property from the database
$all_properties_query = "
    SELECT 
        p.id, 
        p.title, 
        p.location, 
        p.price, 
        (SELECT pi.image_path FROM property_images pi WHERE pi.property_id = p.id LIMIT 1) AS image_path
    FROM properties p
    ORDER BY p.created_at DESC";

$all_properties_result = $conn->query($all_properties_query);

// Check if the query was successful
if (!$all_properties_result) {
    die("Error fetching properties: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Properties - Dada Properties</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
            font-size: 18px;
        }

        .logo {
            width: 100px;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .nav-container {
            flex: 2;
            display: flex;
            justify-content: center;
            margin-left: 350px;
        }

        nav ul {
            list-style-type: none;
            display: flex;
        }

        nav ul li {
            position: relative;
            margin: 0 15px;
            white-space: nowrap;
        }

        nav ul li a {
            text-decoration: none;
            color: #fff;
            padding: 10px 15px;
            display: block;
        }

        nav ul li a:hover,
        nav ul li:hover > a {
            background-color: #444;
        }

        .profile {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-left: 10px;
        }

        .hero {
            background-image: url('_images/properties-bg-image.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
            padding: 100px 20px;
            text-align: center;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero h2,
        .hero p {
            position: relative;
            z-index: 1;
        }

        .hero h2 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1.2rem;
        }

        .section-container {
            padding: 20px;
        }

        .property-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .property-listing {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            text-align: left;
            max-width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .property-listing:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .property-listing img {
            max-width: 100%;
            height: fit-content;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .property-listing h3 {
            margin-top: 0;
            font-size: 1rem;
            color: #333;
        }

        .property-listing p {
            margin: 0.5em 0;
            font-size: 0.7rem;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1rem;
            position: relative;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .social-media {
            margin-top: 1rem;
        }

        .social-media a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-media a:hover {
            color: #ddd;
        }

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
</head>

<body>
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
        <h2>Explore All Our Properties</h2>
        <p>Browse through the complete list of properties available.</p>
    </section>

    

    <main>
        <section class="section-container">
            <h2>All Properties</h2>
            <div class="property-container">
                <?php
                    if ($all_properties_result->num_rows > 0) {
                        while ($property = $all_properties_result->fetch_assoc()) {
                            ?>
                            <div class="property-listing">
                                <img src="<?php echo $property['image_path'] ? htmlspecialchars($property['image_path']) : 'default_image.jpg'; ?>" alt="Property Image" onclick="viewPropertyDetails(<?php echo $property['id']; ?>)">
                                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                                <p>Location: <?php echo htmlspecialchars($property['location']); ?></p>
                                <p>Price: R<?php echo number_format($property['price'], 2); ?></p>
                            </div>
                            <?php
                    }
                } else {
                    echo '<p>No properties found.</p>';
                }
                ?>

                
            </div>
        </section>
        <button class="back-button" onclick="goBack()">Back</button>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Dada Properties. All rights reserved.</p>
            <div class="social-media">
                <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

    <script>
        function viewPropertyDetails(propertyId) {
            // Redirect to the property details page with the selected property ID
            window.location.href = 'PropertyDetails.php?id=' + propertyId;
        }

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
</body>

</html>
