<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $property_id = intval($_GET['id']);

    $sql = "SELECT id, title, location, address, price, bedrooms, garage, size, status, description, property_type, selling_type FROM properties WHERE id = $property_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
        echo json_encode($property);
    } else {
        echo json_encode(['error' => 'Property not found']);
    }
}
$conn->close();
?>