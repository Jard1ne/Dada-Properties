<?php
include 'db_connection.php';  // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agent_id = $_POST['agent_id'];  // ID of the agent
    $user_id = $_POST['user_id'];    // ID of the user (from session)
    $message = $_POST['message'];
    $username = $_POST['username'];  // Username of the sender (from the form)

    // Make sure the required fields are set
    if (!empty($agent_id) && !empty($user_id) && !empty($message) && !empty($username)) {
        // Prepare the SQL statement to include 'username'
        $sql = "INSERT INTO agent_messages (agent_id, user_id, message, username) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("iiss", $agent_id, $user_id, $message, $username);
            if ($stmt->execute()) {
                 // Redirect back to the property details page with a success message
        header("Location: Realtors.php?inquiry=success");
            } else {
                echo "Error sending message: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing query: " . $conn->error;
        }
    } else {
        echo "All fields are required.";
    }

    $conn->close();  // Close the database connection
}
?>
