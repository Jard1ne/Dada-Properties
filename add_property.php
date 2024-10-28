<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $title = $_POST['propertyTitle'];
    $location = $_POST['propertyLocation'];
    $address = $_POST['propertyAddress'];
    $price = $_POST['propertyPrice'];
    $bedrooms = $_POST['propertyBedrooms'];
    $garages = $_POST['propertyGarages'];
    $size = $_POST['propertySize'];
    $status = $_POST['propertyStatus'];
    $description = $_POST['propertyDescription'];
    $property_type = $_POST['propertyType'];
    $selling_type = $_POST['propertySellingType'];

    // Insert property data into the 'properties' table
    $sql = "INSERT INTO properties (title, location, address, price, bedrooms, garage, size, status, description, property_type, selling_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

   

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssdiiissss", $title, $location, $address, $price, $bedrooms, $garages, $size, $status, $description, $property_type, $selling_type);
            if ($stmt->execute()) {
                $property_id = $stmt->insert_id; // Get the last inserted ID
    
                // Define directories for image uploads
                $rootUploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/GoldenTigersRealtorsWebsite-20240414T094636Z-001/Sue@GoldenTigerRealtors/_images/" ;
                $adminUploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/GodenRealtorsAdmin/uploads/";
    
                // Ensure directories exist
                if (!file_exists($rootUploadsDir)) {
                    mkdir($rootUploadsDir, 0777, true);
                }
                if (!file_exists($adminUploadsDir)) {
                    mkdir($adminUploadsDir, 0777, true);
                }
    
                // Handle multiple image uploads
                foreach ($_FILES['propertyImages']['tmp_name'] as $key => $tmp_name) {
                    $image_name = basename($_FILES['propertyImages']['name'][$key]);
                    $rootTargetFile = $rootUploadsDir . $image_name;
                    $adminTargetFile = $adminUploadsDir . $image_name;
    
                    if (move_uploaded_file($tmp_name, $rootTargetFile)) {
                        copy($rootTargetFile, $adminTargetFile);
    
                        // Store relative path in the database
                        $dbImagePath = "_images/" . $image_name;
                        $sql_image = "INSERT INTO property_images (property_id, image_path) VALUES (?, ?)";
                        if ($stmt_image = $conn->prepare($sql_image)) {
                            $stmt_image->bind_param("is", $property_id, $dbImagePath);
                            $stmt_image->execute();
                            $stmt_image->close();
                        }
                    }
                }
    

            // Redirect to properties.php with success message
            header("Location: properties.php?insert=success");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
