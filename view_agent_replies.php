<?php
session_start();
include 'db_connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html"); // Redirect to login page
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch messages sent by the logged-in user and replies from the agents
$sql = "
    SELECT am.id, am.message, am.reply, a.full_name AS agent_name, am.created_at 
    FROM agent_messages am
    INNER JOIN agents a ON am.agent_id = a.id
    WHERE am.user_id = ? 
    ORDER BY am.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Replies</title>
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

        .messages-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Responsive grid */
            gap: 20px;
            margin-top: 20px;
        }

        .message-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .message-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .message-card h3 {
            font-size: 18px;
            color: #4b69ec;
            margin-bottom: 10px;
        }

        .message-card p {
            font-size: 14px;
            margin: 10px 0;
            color: #666;
        }

        .message-card strong {
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

        /* Exit button */
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
        <h2>Agent Replies</h2>
    </section>

    <main>
        <div class="messages-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="message-card">
                        <h3>Message to <?php echo htmlspecialchars($row['agent_name']); ?></h3>
                        <p><strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                        <p><strong>Sent On:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                        <?php if (!empty($row['reply'])): ?>
                            <p><strong>Agent Reply:</strong> <?php echo htmlspecialchars($row['reply']); ?></p>
                        <?php else: ?>
                            <p><strong>Status:</strong> Pending Reply</p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No messages found.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
$conn->close();
?>
