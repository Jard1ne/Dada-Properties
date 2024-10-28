<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['editUserId'];
    $name = $_POST['editName'] ?? '';
    $username = $_POST['editUserName'] ?? '';
    $email = $_POST['editEmail'] ?? '';
    $role = $_POST['editUserRole'] ?? '';

    // Prevent blank submissions from overwriting existing data
    if (!empty($name) && !empty($username) && !empty($email) && !empty($role)) {
        $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $username, $email, $role, $id);

        if ($stmt->execute()) {
            header("Location: users.php?message=User%20updated%20successfully");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: Missing fields. Please make sure all fields are filled out.";
    }
}
$conn->close();
?>
