<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// Update the user's profile in the database
$sql = "UPDATE users SET full_name = ?, username = ?, email = ?, phone = ?, address = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $full_name, $username, $email, $phone, $address, $user_id);

if ($stmt->execute()) {
    echo "Profile updated successfully!";
    header("Location: AccountSettings.php"); // Redirect back to settings page
} else {
    echo "Error updating profile: " . $conn->error;
}

$stmt->close();
$conn->close();
?>