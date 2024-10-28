<?php
session_start();
include 'db_connection.php'; // Include your database connection file


if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html"); // Redirect to login page
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$sql = "SELECT full_name, username, email, phone, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Handle case where user is not found
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Dada Properties</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .logo {
            width: 100px;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        main {
            padding: 2rem;
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            color: white;
        }

        .hero p {
            font-size: 1.2rem;
        }
        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .account-section {
            margin-bottom: 2rem;
        }

        .account-section h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .account-section form {
            display: flex;
            flex-direction: column;
        }

        .account-section form label {
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .account-section form input,
        .account-section form textarea {
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .account-section form button {
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .account-section form button:hover {
            background-color: #555;
        }

        .inquiry-replies {
            list-style-type: none;
            padding: 0;
        }

        .inquiry-replies li {
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .inquiry-replies li h4 {
            margin: 0 0 5px;
        }

        .inquiry-replies li p {
            margin: 0;
        }

        @media screen and (max-width: 600px) {
            main {
                padding: 1rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .account-section h3 {
                font-size: 1.25rem;
            }
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
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            align-items: center;
            font-size: 1.2rem;
        }

        .profile a {
            color: white;
            text-decoration: none;
            margin-right: 10px;
        }

        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .exit-button {
            margin-top: 20px;
            padding: 10px;
            background-color: #ff0000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .exit-button:hover {
            background-color: #cc0000;
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
        <h2>Account Settings</h2>
    </section>

    <main>
        <!-- Profile Viewing Section -->
        <div class="account-section">
            <h2>View Profile</h2>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
        </div>

        <!-- Profile Editing Section -->
        <div class="account-section">
            <h2>Edit Profile</h2>
            <form action="update_profile.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">

                <button type="submit">Update Profile</button>
            </form>
        </div>

        <!-- Logout Button Section -->
        <div class="account-section">
            <button class="exit-button" onclick="window.location.href='logout.php'">Logout</button>
        </div>

        <!-- Exit Button Section -->
        <div class="account-section">
            <button class="exit-button" onclick="window.location.href='index.php'">Exit to Home</button>
        </div>

        <!-- Inquiry Replies Section -->
        <div class="account-section">
            <h2>Inquiry Replies</h2>
            <ul class="inquiry-replies">
                <?php
                // Fetch inquiry replies from the database
                $sql = "SELECT property_id, reply, reply_date FROM inquiry_replies WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $replies = $stmt->get_result();

                if ($replies->num_rows > 0) {
                    while ($reply = $replies->fetch_assoc()) {
                        echo "<li>";
                        echo "<h4>Inquiry about Property ID: " . htmlspecialchars($reply['property_id']) . "</h4>";
                        echo "<p><strong>Admin Reply:</strong> " . htmlspecialchars($reply['reply']) . "</p>";
                        echo "<p><strong>Date:</strong> " . htmlspecialchars($reply['reply_date']) . "</p>";
                        echo "</li>";
                    }
                } else {
                    echo "<p>No replies found.</p>";
                }
                ?>
            </ul>
        </div>

        
    </main>
</body>
</html>
