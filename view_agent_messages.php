<?php
include 'db_connection.php';

// Get the agent ID from the URL
$agent_id = $_GET['agent_id'];

// Fetch agent information
$agent_sql = "SELECT full_name, username FROM agents WHERE id = ?";
$stmt = $conn->prepare($agent_sql);
$stmt->bind_param('i', $agent_id);
$stmt->execute();
$agent_result = $stmt->get_result();

if ($agent_result->num_rows == 0) {
    die("Agent not found");
}

$agent = $agent_result->fetch_assoc();

// Fetch messages sent to this agent
$messages_sql = "SELECT * FROM agent_messages WHERE agent_id = ? AND reply IS NULL";
$stmt = $conn->prepare($messages_sql);
$stmt->bind_param('i', $agent_id);
$stmt->execute();
$messages_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Messages Management</title>
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
                <li><a href="agents.php" class="active"><i class="fas fa-user-tie"></i> Agents</a></li>
                <li><a href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="view_contact_messages.php"><i class="fas fa-envelope"></i> View Contact Messages</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                <h1>Messages for <?php echo htmlspecialchars($agent['full_name']); ?> (<?php echo htmlspecialchars($agent['username']); ?>)</h1>
                </div>
                <div class="topbar-right">
                    <div class="notifications">
                        
                    </div>
                    <div class="user-profile">
                        <img src="profile.jpg" alt="User">
                        <span>Admin</span>
                        <a href="logout.php" class="logout-btn">Logout</a> <!-- Add a logout button -->
                    </div>
                </div>
            </header>
    <div class="main-content">
        
        <?php if ($messages_result->num_rows > 0): ?>
            <div class="messages-list">
                <?php while ($message = $messages_result->fetch_assoc()): ?>
                    <div class="message-card">
                        <h3>Message from <?php echo htmlspecialchars($message['username']); ?></h3>
                        <p class="message-content"><?php echo htmlspecialchars($message['message']); ?></p>
                        <div class="message-meta">
                            <strong>Date:</strong> <?php echo htmlspecialchars($message['created_at']); ?>
                        </div>
                        
                        <!-- Reply Section -->
                        <form method="POST" action="reply_to_message.php" class="reply-form">
                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                            <textarea name="reply" placeholder="Write your reply here" required></textarea>
                            <button type="submit" class="reply-btn">Send Reply</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No messages found for this agent.</p>
        <?php endif; ?>

        <!-- Back Button -->
        <button class="back-btn" onclick="goBack()">Back to Agents</button>
    </div>

    <script>
        function goBack() {
            window.location.href = "agents.php";
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
