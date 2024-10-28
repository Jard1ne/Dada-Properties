<?php
include 'db_connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html"); // Redirect to login page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover Properties - Golden Tigers Realtors</title>
    <meta name="description" content="Discover premium properties with Golden Tigers Realtors. Explore residential and commercial listings.">
    <meta name="keywords" content="real estate, properties, homes, apartments, commercial real estate, residential real estate">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #cac8c8;
            color: #333;
            margin: 0;
            padding: 0;
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

        .video-background {
            position: relative;
            width: 100%;
            height: 600px;
            overflow: hidden;
            margin-top: 80px;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .search-filters-container {
            position: relative;
            width: 100%;
            text-align: center;
            margin-top: -50px;
        }

        .search-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            max-width: 80%;
            margin: 0 auto;
        }

        .search-filters select,
        .search-filters input {
            padding: 10px;
            border-radius: 20px;
            border: none;
            outline: none;
            width: 200px;
        }

        .search-filters button {
            padding: 10px 20px;
            border-radius: 20px;
            border: none;
            background-color: #f0f0f0;
            color: #333;
            cursor: pointer;
        }

        .search-filters button:hover {
            background-color: #ccc;
        }

        main {
            padding: 100px 20px 20px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            text-align: center;
            margin-top: 2rem;
            font-size: 2.5rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 1s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .property-container,
        .realtors-container,
        .areas-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .property-listing,
        .realtor-box,
        .area-box,
        .see-more-box {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            text-align: center;
            width: 300px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .property-listing img,
        .realtor-box img,
        .area-box img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }

        .property-listing:hover,
        .realtor-box:hover,
        .area-box:hover,
        .see-more-box:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .see-more-box {
            background: url('_images/see-more-bg.jpeg') no-repeat center center/cover;
            color: #fff;
            position: relative;
        }

        .see-more-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Dark overlay */
            z-index: 0;
        }

        .see-more-box h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: #fff;
            position: relative;
            z-index: 1;
        }

        .see-more-box:hover h3 {
            color: #ddd;
        }

        .submit-property {
            position: relative;
            text-align: center;
            padding: 3rem 2rem;
            background: url('_images/submit-property-bg.png') no-repeat center center/cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            transition: all 0.3s ease;
            color: #fff;
            width: calc(100% - 350px); /* Adjust the width */
            margin-left: auto;
            margin-right: auto;
        }

        .submit-property::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Dark overlay */
            border-radius: 10px;
            z-index: 0;
        }

        .submit-property:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .submit-property-content {
            position: relative;
            z-index: 1;
        }

        .submit-property h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
        }

        .submit-property p,
        .submit-property ol {
            font-family: 'Roboto', sans-serif;
            font-size: 1.2rem;
        }

        .submit-property ol {
            text-align: left;
            margin: 0 auto 1.5rem;
            padding-left: 20px;
            max-width: 400px;
        }

        .submit-property button {
            padding: 12px 25px;
            border-radius: 20px;
            border: none;
            background-color: #333;
            color: #fff;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .submit-property button:hover {
            background-color: #555;
        }

        .areas-section {
            background-color: #222;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .area-box {
            background-color: transparent;
            box-shadow: none;
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

    <div class="video-background">
        <video autoplay muted loop>
            <source src="_videos/house-tour.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

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
        <input type="number" name="size" placeholder="Size (sq ft)" aria-label="Size in square feet">
        <input type="number" name="bathrooms" placeholder="Bathrooms" aria-label="Number of Bathrooms">
        <input type="number" name="bedrooms" placeholder="Bedrooms" aria-label="Number of Bedrooms">
        <input type="number" name="min_price" placeholder="Min Price" aria-label="Minimum Price">
        <input type="number" name="max_price" placeholder="Max Price" aria-label="Maximum Price">
        <button type="submit">Search</button>
    </form>
</div>

    <main>
        <section id="featured-listings">
            <h2>Featured Listings</h2>
            <div class="property-container">
                <div class="property-listing" onclick="viewPropertyDetails('1')">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.14 PM.jpeg" alt="Property in Ruimsig, Roodepoort, Gauteng">
                    <p>Location: Ruimsig, Roodepoort, Gauteng</p>
                    <p>Price: R4 900 000</p>
                </div>
                <div class="property-listing" onclick="viewPropertyDetails('2')">
                    <img src="_images/Struben1_House.png" alt="Property in Strubensvalley, Gauteng">
                    <p>Location: Strubensvalley, Gauteng</p>
                    <p>Price: R 3 299 000</p>
                </div>
                <div class="property-listing" onclick="viewPropertyDetails('3')">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.15 PM.jpeg" alt="Property in Constantia Kloof, Roodepoort, Gauteng">
                    <p>Location: Constantia Kloof, Roodepoort, Gauteng</p>
                    <p>Price: R2 498 000</p>
                </div>
                <div class="property-listing" onclick="viewPropertyDetails('4')">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.14 PM (1).jpeg" alt="Property in Weltevreden Park, Roodepoort, Gauteng">
                    <p>Location: Weltevreden Park, Roodepoort, Gauteng</p>
                    <p>Price: R1 499 000</p>
                </div>
                <div class="property-listing" onclick="viewPropertyDetails('5')">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.13 PM (1).jpeg" alt="Property in Little Falls, Roodepoort, Gauteng">
                    <p>Location: Little Falls, Roodepoort, Gauteng</p>
                    <p>Price: R899 000</p>
                </div>
                <div class="property-listing" onclick="viewPropertyDetails('6')">
                    <img src="_images/WhatsApp Image 2024-04-15 at 8.36.13 PM.jpeg" alt="Property in Florida Glen, Roodepoort, Gauteng">
                    <p>Location: Florida Glen, Roodepoort, Gauteng</p>
                    <p>Price: R1 800 000</p>
                </div>
                <div class="see-more-box" onclick="location.href='Properties.php'">
                    <h3>See More Properties</h3>
                </div>
            </div>
        </section>

        

        <section id="our-realtors">
            <h2>Our Realtors</h2>
            <div class="realtors-container">
                <div class="realtor-box">
                    <img src="_images/realtor1.jpg" alt="Realtor John Doe">
                    <p>Name: John Doe</p>
                    <p>Location: Johannesburg</p>
                </div>
                <div class="realtor-box">
                    <img src="_images/realtor2.jpg" alt="Realtor Jane Smith">
                    <p>Name: Jane Smith</p>
                    <p>Location: Sandton</p>
                </div>
                <div class="realtor-box">
                    <img src="_images/realtor3.jpg" alt="Realtor Mike Johnson">
                    <p>Name: Mike Johnson</p>
                    <p>Location: Pretoria</p>
                </div>
                <div class="realtor-box">
                    <img src="_images/realtor4.jpg" alt="Realtor Sarah Lee">
                    <p>Name: Sarah Lee</p>
                    <p>Location: Krugersdorp</p>
                </div>
                <div class="see-more-box" onclick="location.href='Realtors.php'">
                    <h3>See All Realtors</h3>
                </div>
            </div>
        </section>

        <section id="submit-property">
            <div class="submit-property">
                <div class="submit-property-content">
                    <h2>Submit a Property</h2>
                    <p>Ready to sell your property? Follow these simple steps to get started:</p>
                    <ol>
                        <li>Fill out the property details form.</li>
                        <li>Upload high-quality images of your property.</li>
                        <li>Set your asking price and any special conditions.</li>
                        <li>Submit the form for review by our team.</li>
                    </ol>
                    <button onclick="location.href='SubmitProperty.php'">Submit Property</button>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Sue@GoldenTigersRealtors. All rights reserved.</p>
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