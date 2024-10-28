<?php
// Start the session
session_start();

// Check if an admin is logged in
if (isset($_SESSION['admin_id'])) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();
    
    // Redirect to login page or homepage
    header("Location: login.php");
    exit();
} else {
    // If no admin session exists, redirect to login page
    header("Location: login.php");
    exit();
}
?>
