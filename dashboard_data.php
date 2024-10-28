<?php
include 'db_connection.php';

// Fetch total properties (all properties in the 'properties' table)
$total_properties_query = "SELECT COUNT(*) AS total FROM properties";
$total_properties_result = $conn->query($total_properties_query);
$total_properties = $total_properties_result->fetch_assoc()['total'];

// Fetch sold properties (all properties with status 'sold')
$sold_properties_query = "SELECT COUNT(*) AS sold FROM properties WHERE status = 'sold'";
$sold_properties_result = $conn->query($sold_properties_query);
$sold_properties = $sold_properties_result->fetch_assoc()['sold'];

// Fetch users for visitor analytics (grouped by date they registered)
$visitor_analytics_query = "SELECT DATE(created_at) as date, COUNT(*) as users FROM users GROUP BY DATE(created_at)";
$visitor_analytics_result = $conn->query($visitor_analytics_query);
$visitor_analytics = [];
while ($row = $visitor_analytics_result->fetch_assoc()) {
    $visitor_analytics[] = $row;
}

// Fetch monthly revenue from sold properties
$revenue_query = "
    SELECT 
        MONTH(sold_at) AS month, 
        SUM(price) AS total_revenue 
    FROM properties 
    WHERE status = 'sold' 
    GROUP BY MONTH(sold_at)";
$revenue_result = $conn->query($revenue_query);
$monthly_revenue = [];
while ($row = $revenue_result->fetch_assoc()) {
    $monthly_revenue[] = $row;
}

// Return data as JSON
echo json_encode([
    'total_properties' => $total_properties,
    'sold_properties' => $sold_properties,
    'visitor_analytics' => $visitor_analytics,
    'monthly_revenue' => $monthly_revenue,
]);

$conn->close();
?>
