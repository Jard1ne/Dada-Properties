<?php
include 'db_connection.php'; // Include your database connection file

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
    <title>Submit Your Property - Dada Properties</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f0f0;
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
            flex:1;
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

        .sign-up-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
        }

        .sign-up-icon a {
            color: white;
            text-decoration: none;
        }

        .sign-up-icon a:hover {
            color: #ddd;
        }

        main {
            padding: 2rem;
            max-width: 800px;
            margin: auto;
            text-align: center;
            position: relative;
        }

        /* Background image behind the heading */
        .heading-section {
            background-image: url('_images/WhatsApp\ Image\ 2024-08-12\ at\ 10.16.49\ PM.jpeg');
            background-size: cover;
            background-position: center;
            padding: 50px 20px;
            border-radius: 10px;
            color: #fff;
            margin-bottom: 2rem;
        }

        .heading-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #fff;
        }

        .heading-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #ddd;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        form .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        form .input-group i {
            margin-right: 10px;
            font-size: 1.2rem;
            color: #333;
        }

        form input,
        form select,
        form textarea {
            width: calc(100% - 30px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
        }

        form textarea {
            resize: vertical;
        }

        form input[type="file"] {
            padding-left: 0; /* No padding needed for file inputs */
        }

        form button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #555;
        }

        /* Hide 'other-property-type' initially */
.hidden {
    display: none;
}

/* Styling for the hidden field once it's shown */
#other-property-type {
    margin-top: 10px;
}

/* Ensure select dropdowns are styled similarly to text inputs */
form select {
    appearance: none;
    background-color: #fff;
    background-image: url('data:image/svg+xml;base64,YOUR_BASE64_SVG_ARROW');
    background-position: right 10px center;
    background-repeat: no-repeat;
    padding-right: 40px; /* space for dropdown arrow */
}

/* For the icons to align better */
.input-group select {
    padding-left: 40px;
}

        .contact-info {
            margin-top: 2rem;
            text-align: left;
        }

        .contact-info p {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #333;
        }

        .contact-info i {
            margin-right: 10px;
            font-size: 1.2rem;
            color: #333;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1rem;
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

        @media screen and (max-width: 600px) {
            header img {
                width: 60px;
            }

            header h1 {
                font-size: 1.2rem;
                left: 80px;
            }

            nav ul {
                flex-direction: column;
                align-items: center;
                margin-top: 20px;
            }

            nav ul li {
                margin: 10px 0;
            }

            .heading-section {
                padding: 30px 10px;
            }

            .heading-section h2 {
                font-size: 2rem;
            }

            .heading-section p {
                font-size: 1rem;
            }
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

    <main>
        <div class="heading-section">
            <h2>Submit Your Property</h2>
            <p>Please fill out the form below to submit your property for sale or rent.</p>

        </div>

        <form action="submit_property.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <i class="fas fa-home"></i>
                <input type="text" id="property-title" name="property-title" placeholder="Property Title" required>
            </div>

            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" id="property-location" name="property-location" placeholder="Property Location" required>
            </div>

            <div class="input-group">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" id="property-address" name="property-address" placeholder="Property Address" required>
            </div>

            <div class="input-group">
                <i class="fas fa-dollar-sign"></i>
                <input type="number" id="property-price" name="property-price" placeholder="Price (in ZAR)" required>
            </div>

            <div class="input-group">
            <i class="fas fa-tag"></i>
                <select id="selling-type" name="selling-type" required>
                    <option value="" disabled selected>Selling Type</option>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
            </div>
            
            <div class="input-group">
                <i class="fas fa-building"></i>
                <select id="property-type" name="property-type" required>
                    <option value="" disabled selected>Property Type</option>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                    <!--<option value="other">Other</option>-->
                </select>

                <!-- This input field is hidden initially -->
                <input type="text" id="other-property-type" name="other-property-type" placeholder="Please specify" class="hidden">
            </div>

            <div class="input-group">
                <i class="fas fa-bed"></i>
                <input type="number" id="property-bedrooms" name="property-bedrooms" placeholder="Number of Bedrooms" required>
            </div>

            <div class="input-group">
                <i class="fas fa-bath"></i>
                <input type="number" id="property-bathrooms" name="property-bathrooms" placeholder="Number of Bathrooms" required>
            </div>

            <div class="input-group">
                <i class="fas fa-car"></i>
                <input type="number" id="property-garage" name="property-garage" placeholder="Number of cars that fit in garage" required>
            </div>

            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="number" id="property-size" name="property-size" placeholder="Size in square metres of the property" required>
            </div>

            <div class="input-group">
                <i class="fas fa-edit"></i>
                <textarea id="property-description" name="property-description" placeholder="Property Description" rows="5" required></textarea>
            </div>

            <div class="input-group">
                <i class="fas fa-camera"></i>
                <input type="file" id="property-images" name="property-images[]" accept="image/*" multiple required>
            </div>

            <button type="submit">Submit Property</button>
        </form>

        <div class="contact-info">
            <p><i class="fas fa-phone"></i> +27 82 123 4567</p>
            <p><i class="fas fa-envelope"></i> Dada Properties.com</p>
        </div>
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
</body>

<script>
    document.getElementById('property-type').addEventListener('change', function() {
        var otherInput = document.getElementById('other-property-type');
        if (this.value === 'other') {
            otherInput.style.display = 'block'; // Show the input field
            otherInput.required = true; // Make it required
        } else {
            otherInput.style.display = 'none'; // Hide the input field
            otherInput.required = false; // Remove required validation
        }
    });
</script>

</html>