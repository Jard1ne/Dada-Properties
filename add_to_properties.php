<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submission_id'])) {
    $submission_id = $_POST['submission_id'];

    $conn->begin_transaction(); // Start the transaction
    try {
        $stmt = $conn->prepare("SELECT * FROM property_submissions WHERE id = ?");
        $stmt->bind_param("i", $submission_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $property = $result->fetch_assoc();

            // Prepare and execute insertion of the property
            $stmt = $conn->prepare("INSERT INTO properties (title, location, address, price, bedrooms, bathrooms, garage, size, status, description, property_type, selling_type)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $status = 'available'; // Default status
            $stmt->bind_param("sssdiiisssss", $property['title'], $property['location'], $property['address'], $property['price'], $property['bedrooms'], $property['bathrooms'], $property['garage'], $property['size'], $status, $property['description'], $property['property_type'], $property['selling_type']);
            $stmt->execute();
            $property_id = $conn->insert_id;

            // Handle images
            if (!empty($property['images'])) {
                $images = explode(',', $property['images']);
                foreach ($images as $image) {
                    $image_name = basename($image);
                    $dbImagePath = "_images/" . $image_name;
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/uploads/" . $image_name)) {
                        $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                        $stmt->bind_param("is", $property_id, $dbImagePath);
                        $stmt->execute();
                    } else {
                        echo "Image file not found: $image";
                    }
                }
            }

            // Optionally, delete the property submission after it has been added
            $stmt = $conn->prepare("DELETE FROM property_submissions WHERE id = ?");
            $stmt->bind_param("i", $submission_id);
            $stmt->execute();

            $conn->commit(); // Commit the transaction
            header("Location: SubmittedProperties.php?success=added");
            exit;
        } else {
            throw new Exception("Property submission not found.");
        }
    } catch (Exception $e) {
        $conn->rollback(); // Roll back the transaction on error
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
