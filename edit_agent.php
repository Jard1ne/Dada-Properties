<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agent_id = $_POST['editAgentId'];
    $fields_to_update = [];

    

    // Check each field before adding it to the update array
    if (!empty($_POST['editName'])) {
        $name = $conn->real_escape_string($_POST['editName']);
        $fields_to_update[] = "full_name='$name'";
    }

    if (!empty($_POST['editUserName'])) {
        $username = $conn->real_escape_string($_POST['editUserName']);
        $fields_to_update[] = "username='$username'";
    }

    if (!empty($_POST['editEmail'])) {
        $email = $conn->real_escape_string($_POST['editEmail']);
        $fields_to_update[] = "email='$email'";
    }

    if (!empty($_POST['editPhoneNumber'])) {
        $phone = $conn->real_escape_string($_POST['editPhoneNumber']);
        $fields_to_update[] = "phone_number='$phone'";
    }

    if (!empty($_POST['editPosition'])) {
        $position = $conn->real_escape_string($_POST['editPosition']);
        $fields_to_update[] = "position='$position'";
    }

    if (!empty($_POST['editArea'])) {
        $area = $conn->real_escape_string($_POST['editArea']);
        $fields_to_update[] = "area='$area'";
    }

    // Handle image upload (optional)
    if (isset($_FILES['editAgentImage']) && $_FILES['editAgentImage']['error'] === UPLOAD_ERR_OK) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES["editAgentImage"]["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . "." . $imageFileType; // Generate a unique name for the image
        $target_file = $target_dir . $newFileName;

        if (move_uploaded_file($_FILES["editAgentImage"]["tmp_name"], $target_file)) {
            $imagePath = "/uploads/" . $newFileName; // Store relative path
            $fields_to_update[] = "image_path='$imagePath'";
        } else {
            echo "Error uploading the image. Please check file permissions.";
            exit(); // Stop further execution if the image upload fails
        }
    }

     // Check if updates are not empty and perform the update
     if (!empty($fields_to_update)) {
        $sql = "UPDATE agents SET " . implode(", ", $fields_to_update) . " WHERE id='$agent_id'";

        if ($conn->query($sql) === TRUE) {
            // Redirect with a success message
            header("Location: agents.php?message=Agent updated successfully");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "No changes were made. Please ensure at least one field is updated.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
