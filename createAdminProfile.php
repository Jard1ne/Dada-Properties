<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new admin into the database
    $stmt = $conn->prepare("INSERT INTO admin (fullname, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $username, $hashed_password);

    if ($stmt->execute()) {
        echo "Admin profile created successfully!";
        // Optionally redirect to login or admin dashboard
        header('Location: login.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Profile</title>
    <style>
        /* Include your previous CSS code here */
        /* Resetting some default styles */
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
            font-size: 2rem; /* Adjust the size of the title */
            color: #333;
        }

        #login {
            background-color: white;
            padding: 30px; /* Adjust padding for a compact layout */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px; /* Smaller width */
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.5rem; /* Smaller subtitle */
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 6px;
            font-size: 1rem; /* Smaller label text */
            color: #555;
        }

        input[type="text"], input[type="password"] {
            padding: 10px; /* Reduced padding for compact fields */
            margin-bottom: 15px;
            font-size: 1rem; /* Reduced input text size */
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
            font-size: 1.2rem; /* Smaller button text */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            margin-top: 10px;
            color: #777;
            font-size: 0.9rem; /* Smaller footer text */
        }

        /* Footer fixed at the bottom */
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
        <h1>Create Admin Profile</h1>
    </header>

    <main>
        <section id="create-profile">
            <h2>Create Admin Profile</h2>
            <form action="createAdminProfile.php" method="POST">
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Create Profile</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Golden Tigers Realtors. All rights reserved.</p>
    </footer>
</body>
</html>
