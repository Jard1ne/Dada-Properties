<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['agentId'];

    $sql = "DELETE FROM agents WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Agent deleted successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>
