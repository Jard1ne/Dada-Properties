<?php
include 'db_connection.php';

// Query to get user registrations per month for the current year
$year = date('Y');
$visitor_query = "
    SELECT MONTH(created_at) AS month, COUNT(id) AS registrations 
    FROM users 
    WHERE YEAR(created_at) = ? 
    GROUP BY MONTH(created_at)";

$stmt = $conn->prepare($visitor_query);
$stmt->bind_param("i", $year);
$stmt->execute();
$visitor_result = $stmt->get_result();

$visitor_data = [];
while ($row = $visitor_result->fetch_assoc()) {
    $visitor_data[] = [
        'month' => $row['month'],
        'registrations' => $row['registrations']
    ];
}

// Query to get enquiry statistics (pending and responded enquiries)
$enquiry_query = "
    SELECT 
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending, 
        SUM(CASE WHEN status = 'responded' THEN 1 ELSE 0 END) AS responded 
    FROM enquiries";

$enquiry_result = $conn->query($enquiry_query);
$enquiry_data = $enquiry_result->fetch_assoc();

$response = [
    'visitor_data' => $visitor_data,
    'enquiry_data' => $enquiry_data
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
