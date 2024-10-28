<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO users (full_name, username, email, password, phone, address) VALUES ('$full_name','$username', '$email', '$password', '$phone', '$address')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to login page with success message in the query string
        header("Location: Login.html?signup=success");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>