<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'])) {
    // Get the property ID from the request
    $property_id = $_POST['property_id'];

    // Start a transaction to ensure all operations succeed or fail together
    $conn->begin_transaction();

    try {
        // Fetch the images associated with the property from the database
        $images_query = "SELECT image_path FROM property_images WHERE property_id = ?";
        $stmt = $conn->prepare($images_query);
        $stmt->bind_param('i', $property_id);
        $stmt->execute();
        $images_result = $stmt->get_result();

        // Loop through the results and delete the images from the uploads/ folder
        while ($row = $images_result->fetch_assoc()) {
            $image_path = $row['image_path'];
            $file_path = 'uploads/' . $image_path;
            if (file_exists($file_path)) {
                unlink($file_path); // Delete the image file
            }
        }

        // Delete the images from the `property_images` table
        $delete_images_query = "DELETE FROM property_images WHERE property_id = ?";
        $stmt = $conn->prepare($delete_images_query);
        $stmt->bind_param('i', $property_id);
        $stmt->execute();

        // Delete the property from the `properties` table
        $delete_property_query = "DELETE FROM properties WHERE id = ?";
        $stmt = $conn->prepare($delete_property_query);
        $stmt->bind_param('i', $property_id);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect to the properties page with a success message
        header("Location: properties.php?success=Property deleted successfully!");
        exit;
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        die("Error deleting property: " . $e->getMessage());
    }

} else {
    // If the request is not valid, redirect back to the properties page
    header("Location: properties.php?error=Invalid request");
    exit;
}

$conn->close();
?>