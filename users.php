<?php
include 'db_connection.php';

$sql = "
    SELECT 
        u.id, 
        u.full_name, 
        u.username, 
        u.email, 
        u.role, 
        COUNT(i.id) AS inquiry_count
    FROM 
        users u
    LEFT JOIN 
        inquiries i ON u.id = i.user_id
    GROUP BY 
        u.id;
";

$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
                    <h1>User Management</h1>
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
            <section class="users-section">
                <h2>Manage Users</h2>
                <button class="add-btn" onclick="openAddUserModal()">Add User</button>
                <div class="user-list">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="user-item" data-user-id="<?php echo $row['id']; ?>">
                                <h3><?php echo htmlspecialchars($row['full_name']); ?> (<?php echo htmlspecialchars($row['username']); ?>)</h3>
                                <p>Email: <?php echo htmlspecialchars($row['email']); ?></p>
                                <p>Role: <?php echo htmlspecialchars($row['role']); ?></p>
                                <p>Inquiries Made: <?php echo $row['inquiry_count']; ?></p>
                                <button class="edit-btn" onclick="openEditUserModal(<?php echo $row['id']; ?>)">Edit</button>
                                <button class="delete-btn" onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No users found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Modals for adding and editing users -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddUserModal()">&times;</span>
            <h2>Add User</h2>
            <form id="addUserForm" method="POST" action="add_user.php">
                <label for="Name">Full Name:</label>
                <input type="text" id="Name" name="Name" required>
                <label for="userName">Username:</label>
                <input type="text" id="userName" name="userName" required>
                <label for="Email">Email:</label>
                <input type="email" id="Email" name="Email" required>
                <label for="userRole">Role:</label>
                <select id="userRole" name="userRole" required>
                    <option value="user">User</option>
                    <option value="agent">Agent</option>
                </select>
                <button type="submit">Add User</button>
            </form>
        </div>
    </div>

    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditUserModal()">&times;</span>
            <h2>Edit User</h2>
            <form id="editUserForm" method="POST" action="edit_user.php">
                <input type="hidden" id="editUserId" name="editUserId">
                <label for="editName">Full Name:</label>
                <input type="text" id="editName" name="editName">
                <label for="editUserName">Username:</label>
                <input type="text" id="editUserName" name="editUserName">
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="editEmail" >
                <label for="editUserRole">Role:</label>
                <select id="editUserRole" name="editUserRole">
                    <option value="user">User</option>
                    <option value="agent">Agent</option>
                </select>
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