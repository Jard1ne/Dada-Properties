<?php
include 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html"); // Redirect to login page
    exit;
}
// Fetch recent properties (for example, properties added in the last 30 days)
$recent_properties_query = "
    SELECT 
        p.id, 
        p.title, 
        p.location, 
        p.price, 
        (SELECT pi.image_path FROM property_images pi WHERE pi.property_id = p.id LIMIT 1) AS image_path
    FROM properties p
    ORDER BY p.created_at DESC
    LIMIT 4";

$recent_properties_result = $conn->query($recent_properties_query);

// Check if the query was successful
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - Dada Properties</title>
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

        header {
            background-color: #333;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .logo {
            flex: 1;
        }

        .nav-container {
            margin-left: 130px;
            padding-top: 10px;
        }

        nav ul {
            list-style-type: none;
            display: flex;
            justify-content: center;
            font-size: 1.2rem;
        }

        nav ul li {
            position: relative;
            margin-right: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: #fff;
            padding: 10px;
            display: block;
        }

        nav ul li ul {
            position: absolute;
            top: 40px;
            left: 0;
            background-color: #444;
            display: none;
            flex-direction: column;
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
        }

        nav ul li:hover > ul {
            display: block;
        }

        nav ul li ul li {
            margin: 0;
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

        .profile a {
            color: #fff;
            text-decoration: none;
        }

        .profile a:hover {
            color: #ddd;
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
            margin-bottom: 20px;
            text-align: center;
        }

        .section-container:nth-child(even) {
            background-color: #333;
            color: #fff;
        }

        .section-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
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

        .section-container:nth-child(even) .property-listing {
            background-color: #444;
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

        .section-container:nth-child(even) .property-listing h3 {
            color: #fff;
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
        <h2>Discover Your Dream Property</h2>
        <p>Explore our curated listings and find the perfect property for you.</p>
    </section>

     <div class="search-filters-container">
    <form action="search_properties.php" method="GET" class="search-filters">
        <select name="property_type" aria-label="Property Type">
            <option value="" disabled selected>Type</option>
            <option value="house">House</option>
            <option value="apartment">Apartment</option>
        </select>
        <select name="location" aria-label="Location">
        <option value="" disabled selected>Location</option>
            <option value="roodepoort">Roodepoort</option>
            <option value="sandton">Sandton</option>
            <option value="johannesburg">Johannesburg</option>
            <option value="krugersdorp">Krugersdorp</option>
            <option value="pretoria">Pretoria</option>
        </select>
        <select name="status" aria-label="Status">
            <option value="" disabled selected>Status</option>
            <option value="available">Available</option>
            <option value="sold">Sold</option>
            <option value="pending">Pending</option>
        </select>
        <input type="number" name="size" placeholder="Size (sq ft)" aria-label="Size in square feet">
        <input type="number" name="bedrooms" placeholder="Bedrooms" aria-label="Number of Bedrooms">
        <input type="number" name="min_price" placeholder="Min Price" aria-label="Minimum Price">
        <input type="number" name="max_price" placeholder="Max Price" aria-label="Maximum Price">
        <button type="submit">Search</button>
    </form>
</div>

    <main>

    
        <section class="section-container">
            <h2>Featured Listings</h2>
            <div class="property-container">


                <div class="property-listing">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.14 PM.jpeg" alt="Property 1" onclick="viewPropertyDetails(1)">
                    <h3>Ruimsig House</h3>
                    <p>Location: Ruimsig, Roodepoort, Gauteng</p>
                    <p>Price: R4 900 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/Struben1_House.png" alt="Property 2" onclick="viewPropertyDetails(2)">
                    <h3>Strubensvalley House</h3>
                    <p>Location: Strubensvalley, Gauteng</p>
                    <p>Price: R3 299 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.15 PM.jpeg" alt="Property 3" onclick="viewPropertyDetails(3)">
                    <h3>Constantia Kloof House</h3>
                    <p>Location: Constantia Kloof, Roodepoort, Gauteng</p>
                    <p>Price: R2 498 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.14 PM (1).jpeg" alt="Property 4" onclick="viewPropertyDetails('4')">
                    <h3>Weltevreden Park House</h3>
                    <p>Location: Weltevreden Park, Roodepoort, Gauteng</p>
                    <p>Price: R1 499 000</p>
                </div>

                <div class="property-listing">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.13 PM (1).jpeg" alt="Property 5" onclick="viewPropertyDetails('5')">
                    <h3>Little Falls House</h3>
                    <p>Location: Little Falls, Roodepoort, Gauteng</p>
                    <p>Price: R899 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.13 PM.jpeg" alt="Property 6" onclick="viewPropertyDetails('6')">
                    <h3>Florida Glen House</h3>
                    <p>Location: Florida Glen, Roodepoort, Gauteng</p>
                    <p>Price: R1 800 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/Blairgowrie house.png" alt="Property 7" onclick="viewPropertyDetails('7')">
                    <h3>Blairgowrie House</h3>
                    <p>Location: Blairgowrie, Randburg, Gauteng</p>
                    <p>Price: R1 849 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/house krugersdorp.jpg" alt="Property 8" onclick="viewPropertyDetails('8')">
                    <h3>Rangeview House</h3>
                    <p>Location: Rangeview, Krugersdorp, Gauteng</p>
                    <p>Price: R2 995 000</p>
                </div>

                <Button><a href="allProperties.php">View All Properties</a></Button>

            </div>
            
        </section> 

         <!-- Recent Listings Section -->
         <section class="section-container">
            <h2>Recent Listings</h2>
            <div class="property-container">
                <?php
                if ($recent_properties_result->num_rows > 0) {
                    while ($property = $recent_properties_result->fetch_assoc()) {
                        ?>
                        <div class="property-listing">
                            <img src="<?php echo htmlspecialchars($property['image_path'] ? $property['image_path'] : 'default_image.jpg'); ?>" alt="Property Image" onclick="viewPropertyDetails(<?php echo $property['id']; ?>)">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p>Location: <?php echo htmlspecialchars($property['location']); ?></p>
                            <p>Price: R<?php echo number_format($property['price'], 2); ?></p>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>No recent listings found.</p>';
                }
                ?>
            </div>
        </section>

        

        <section class="section-container">
            <h2>Top Houses</h2>
            <div class="property-container">
                <div class="property-listing">
                    <img src="_images/house helderkruin.jpg" alt="Property 9" onclick="viewPropertyDetails('9')">
                    <h3>Top House 1</h3>
                    <p>Location: Helderkruin View, Roodepoort, Gauteng</p>
                    <p>Price: R2 225 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/house sandton.jpg" alt="Property 10" onclick="viewPropertyDetails('10')">
                    <h3>Top House 2</h3>
                    <p>Location: Sandton Central, Sandton, Gauteng</p>
                    <p>Price: R7 399 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/rosebank1.jpeg" alt="Property 11" onclick="viewPropertyDetails('11')">
                    <h3>Top House 3</h3>
                    <p>Location: Rosebank, Johannesburg, Gauteng</p>
                    <p>Price: R1 595 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/house rand park.jpg" alt="Property 12" onclick="viewPropertyDetails('12')">
                    <h3>Top House 4</h3>
                    <p>Location: Randpark Ridge, Randburg, Gauteng</p>
                    <p>Price: R2 225 000</p>
                </div>
            </div>
        </section>

        <section class="section-container">
            <h2>Top Properties</h2>
            <div class="property-container">
                <div class="property-listing">
                    <img src="_images/house green side.jpg" alt="Property 13" onclick="viewPropertyDetails('13')">
                    <h3>Top Property 1</h3>
                    <p>Location: Greenside, Johannesburg, Gauteng</p>
                    <p>Price: R3 395 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.14 PM.jpeg" alt="Property 1" onclick="viewPropertyDetails('1')">
                    <h3>Top Property 2</h3>
                    <p>Location: Ruimsig, Roodepoort, Gauteng</p>
                    <p>Price: R4 900 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.13 PM.jpeg" alt="Property 6" onclick="viewPropertyDetails('6')">
                    <h3>Top Property 3</h3>
                    <p>Location: Florida Glen, Roodepoort, Gauteng</p>
                    <p>Price: R1 800 000</p>
                </div>
                <div class="property-listing">
                    <img src="_images/house rand park.jpg" alt="Property 12" onclick="viewPropertyDetails('12')">
                    <h3>Top Property 4</h3>
                    <p>Location: Randpark Ridge, Randburg, Gauteng</p>
                    <p>Price: R2 225 000</p>
                </div>
            </div>
        </section>
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
            window.location.href = 'PropertyDetails.php?id=' + propertyId;
        }
    </script>
    
</body>

</html>
