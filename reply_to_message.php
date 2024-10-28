<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply'];

    if (!empty($message_id) && !empty($reply)) {
        // Update the message with the admin's reply and mark it as replied
        $sql = "UPDATE agent_messages SET reply = ?, is_replied = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si", $reply, $message_id);
            if ($stmt->execute()) {
                echo "Reply sent successfully.";
                // Redirect back to the message page
                header("Location: agent_messages.php?agent_id={$_GET['agent_id']}");
                exit();
            } else {
                echo "Error sending reply: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing query: " . $conn->error;
        }
    } else {
        echo "Reply cannot be empty.";
    }
}

$conn->close();
?>
