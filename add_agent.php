<?php
include 'db_connection.php';
session_start(); // Start session for setting messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['Name'];
    $username = $_POST['userName'];
    $email = $_POST['Email'];
    $position = $_POST['Position'];
    $area = $_POST['Area'];
    $phone_number = $_POST['PhoneNumber'];

    // Prepare directories for image uploads
    $rootUploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/GoldenTigersRealtorsWebsite-20240414T094636Z-001/Sue@GoldenTigerRealtors/_images/";
    $adminUploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/GodenRealtorsAdmin/uploads/";

    // Ensure directories exist
    if (!file_exists($rootUploadsDir)) {
        mkdir($rootUploadsDir, 0777, true);
    }
    if (!file_exists($adminUploadsDir)) {
        mkdir($adminUploadsDir, 0777, true);
    }

    $imagePath = null; // Default value
    // Check if file upload was attempted
    if (isset($_FILES['AgentImage']) && $_FILES['AgentImage']['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($_FILES["AgentImage"]["name"]);
        $rootTargetFile = $rootUploadsDir . $image_name;
        $adminTargetFile = $adminUploadsDir . $image_name;

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES["AgentImage"]["tmp_name"], $rootTargetFile)) {
            copy($rootTargetFile, $adminTargetFile);
            $imagePath = "_images/" . $image_name; // Store relative path for use in the database
        } else {
            die("Error: Failed to move uploaded file.");
        }
    } else {
        die("Error: No file was uploaded or other upload error: " . $_FILES['AgentImage']['error']);
    }

    // Insert agent data into the database
    $sql = "INSERT INTO agents (full_name, username, email, position, area, phone_number, image_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssss", $name, $username, $email, $position, $area, $phone_number, $imagePath);

        if ($stmt->execute()) {
            header("Location: agents.php?message=Agent%20added%20successfully");
            exit();
        } else {
            die("Error executing query: " . $stmt->error);
        }
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }

    $conn->close();
}
?>
