<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = $_POST['editPropertyId']; // Ensure property ID is passed
    $title = $_POST['editPropertyTitle'];
    $location = $_POST['editPropertyLocation'];
    $address = $_POST['editPropertyAddress'];
    $price = $_POST['editPropertyPrice'];
    $bedrooms = $_POST['editPropertyBedrooms'];
    $garages = $_POST['editPropertyGarages'];
    $size = $_POST['editPropertySize'];
    $status = $_POST['editPropertyStatus'];
    $description = $_POST['editPropertyDescription'];
    $property_type = $_POST['editPropertyType'];
    $selling_type = $_POST['editPropertySellingType'];

    // Update property information using prepared statements
    $stmt = $conn->prepare("UPDATE properties SET title=?, location=?, address=?, price=?, bedrooms=?, garage=?, size=?, status=?, description=?, property_type=?, selling_type=? WHERE id=?");
    $stmt->bind_param("sssdiiissssi", $title, $location, $address, $price, $bedrooms, $garages, $size, $status, $description, $property_type, $selling_type, $property_id);
    if ($stmt->execute()) {
        // Handle new image uploads
        if (!empty($_FILES['editPropertyImages']['name'][0])) {
            $rootUploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/GoldenTigersRealtorsWebsite-20240414T094636Z-001/Sue@GoldenTigerRealtors/_images/";
            $adminUploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/GodenRealtorsAdmin/uploads/";

            foreach ($_FILES['editPropertyImages']['tmp_name'] as $key => $tmp_name) {
                $image_name = basename($_FILES['editPropertyImages']['name'][$key]);
                $rootTargetFile = $rootUploadsDir . $image_name;
                $adminTargetFile = $adminUploadsDir . $image_name;

                if (move_uploaded_file($tmp_name, $rootTargetFile)) {
                    copy($rootTargetFile, $adminTargetFile);

                    // Store relative path in the database
                    $dbImagePath = "_images/" . $image_name;
                    $stmt_image = $conn->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                    $stmt_image->bind_param("is", $property_id, $dbImagePath);
                    $stmt_image->execute();
                    $stmt_image->close();
                }
            }
        }

        header("Location: properties.php"); // Redirect to properties.php
    } else {
        echo "Error updating property: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
