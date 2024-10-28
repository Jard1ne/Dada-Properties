<?php
include 'db_connection.php';

// Fetch agents with their message counts
$sql = "
    SELECT 
        a.id, 
        a.full_name, 
        a.username, 
        a.email, 
        a.phone_number,
        a.position,
        a.area,
        COUNT(am.id) AS message_count
    FROM 
        agents a
    LEFT JOIN 
        agent_messages am ON a.id = am.agent_id
    GROUP BY 
        a.id;
";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$message = "";
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- Trigger JavaScript alert for success message -->
<?php if (!empty($message)): ?>
    <script>
        showMessage('<?php echo $message; ?>');
    </script>
<?php endif; ?>

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
                    <h1>Agent Management</h1>
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
            <section class="agents-section">
                <h2>Manage Agents</h2>
                <button class="add-btn" onclick="openAddAgentModal()">Add Agent</button>
                <div class="agent-list">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="agent-item" data-agent-id="<?php echo $row['id']; ?>">
                                                                <!-- Display agent image if available -->
                                    <?php if (!empty($row['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Agent Image" width="100">
                                    <?php else: ?>
                                        <img src="default_agent.png" alt="Default Agent Image" width="100">
                                    <?php endif; ?>
                                <h3><?php echo htmlspecialchars($row['full_name']); ?> (<?php echo htmlspecialchars($row['username']); ?>)</h3>
                                <p>Email: <?php echo htmlspecialchars($row['email']); ?></p>
                                <p>Phone Number: <?php echo htmlspecialchars($row['phone_number']); ?></p>
                                <p>Area: <?php echo htmlspecialchars($row['area']); ?></p>
                                <p>Position: <?php echo htmlspecialchars($row['position']); ?></p>
                                <p>Messages Received: <?php echo $row['message_count']; ?></p>

                                <!-- Check Messages Button -->
                                <form action="view_agent_messages.php" method="GET">
                                    <input type="hidden" name="agent_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="check-messages-btn">Check Messages</button>
                                </form>

                                <button class="edit-btn" onclick="openEditAgentModal(<?php echo $row['id']; ?>)">Edit</button>
                                <button class="delete-btn" onclick="deleteAgent(<?php echo $row['id']; ?>)">Delete</button>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No agents found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Modals for adding and editing agents -->
    <div id="addAgentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddAgentModal()">&times;</span>
            <h2>Add Agent</h2>
            <form id="addAgentForm" method="POST" action="add_agent.php" enctype="multipart/form-data">
                <label for="AgentImage">Agent Image:</label>
                <input type="file" id="AgentImage" name="AgentImage" required>
                <label for="Name">Full Name:</label>
                <input type="text" id="Name" name="Name" required>
                <label for="userName">Username:</label>
                <input type="text" id="userName" name="userName" required>
                <label for="Email">Email:</label>
                <input type="email" id="Email" name="Email" required>
                <label for="PhoneNumber">Phone Number:</label>
                <input type="int" id="PhoneNumber" name="PhoneNumber" required>
                <label for="Position">Position:</label>
                <input type="text" id="Position" name="Position" required>
                <label for="Area">Area:</label>
                <input type="text" id="Area" name="Area" required>
                <button type="submit">Add Agent</button>
            </form>
        </div>
    </div>

    <div id="editAgentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditAgentModal()">&times;</span>
            <h2>Edit Agent</h2>
            <form method="POST" action="edit_agent.php" enctype="multipart/form-data">
                <input type="hidden" id="editAgentId" name="editAgentId">
                <label for="editAgentImage">Agent Image (Optional):</label>
                <input type="file" id="editAgentImage" name="editAgentImage" accept="image/*">
                <label for="editName">Full Name:</label>
                <input type="text" id="editName" name="editName">
                <label for="editUserName">Username:</label>
                <input type="text" id="editUserName" name="editUserName">
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="editEmail">
                <label for="editPhoneNumber">Phone Number:</label>
                <input type="int" id="editPhoneNumber" name="editPhoneNumber">
                <label for="editPosition">Position:</label>
                <input type="text" id="editPosition" name="editPosition">
                <label for="editArea">Area:</label>
                <input type="text" id="editArea" name="editArea">
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>



<?php
$conn->close();
?>














   