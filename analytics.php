<?php
// Database connection
include 'db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetching user data
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(id) AS user_count FROM users GROUP BY month ORDER BY month";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$months = [];
$userCounts = [];

while ($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $userCounts[] = $row['user_count'];
}

// Total inquiries
$totalQuery = "SELECT COUNT(*) AS total FROM inquiries";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalInquiries = $totalRow['total'];

// Inquiries replied to
$repliedQuery = "SELECT COUNT(*) AS replied FROM inquiries WHERE response IS NOT NULL";
$repliedResult = $conn->query($repliedQuery);
$repliedRow = $repliedResult->fetch_assoc();
$repliedInquiries = $repliedRow['replied'];

// Inquiries pending response
$pendingInquiries = $totalInquiries - $repliedInquiries;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="properties.php"><i class="fas fa-building"></i> Properties</a></li>
                <li><a href="SubmittedProperties.php"><i class="fas fa-file-alt"></i> Submitted Properties</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="agents.php" class="active"><i class="fas fa-user-tie"></i> Agents</a></li>
                <li><a href="enquiries.php"><i class="fas fa-envelope"></i> Enquiries</a></li>
                <li><a href="analytics.php"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li><a href="view_contact_messages.php"><i class="fas fa-envelope"></i> View Contact Messages</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <h1>Analytics</h1>
                </div>
                <div class="topbar-right">
                    <div class="notifications">
                       
                    </div>
                    <div class="user-profile">
                        <img src="profile.jpg" alt="User">
                        <span>Admin</span>
                        <a href="logout.php" class="logout-btn">Logout</a> <!-- Add a logout button -->
                    </div>
                </div>
            </header>
            <section class="analytics-section">
                <h2>Website Analytics</h2>
                <div class="charts">
                    <div class="chart">
                        <h3>Visitor Analytics</h3>
                        <canvas id="visitorChart"></canvas>
                    </div>
                    <div class="chart">
                        <h3>Revenue Statistics</h3>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('visitorChart').getContext('2d');
        var visitorChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'User Registrations',
                    data: <?php echo json_encode($userCounts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        
    </script>

<script>
var ctx = document.getElementById('revenueChart').getContext('2d');
var revenueChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Total Inquiries', 'Inquiries Replied', 'Inquiries Pending'],
        datasets: [{
            label: 'Inquiries Statistics',
            data: [<?php echo $totalInquiries; ?>, <?php echo $repliedInquiries; ?>, <?php echo $pendingInquiries; ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
        }
    }
});
</script>


<script src="scripts.js"></script>
</body>
</html>
