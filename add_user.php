<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['Name'];
    $username = $_POST['userName'];
    $email = $_POST['Email'];
    $role = $_POST['userRole'];

    $sql = "INSERT INTO users (username, full_name, email, role) VALUES ('$username', '$name', '$email', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "New user added successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>