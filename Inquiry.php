<?php
session_start();
include 'db_connection.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html"); // Redirect to login page
    exit;
}


$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$sql = "SELECT full_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

// Get the property ID from the URL parameter
$propertyId = $_GET['id'] ?? '';

if ($propertyId) {
    // Fetch the property title from the database 
    $sql = "SELECT title FROM properties WHERE id = ?";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("i", $propertyId);
     $stmt->execute();
     $stmt->bind_result($propertyTitle);
     $stmt->fetch();
     $stmt->close();

    //$propertyTitle = $properties[$propertyId] ?? 'Unknown Property';

   
    
} else {
    echo "Invalid property ID.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquire About Property - Dada Properties</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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

        main {
            padding-top: 100px; /* Adjust for the fixed header */
        }

        section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 2em;
        }

        form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        form label {
            display: block;
            margin-bottom: 0.5rem;
            text-align: left;
        }

        form input[type="text"],
        form input[type="email"],
        form textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form button[type="submit"] {
            margin: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button[type="submit"]:hover {
            background-color: #ff7f00;
        }

        .back-button {
            margin: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #555;
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

    <main>
        <section>
            <h2>Inquire About Property</h2>
            <form id="inquiry-form" action="submit_inquiry.php" method="POST">
                <!-- Add a hidden input field to capture the property ID -->
                <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($propertyId); ?>">

                <label for="property">Property:</label>
                <input type="text" id="property" name="property" value="<?php echo htmlspecialchars($propertyTitle); ?>" readonly>

                <label for="username">Your Name:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>

                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>

                <label for="message">Your Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>

                <button type="submit">Send Inquiry</button>
                <button class="back-button" type="button" onclick="goBack()">Back</button>
            </form>
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
        // Get the property ID from the URL parameter
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const propertyId = urlParams.get('id');

        // Sample property data
        const properties = {
            'property1': {
                title: 'Ruimsig House',
                // other details...
            },
            'property2': {
                title: 'Strubensvalley House',
                // other details...
            },
            // More properties...
        };

        // Load the property details into the form
        function loadPropertyDetails(propertyId) {
            const property = properties[propertyId];
            if (property) {
                document.getElementById('property').value = property.title;
            }
        }

        // Load property details when the page loads
        loadPropertyDetails(propertyId);

        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>

submit_inquiry.php:
<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property = $_POST['property'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Prepare the SQL statement
    $sql = "INSERT INTO inquiries (property_title, username, email, message) VALUES (?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ssss", $property, $username, $email, $message);

        if ($stmt->execute()) {
            header("Location: PropertyDetails.php?message=Inquiry%20was%20sent%20successfully");
            exit();
        } else {
            die("Error executing query: " . $stmt->error);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>