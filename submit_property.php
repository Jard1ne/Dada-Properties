<?php

session_start();
include 'db_connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: Login.html");
    exit();
}

// Fetch user details from session or database
$user_id = $_SESSION['user_id'];

// Check if the session variables for full_name, email, and phone_number are set
if (!isset($_SESSION['full_name']) || !isset($_SESSION['email']) || !isset($_SESSION['phone'])) {
    // Query the database to get the user's full name, email, and phone
    $sql = "SELECT full_name, email, phone FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'];
    } else {
        echo "Error: User details not found.";
        exit();
    }
    
    $stmt->close();
}

// Now fetch the session values
$full_name = $_SESSION['full_name'];
$email = $_SESSION['email'];
$phone_number = $_SESSION['phone'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch property details from POST request (submitted form data)
    $title = $_POST['property-title'];
    $location = $_POST['property-location'];
    $address = $_POST['property-address'];
    $price = $_POST['property-price'];
    $selling_type = $_POST['selling-type'];
    $property_type = $_POST['property-type'];
    $other_property_type = $_POST['other-property-type'];
    $bedrooms = $_POST['property-bedrooms'];
    $bathrooms = $_POST['property-bathrooms'];
    $garage = $_POST['property-garage'];
    $size = $_POST['property-size'];
    $description = $_POST['property-description'];

    // Handle file upload for images
    $image_names = [];
    $upload_directory = $_SERVER['DOCUMENT_ROOT'] . "/GoldenTigersRealtorsWebsite-20240414T094636Z-001/Sue@GoldenTigerRealtors/_images/" ;

    // Ensure the uploads directory exists
    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0755, true);
    }

    foreach ($_FILES['property-images']['name'] as $key => $image_name) {
        $tmp_name = $_FILES['property-images']['tmp_name'][$key];
        $target_file = $upload_directory . basename($image_name);

        // Check for upload errors
        if ($_FILES['property-images']['error'][$key] === UPLOAD_ERR_OK) {
            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_names[] = $target_file;  // Store the image path
            } else {
                echo "Error moving file: $image_name";
            }
        } else {
            echo "Error uploading file: $image_name";
        }
    }

    $images = implode(",", $image_names);  // Convert image paths array to a comma-separated string

    // Insert the submission into the `property_submissions` table
    $sql = "INSERT INTO property_submissions 
            (user_id, full_name, email, phone_number, title, location, address, price, selling_type, property_type, other_property_type, bedrooms, bathrooms, garage, size, description, images) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssssssssiiidss', $user_id, $full_name, $email, $phone_number, $title, $location, $address, $price, $selling_type, $property_type, $other_property_type, $bedrooms, $bathrooms, $garage, $size, $description, $images);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the form page with a success message
        header("Location: SubmitProperty.php?inquiry=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

}
?>
