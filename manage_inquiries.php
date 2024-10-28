<?php
include 'db_connection.php';

$sql = "SELECT * FROM inquiries WHERE status = 'pending'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='inquiry-item' data-inquiry-id='" . $row['id'] . "'>";
        echo "<p>Username: " . $row['username'] . "</p>";
        echo "<p>Property: " . $row['property'] . "</p>";
        echo "<p>Message: " . $row['message'] . "</p>";
        echo "<button onclick='openReplyModal(this)'>Reply</button>";
        echo "</div>";
    }
} else {
    echo "No inquiries found.";
}
$conn->close();
?>

<!-- Reply Modal -->
<div id="replyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReplyModal()">&times;</span>
        <h2>Reply to Inquiry</h2>
        <form id="replyForm" method="POST" action="submit_reply.php">
            <input type="hidden" id="inquiryId" name="inquiryId">

            <label for="replyMessage">Reply:</label>
            <textarea id="replyMessage" name="replyMessage" required></textarea>

            <button type="submit">Submit Reply</button>
        </form>
    </div>
</div>