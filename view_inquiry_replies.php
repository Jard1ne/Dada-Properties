<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];



// Fetch inquiries made by the logged-in user
$sql = "SELECT id, message, response, status, inquiry_date FROM inquiries WHERE user_id = ? ORDER BY inquiry_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

// Check query execution
if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    echo "Query execution failed: " . $stmt->error;
}

$stmt->execute(); // Re-execute the query to render in the HTML below
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inquiries</title>
    <link rel="stylesheet" href="styles.css">

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
            max-width: 1200px;
            margin: 2rem auto;
        }

        .inquiries-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Responsive grid */
            gap: 20px;
            margin-top: 20px;
        }

        .inquiry-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .inquiry-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .inquiry-card h3 {
            font-size: 18px;
            color: #4b69ec;
            margin-bottom: 10px;
        }

        .inquiry-card p {
            font-size: 14px;
            margin: 10px 0;
            color: #666;
        }

        .inquiry-card strong {
            color: #333;
        }

        /* Hero section */
        .hero {
            background-image: url('_images/properties-bg-image.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
            padding: 100px 20px;
            text-align: center;
            position: relative;
            margin-bottom: 2rem;
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

        .hero h2 {
            position: relative;
            z-index: 1;
            font-size: 3rem;
            margin-bottom: 10px;
            color: white;
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

    <!-- Hero section -->
    <section class="hero">
        <h2>My Inquiries</h2>
    </section>

    <!-- Main content -->
    <main>
        <div class="inquiries-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="inquiry-card">
                        <h3>Inquiry</h3>
                        <p><strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                        <p><strong>Submitted:</strong> <?php echo date("F j, Y, g:i a", strtotime($row['inquiry_date'])); ?></p> <!-- Date formatted -->
                        <?php if (!empty($row['response'])): ?>
                            <p><strong>Admin Reply:</strong> <?php echo htmlspecialchars($row['response']); ?></p>
                        <?php else: ?>
                            <p><strong>Admin Reply:</strong> No reply yet.</p> <!-- Show "No reply yet" if response is empty -->
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No inquiries found.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
$conn->close();
?>