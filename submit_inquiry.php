<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $property_id = $_POST['property_id']; // From hidden input
    $property_title = $_POST['property']; // This is just for display
    $username = $_POST['username'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Begin a transaction to ensure data integrity
    $conn->begin_transaction();

    try {
        // Step 1: Check if the user is already in the users table (assuming users table holds user_id)
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing the user check statement: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            // User is not registered, insert them into the users table
            $stmt->close();
            $sql = "INSERT INTO users (full_name, email) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparing the user insert statement: " . $conn->error);
            }

            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $user_id = $stmt->insert_id; // Get the ID of the new user
        } else {
            // User is already registered, get their ID
            $stmt->bind_result($user_id);
            $stmt->fetch();
        }

        $stmt->close();

        // Step 2: Insert the inquiry into the inquiries table using user_id
        $sql = "INSERT INTO inquiries (user_id, property_id, message, username, property_title, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing the inquiry insert statement: " . $conn->error);
        }

        $stmt->bind_param("iisss", $user_id, $property_id, $message, $username, $property_title);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the property details page with a success message
        header("Location: PropertyDetails.php?id=$property_id&inquiry=success");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Failed to submit inquiry: " . $e->getMessage();
    }

    // Close the database connection
    $conn->close();
} else {
    echo "Invalid request method.";
}