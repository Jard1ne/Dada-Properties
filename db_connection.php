<?php
$servername = "Localhost";
$username = "root";
$password = "thekyle3104";
$dbname = "golden_tigers";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


