<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['inquiry_id'])) {
        $inquiryId = intval($_POST['inquiry_id']);

        // Prepare the delete statement
        $sql = "DELETE FROM inquiries WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $inquiryId);

        if ($stmt->execute()) {
            // Redirect back to the enquiries page after successful deletion
            header("Location: enquiries.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>