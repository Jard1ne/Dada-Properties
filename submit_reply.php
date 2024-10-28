<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming $_POST['inquiryId'] and $_POST['replyMessage'] are available
$inquiryId = $_POST['inquiryId'];
$replyMessage = $_POST['replyMessage'];



    if (!empty($inquiryId) && !empty($replyMessage)) {
        // Prepare the SQL query using a prepared statement
        $sql = "UPDATE inquiries SET response = ?, status = 'responded' WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind the parameters to the SQL query
            $stmt->bind_param("si", $replyMessage, $inquiryId);
            
            if ($stmt->execute()) {
                echo "Reply submitted successfully!";
                // Redirect back to the inquiries page (or whichever page is appropriate)
                header("Location: enquiries.php?inquiry_id={$inquiryId}");
                exit();
            } else {
                echo "Error submitting reply: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing query: " . $conn->error;
        }
    } else {
        echo "Reply message cannot be empty.";
    }
}

// Close the connection
$conn->close();
?>