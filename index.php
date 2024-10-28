<?php
session_start(); // Start the session to access session variables

// Check if the admin is logged in (check if 'admin_id' is set in the session)
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit(); // Stop further execution
}


// Include the database connection file
include 'db_connection.php';

// Queries to fetch data for the dashboard
// 1. Total Properties
$totalPropertiesQuery = "SELECT COUNT(*) AS total FROM properties";
$totalPropertiesResult = $conn->query($totalPropertiesQuery);
$totalProperties = $totalPropertiesResult->fetch_assoc()['total'];

// 2. Largest Property
$largestPropertyQuery = "SELECT title, size FROM properties ORDER BY size DESC LIMIT 1";
$largestPropertyResult = $conn->query($largestPropertyQuery);
$largestProperty = $largestPropertyResult->fetch_assoc();

// 3. Most Expensive Property
$mostExpensivePropertyQuery = "SELECT title, price FROM properties ORDER BY price DESC LIMIT 1";
$mostExpensivePropertyResult = $conn->query($mostExpensivePropertyQuery);
$mostExpensiveProperty = $mostExpensivePropertyResult->fetch_assoc();

// 4. Cheapest Property
$cheapestPropertyQuery = "SELECT title, price FROM properties ORDER BY price ASC LIMIT 1";
$cheapestPropertyResult = $conn->query($cheapestPropertyQuery);
$cheapestProperty = $cheapestPropertyResult->fetch_assoc();

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Admin Panel</title>
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
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>
                <div class="topbar-left">
                    <h1>Dashboard</h1>
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
            <section id="dashboard" class="dashboard-section">
                <h2>Overview</h2>
                <div class="cards">
                    <div class="card">
                        <h3>Total Properties</h3>
                        <p><?php echo $totalProperties; ?></p>
                    </div>
                    <div class="card">
                        <h3>Largest Property</h3>
                        <p><?php echo $largestProperty['title'] . ' (' . $largestProperty['size'] . ' sq ft)'; ?></p>
                    </div>
                    <div class="card">
                        <h3>Most Expensive Property</h3>
                        <p><?php echo $mostExpensiveProperty['title'] . ' (R' . number_format($mostExpensiveProperty['price'], 2) . ')'; ?></p>
                    </div>
                    <div class="card">
                        <h3>Cheapest Property</h3>
                        <p><?php echo $cheapestProperty['title'] . ' (R' . number_format($cheapestProperty['price'], 2) . ')'; ?></p>
                    </div>
                </div>
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

<?php
$conn->close(); // Close the database connection
?>