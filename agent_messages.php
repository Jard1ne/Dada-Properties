<?php
include 'db_connection.php';
// Fetch only unreplied messages
$sql = "SELECT * FROM agent_messages WHERE agent_id = ? AND is_replied = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($message = $result->fetch_assoc()) {
        // Display each message
    }
} else {
    echo "No new messages.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages to Agents</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
        <div class="message-container">
        <h2>Messages to Agents</h2>
        <div class="messages-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="message-card">
                    <div class="message-content">
                        <h3>Message from <?php echo htmlspecialchars($row['sender']); ?> to <?php echo htmlspecialchars($row['agent']); ?></h3>
                        <p><strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                        <p><strong>Reply:</strong> <?php echo htmlspecialchars($row['reply']); ?></p>
                        </div>
                        <div class="message-meta">
                        <p><small>Sent on: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                        </div>
                        <!-- Reply form -->
                        <form method="POST" action="reply_to_message.php">
                            <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                            <textarea name="reply" placeholder="Enter reply..."></textarea>
                            <button type="submit">Reply</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No messages found.</p>
                <?php endif; ?>
        </div>
    </div>

    <!-- Back Button -->
    <button onclick="goBack()">Back to Messages</button>

    <script>
        function goBack() {
            window.location.href = "agents.php?agent_id=<?php echo $_GET['agent_id']; ?>";
        }
    </script>


</body>
</html>

<?php
$conn->close();
?>
