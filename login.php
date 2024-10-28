<?php
session_start();
include 'db_connection.php'; // Including the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL statement to retrieve admin data
    $stmt = $conn->prepare("SELECT id, fullname, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // If an admin with the provided username exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $fullname, $hashed_password);
        $stmt->fetch();

        // Verify the password using password_verify() (make sure to hash passwords when stored)
        if (password_verify($password, $hashed_password)) {
            // Store admin session data
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['fullname'] = $fullname;

            // Redirect to the admin dashboard or other protected page
            header('Location: index.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        /* Include your previous CSS code here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        header {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        h1 {
            font-size: 2rem;
            color: #333;
        }

        #login {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 6px;
            font-size: 1rem;
            color: #555;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-size: 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
        }

        .signup-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 1rem;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            margin-top: 10px;
            color: #777;
            font-size: 0.9rem;
        }

        body::before {
            content: "";
            height: 100vh;
            display: block;
        }

        footer {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Portal - Login</h1>
    </header>

    <main>
        <section id="login">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>
            <div class="signup-link">
                <p>Don't have an account? <a href="createAdminProfile.php">Sign Up</a></p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Golden Tigers Realtors. All rights reserved.</p>
    </footer>
</body>
</html>
