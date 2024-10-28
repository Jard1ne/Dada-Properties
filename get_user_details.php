<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare and execute the query to fetch user information
    $stmt = $conn->prepare("SELECT id, full_name, username, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user data was found and return as JSON
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        echo json_encode($userData);
    } else {
        echo json_encode(["error" => "User not found"]);
    }

    $stmt->close();
}
$conn->close();
?>
