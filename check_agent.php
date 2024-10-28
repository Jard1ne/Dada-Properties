<?php
include 'db_connection.php'; // Include your database connection file

if (isset($_GET['agent_id'])) {
    $agent_id = intval($_GET['agent_id']); // Sanitize input

    $stmt = $conn->prepare("SELECT id FROM agents WHERE id = ?");
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the agent exists
    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['exists' => false]);
}
