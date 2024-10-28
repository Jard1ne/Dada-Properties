<?php
// Database connection
include 'db_connection.php';

$sql = "SELECT message_id, name, email, message, created_at FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Contact Messages</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="properties.php"><i class="fas fa-building"></i> Properties</a></li>
                <li><a href="SubmittedProperties.php"><i class="fas fa-file-alt"></i> Submitted Properties</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="agents.php"><i class="fas fa-user-tie"></i> Agents</a></li>
                <li><a href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="view_contact_messages.php" class="active"><i class="fas fa-envelope"></i> View Contact Messages</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h1>Contact Messages</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-profile">
                        <img src="profile.jpg" alt="Admin">
                        <span>Admin</span>
                        <a href="logout.php" class="logout-btn">Logout</a> <!-- Add a logout button -->
                    </div>
                </div>
            </header>
            <div class="main-content">
                <?php if ($result->num_rows > 0): ?>
                    <div class="messages-list">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="message-card">
                                <h3>Message from <?php echo htmlspecialchars($row['name']); ?></h3>
                                <p class="message-content"><?php echo htmlspecialchars($row['message']); ?></p>
                                <div class="message-meta">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?>
                                    <br>
                                    <strong>Date:</strong> <?php echo htmlspecialchars($row['created_at']); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No messages found.</p>
                <?php endif; ?>

                <!-- Back Button -->
                <button class="back-btn" onclick="window.history.back()">Back</button>
            </div>
        </main>
    </div>

    <script src="scripts.js"></script>

    
</body>
</html>

<?php
$conn->close();
?>
