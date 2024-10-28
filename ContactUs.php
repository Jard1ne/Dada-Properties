<?php

session_start();
include 'db_connection.php'; // Include your database connection file


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
    <title>Contact Us - Dada Properties</title>
    <link rel="stylesheet" href="styles.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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


        .content {
            text-align: center;
            padding: 20px;
        }

        .map-and-form {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .map {
            width: 60%;
        }

        .form {
            width: 35%;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .form input,
        .form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form input[type="submit"] {
            background-color: #333;
            color: white;
            cursor: pointer;
            border: none;
        }

        .contact-info {
            text-align: center;
            padding: 20px;
            background-color: #f1f1f1;
        }

        .contact-info div {
            display: inline-block;
            width: 30%;
            vertical-align: top;
            padding: 10px;
        }

        .contact-info div img {
            width: 30px;
            margin-bottom: 10px;
        }

        .footer-banner {
            background: url('https://example.com/your-image.jpg') no-repeat center center;
            background-size: cover;
            padding: 50px;
            color: rgb(255, 255, 255);
            text-align: center;
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

<div class="content">
    <h1>Contact us at Dada Properties</h1>
</div>

<div class="map-and-form">
    <div class="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3583.171495638341!2d27.810218516021977!3d-26.079691683487675!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1e95a17e89a3d27b%3A0x2bc913dfb2fdc229!2s3%20Crossberry%20St%2C%20Rangeview%2C%20Krugersdorp%2C%201730%2C%20South%20Africa!5e0!3m2!1sen!2sza!4v1691785561839!5m2!1sen!2sza" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    
    <div class="form">
        <h2>Submit a completed form and a consultant will get in touch with you!</h2>
        <form action="send_contact_messages.php" method="POST">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" required placeholder="First and Last Name">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required placeholder="Your Email">
            <label for="message">Comment or Message *</label>
            <textarea id="message" name="message" required placeholder="Your Message"></textarea>
            <input type="submit" value="Submit">
        </form>
    </div>
</div>

<div class="contact-info" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background-color: #f7f7f7; border-top: 1px solid #000; border-bottom: 1px solid #000;">
    <div class="contact-item" style="text-align: center;">
        <br>
        <!--<img src="_images/Location icon.png" alt="Location Icon" style="width: 50px; height: 50px;"/>-->
        <h3 style="font-size: 16px; margin-top: 10px; font-weight: bold;">LOCATION</h3>
        <p style="margin: 5px 0;">3 Crossberry Street<br/>Rangeview, Krugersdorp</p>
    </div>
    <div class="contact-item" style="text-align: center;">
        <!--<img src="_images/Phone icon.png" alt="Phone Icon" style="width: 50px; height: 50px;"/>-->
        <h3 style="font-size: 16px; margin-top: 10px; font-weight: bold;">PHONE</h3>
        <p style="margin: 5px 0;">+27 76 591 8261</p>
    </div>
    <div class="contact-item" style="text-align: center;">
        <!--<img src="_images/Email icon.png" alt="Email Icon" style="width: 50px; height: 50px;"/>-->
        <h3 style="font-size: 16px; margin-top: 10px; font-weight: bold;">EMAIL</h3>
        <p style="margin: 5px 0;">suhaima@goldentigerrealtors.com</p>
    </div>
</div>
<div class="hero-image" style="background-image: url('_images/properties-bg-image.jpg'); background-size: cover; height: 300px;">
    <section class="hero">
        <h2>Everone Deserves Their Dream Home</h2>
        <p>Find Yours Today!</p>
    </section>
 
</div>
</body>

<footer>
    <div class="footer-content">
        <br>
        <p>&copy; 2024 Dada Properties. All rights reserved.</p>
        <div class="social-media"> 
            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
</footer>

</html>