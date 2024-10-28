<?php
include 'db_connection.php'; // Include your database connection file
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html"); // Redirect to login page
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch user data from the database
$sql = "SELECT full_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}


// Fetch available agents from the database
$agent_sql = "SELECT id, full_name, username, email, phone_number, area, position, image_path  FROM agents";
$agent_result = $conn->query($agent_sql);

if ($agent_result === false) {
    die("Error executing agent query: " . $conn->error);
}

// Group agents by area
$agents_by_area = [];
$all_agents = [];

while ($agent = $agent_result->fetch_assoc()) {
    $all_agents[] = $agent;  // Store for "Available Agents"
    $agents_by_area[$agent['area']][] = $agent;  // Group by area
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Realtors - Dada Properties</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
            font-size: 18px;
        }

        .logo {
            width: 100px;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .nav-container {
            margin-left: 130px;
            padding-top: 10px;
        }

        nav ul {
            list-style-type: none;
            display: flex;
            justify-content: center;
            font-size: 1.2rem;
        }

        nav ul li {
            position: relative;
            margin-right: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: #fff;
            padding: 10px;
            display: block;
        }

        nav ul li ul {
            position: absolute;
            top: 40px;
            left: 0;
            background-color: #444;
            display: none;
            flex-direction: column;
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
        }

        nav ul li:hover > ul {
            display: block;
        }

        nav ul li ul li {
            margin: 0;
        }

        .profile {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            align-items: center;
            font-size: 1.2rem;
        }

        .profile a {
            color: white;
            text-decoration: none;
            margin-right: 10px;
        }

        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .hero {
            background-image: url('_images/properties-bg-image.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
            padding: 100px 20px;
            text-align: center;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero h2,
        .hero p {
            position: relative;
            z-index: 1;
        }

        .hero h2 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1.2rem;
        }

        .section-container {
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .section-container:nth-child(even) {
            background-color: #333;
            color: #fff;
        }

        .section-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .realtor-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }

        .realtor-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            max-width: 300px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .realtor-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .realtor-card img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            margin-bottom: 15px;
        }

        .realtor-card h3 {
            margin-top: 0;
            font-size: 1.2rem;
            color: #333;
        }

        .realtor-card p {
            margin: 0.5em 0;
            font-size: 0.9rem;
            color: #777;
        }

        .realtor-card .rating {
            margin: 10px 0;
            color: #f39c12;
        }

        .realtor-card .social-icons {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .social-icons a {
            color: #555;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #000;
        }

        .wide-realtor-container {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .wide-realtor-card {
            display: flex;
            flex-direction: row;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: left;
            width: 48%;
            max-width: 48%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .wide-realtor-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .wide-realtor-card img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            margin-right: 20px;
        }

        .wide-realtor-details {
            flex-grow: 1;
        }

        .wide-realtor-details h3 {
            margin-top: 0;
            font-size: 1.2rem;
            color: #333;
        }

        .wide-realtor-details p {
            margin: 0.5em 0;
            font-size: 0.9rem;
            color: #555;
        }

        .wide-realtor-details .rating {
            margin: 10px 0;
            color: #f39c12;
        }

        .wide-realtor-details .social-icons {
            margin-top: 10px;
            display: flex;
            justify-content: left;
            gap: 10px;
        }

        .wide-realtor-details .send-message {
            margin-top: 15px;
            text-align: left;
        }

        .send-message button {
            background-color: #f39c12;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .send-message button:hover {
            background-color: #e08e0b;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1rem;
            position: relative;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .social-media {
            margin-top: 1rem;
        }

        .social-media a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
            font-size: 1.5rem;
            transition: color 0.3s;
        }

        .social-media a:hover {
            color: #ddd;
        }

                /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #f39c12;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e08e0b;
        }
    </style>
</head>

<body>
<header role="banner">
        <img src="_images/aunty sue.png" alt="Golden Tigers Realtors Logo" class="logo">
        <div class="nav-container">
            <nav role="navigation">
            <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="AboutUs.html">About Us</a></li>
                    <li><a href="Properties.php">Properties</a></li>
                    <li><a href="Realtors.php">Our Realtors</a></li>
                    <li><a href="ContactUs.php">Contact Sue</a></li>
                    <li><a href="#">My Account <i class="fas fa-caret-down"></i></a>
                        <ul>
                            <li><a href="AccountSettings.php">Account Settings</a></li>
                            <li><a href="view_inquiry_replies.php">Check Inquiry replies</a></li>
                            <li><a href="view_agent_replies.php">Check messages from agents</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>       
    </header>

    <section class="hero">
        <h2>Meet Our Realtors</h2>
        <p>Discover our team of professional realtors dedicated to helping you find your dream property.</p>
    </section>

    <main>

        <!-- Available Agents Section -->
        <section class="section-container">
            <h2>Available Agents</h2>
            <div class="realtor-container">
            <?php if (!empty($all_agents)): ?>
            <?php foreach ($all_agents as $agent): ?>
                <div class="realtor-card">
                    <img src="<?php echo htmlspecialchars($agent['image_path'] ?? 'default_agent.png'); ?>" 
                    alt="Agent Image" class="agent-img">
                    <h3><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                    <p><?php echo htmlspecialchars($agent['position']); ?></p>
                    <p><?php echo htmlspecialchars($agent['area']); ?></p>
                    <p><?php echo htmlspecialchars($agent['phone_number']); ?></p>
                    <p><?php echo htmlspecialchars($agent['email']); ?></p>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this, <?php echo $agent['id']; ?>)">Send Message</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No agents available at the moment.</p>
        <?php endif; ?>
        </div>
        </section>

        <!-- Featured Realtors Section -->
        <section class="section-container">
            <h2>Featured Realtors</h2>
            <div class="realtor-container">
                <!-- Featured Realtor 1 -->
                <div class="realtor-card">
                    <img src="_images/realtor1.jpg" alt="Realtor 1">
                    <h3>Jeffrey Brown</h3>
                    <p>Creative Leader</p>
                    <p> 0987654321</p>
                        <p>JeffreyBrown@gamil.com</p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
                <!-- Featured Realtor 2 -->
                <div class="realtor-card">
                    <img src="_images/realtor2.jpg" alt="Realtor 2">
                    <h3>Linda Larson</h3>
                    <p>Manager</p>
                    <p> 0982354321</p>
                        <p>LindaLarson@gamil.com</p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
                <!-- Featured Realtor 3 -->
                <div class="realtor-card">
                    <img src="_images/realtor3.jpg" alt="Realtor 3">
                    <h3>Alex Greenfield</h3>
                    <p>Programming Guru</p>
                    <p>0123456789</p>
                    <p>AlexGreenfield@gmail.com</p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9734; <!-- 4 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
                <!-- Featured Realtor 4 -->
                <div class="realtor-card">
                    <img src="_images/realtor4.jpg" alt="Realtor 4">
                    <h3>Anna Richmond</h3>
                    <p>Sales Manager</p>
                    <p>0213457689</p>
                    <p>AnnaRichmond@gmail.com </p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Johannesburg Realtors Section -->
        <section class="section-container">
            <h2>Johannesburg Realtors</h2>
            <div class="wide-realtor-container">

            <?php if (!empty($agents_by_area['Johannesburg'])): ?>
                <?php foreach ($agents_by_area['Johannesburg'] as $agent): ?>
                    <div class="wide-realtor-card">
                        <img src="<?php echo !empty($agent['image_path']) ? htmlspecialchars($agent['image_path']) : 'default_agent.png'; ?>" alt="Agent Image" class="agent-img">
                        <div class="wide-realtor-details">
                        <h3><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                        <p><?php echo htmlspecialchars($agent['position']); ?></p>
                        <p><?php echo htmlspecialchars($agent['phone_number']); ?></p>
                        <p><?php echo htmlspecialchars($agent['email']); ?></p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this, <?php echo $agent['id']; ?>)">Send Message</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No agents available in Johannesburg at the moment.</p>
            <?php endif; ?>

                <!-- Johannesburg Realtor 1 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor5.jpg" alt="Realtor 5">
                    <div class="wide-realtor-details">
                        <h3>Sithembile Dlamini</h3>
                        <p>Area Specialist</p>
                        <p>Expert in the Gauteng area with over 10 years of experience helping clients find their dream homes. Known for exceptional customer service and deep knowledge of local markets.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
                <!-- Johannesburg Realtor 2 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor6.jpg" alt="Realtor 6">
                    <div class="wide-realtor-details">
                        <h3>Jeffrey Brown</h3>
                        <p>Area Specialist</p>
                        <p>Jeffrey's in-depth knowledge of the Gauteng region makes him a trusted advisor for both buyers and sellers.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
                <!-- Johannesburg Realtor 3 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor7.jpg" alt="Realtor 7">
                    <div class="wide-realtor-details">
                        <h3>Linda Larson</h3>
                        <p>Area Specialist</p>
                        <p>Linda's experience in the Gauteng area has helped countless families find their perfect homes.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
                <!-- Johannesburg Realtor 4 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor8.jpg" alt="Realtor 8">
                    <div class="wide-realtor-details">
                        <h3>Alex Greenfield</h3>
                        <p>Area Specialist</p>
                        <p>With his background in tech and real estate, Alex provides innovative solutions to his clients in Gauteng.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9734; <!-- 4 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sandton Realtors Section -->
        <section class="section-container">
            <h2>Sandton Realtors</h2>
            <div class="wide-realtor-container">

            <?php if (!empty($agents_by_area['Sandton'])): ?>
                <?php foreach ($agents_by_area['Sandton'] as $agent): ?>
                    <div class="wide-realtor-card">
                        <img src="<?php echo !empty($agent['image_path']) ? htmlspecialchars($agent['image_path']) : 'default_agent.png'; ?>" alt="Agent Image" class="agent-img">
                        <div class="wide-realtor-details">
                        <h3><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                        <p><?php echo htmlspecialchars($agent['position']); ?></p>
                        <p><?php echo htmlspecialchars($agent['phone_number']); ?></p>
                        <p><?php echo htmlspecialchars($agent['email']); ?></p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this, <?php echo $agent['id']; ?>)">Send Message</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No agents available in Sandton at the moment.</p>
            <?php endif; ?>

                <!-- Sandton Realtor 1 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor9.jpg" alt="Realtor 9">
                    <div class="wide-realtor-details">
                        <h3>Ann Richmond</h3>
                        <p>Area Specialist</p>
                        <p>Ann specializes in the Sandton area, offering her clients expert advice on finding luxury properties.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this)">Send Message</button>
                        </div>
                    </div>
                </div>
                <!-- Sandton Realtor 2 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor10.jpg" alt="Realtor 10">
                    <div class="wide-realtor-details">
                        <h3>Sithembile Dlamini</h3>
                        <p>Area Specialist</p>
                        <p>Sithembile's knowledge of the Sandton market helps her clients make informed decisions about their investments.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this)">Send Message</button>
                        </div>
                    </div>
                </div>
                <!-- Sandton Realtor 3 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor11.jpg" alt="Realtor 11">
                    <div class="wide-realtor-details">
                        <h3>Jeffrey Brown</h3>
                        <p>Area Specialist</p>
                        <p>Jeffrey's expertise in the Sandton area is invaluable to clients looking for luxury homes and investments.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this)">Send Message</button>
                        </div>
                    </div>
                </div>
                <!-- Sandton Realtor 4 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor12.jpg" alt="Realtor 12">
                    <div class="wide-realtor-details">
                        <h3>Linda Larson</h3>
                        <p>Area Specialist</p>
                        <p>Linda’s deep understanding of the Sandton market helps her clients navigate the complexities of buying and selling in the area.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this)">Send Message</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pretoria Realtors Section -->
        <section class="section-container">
            <h2>Pretoria Realtors</h2>
            <div class="wide-realtor-container">

            <?php if (!empty($agents_by_area['Pretoria'])): ?>
                <?php foreach ($agents_by_area['Pretoria'] as $agent): ?>
                    <div class="wide-realtor-card">
                        <img src="<?php echo !empty($agent['image_path']) ? htmlspecialchars($agent['image_path']) : 'default_agent.png'; ?>" alt="Agent Image" class="agent-img">
                        <div class="wide-realtor-details">
                        <h3><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                        <p><?php echo htmlspecialchars($agent['position']); ?></p>
                        <p><?php echo htmlspecialchars($agent['phone_number']); ?></p>
                        <p><?php echo htmlspecialchars($agent['email']); ?></p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this, <?php echo $agent['id']; ?>)">Send Message</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No agents available in Pretoria at the moment.</p>
            <?php endif; ?>

                <!-- Pretoria Realtor 1 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor13.jpg" alt="Realtor 13">
                    <div class="wide-realtor-details">
                        <h3>Alex Greenfield</h3>
                        <p>Area Specialist</p>
                        <p>Alex's tech-savvy approach to real estate makes him a standout in the Pretoria market.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9734; <!-- 4 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this)">Send Message</button>
                        </div>
                    </div>
                </div>
                <!-- Pretoria Realtor 2 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor14.jpg" alt="Realtor 14">
                    <div class="wide-realtor-details">
                        <h3>Ann Richmond</h3>
                        <p>Area Specialist</p>
                        <p>Ann’s extensive network and knowledge of Pretoria make her a key player in the market.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this)">Send Message</button>
                        </div>
                    </div>
                </div>
                <!-- Pretoria Realtor 3 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor15.jpg" alt="Realtor 15">
                    <div class="wide-realtor-details">
                        <h3>Sithembile Dlamini</h3>
                        <p>Area Specialist</p>
                        <p>Sithembile’s knowledge of Pretoria’s real estate market ensures that her clients always receive top-notch service.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this)">Send Message</button>
                        </div>
                    </div>
                </div>
                <!-- Pretoria Realtor 4 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor16.jpg" alt="Realtor 16">
                    <div class="wide-realtor-details">
                        <h3>Jeffrey Brown</h3>
                        <p>Area Specialist</p>
                        <p>Jeffrey’s strategic approach to real estate makes him a top choice for clients in Pretoria.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Krugersdorp Realtors Section -->
        <section class="section-container">
            <h2>Krugersdorp Realtors</h2>
            <div class="wide-realtor-container">

            <?php if (!empty($agents_by_area['Krugersdorp'])): ?>
                <?php foreach ($agents_by_area['Krugersdorp'] as $agent): ?>
                    <div class="wide-realtor-card">
                        <img src="<?php echo !empty($agent['image_path']) ? htmlspecialchars($agent['image_path']) : 'default_agent.png'; ?>" alt="Agent Image" class="agent-img">
                        <div class="wide-realtor-details">
                        <h3><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                        <p><?php echo htmlspecialchars($agent['position']); ?></p>
                        <p><?php echo htmlspecialchars($agent['phone_number']); ?></p>
                        <p><?php echo htmlspecialchars($agent['email']); ?></p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                        <div class="send-message">
                            <button onclick="handleAgentClick(this, <?php echo $agent['id']; ?>)">Send Message</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No agents available in Krugersdorp at the moment.</p>
            <?php endif; ?>

                <!-- Krugersdorp Realtor 1 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor17.jpg" alt="Realtor 17">
                    <div class="wide-realtor-details">
                        <h3>Linda Larson</h3>
                        <p>Area Specialist</p>
                        <p>Linda’s extensive knowledge of the Krugersdorp area ensures that her clients receive the best guidance.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
                <!-- Krugersdorp Realtor 2 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor18.jpg" alt="Realtor 18">
                    <div class="wide-realtor-details">
                        <h3>Alex Greenfield</h3>
                        <p>Area Specialist</p>
                        <p>Alex’s tech-driven approach helps his clients in Krugersdorp make data-informed decisions.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9734; <!-- 4 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
                <!-- Krugersdorp Realtor 3 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor19.jpg" alt="Realtor 19">
                    <div class="wide-realtor-details">
                        <h3>Ann Richmond</h3>
                        <p>Area Specialist</p>
                        <p>Ann’s expertise in the Krugersdorp market allows her to guide her clients to make the best decisions.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
                <!-- Krugersdorp Realtor 4 -->
                <div class="wide-realtor-card">
                    <img src="_images/realtor20.jpg" alt="Realtor 20">
                    <div class="wide-realtor-details">
                        <h3>Sithembile Dlamini</h3>
                        <p>Area Specialist</p>
                        <p>Sithembile’s experience in the Krugersdorp market is invaluable to her clients looking for the perfect home.</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                        </div>
                        <div class="social-icons">
                            <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        
                        </div>
                        <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Realtors Section //DevTeam Section -->
        <section class="section-container">
            <h2>Meet The Development Team</h2>
            <div class="realtor-container">
                <!-- Featured Member 1 -->
                <div class="realtor-card">
                    <img src="_images/realtor1.jpg" alt="Realtor 1">
                    <h3>Patrick Swaezy</h3>
                    <p>Development Team Leader</p>
                    <p> 0994762842</p>
                        <p>PatrickSwae@gamil.com</p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
                <!-- Featured Realtor 2 -->
                <div class="realtor-card">
                    <img src="_images/realtor2.jpg" alt="Realtor 2">
                    <h3>Linda Wilson</h3>
                    <p>Property/Database Manager</p>
                    <p>0982554421</p>
                    <p>LindaWilson@gamil.com</p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
                <!-- Featured Realtor 3 -->
                <div class="realtor-card">
                    <img src="_images/realtor3.jpg" alt="Realtor 3">
                    <h3>Alex Greenfield</h3>
                    <p>Programming Specialist</p>
                    <p>0123456789</p>
                    <p>AlexGreenfield@gmail.com</p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9734; <!-- 4 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
                <!-- Featured Realtor 4 -->
                <div class="realtor-card">
                    <img src="_images/realtor4.jpg" alt="Realtor 4">
                    <h3>Anna Richmond</h3>
                    <p>Business Analyst</p>
                    <p>0213457689</p>
                    <p>AnnaRichmond@gmail.com </p>
                    <div class="rating">
                        &#9733;&#9733;&#9733;&#9733;&#9733; <!-- 5 stars rating -->
                    </div>
                    <div class="social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                    <div class="send-message">
                        <button onclick="handleAgentClick(this)">Send Message</button>
                    </div>
                </div>
            </div>
        </section>

                    <!-- Message Modal -->
            <div id="messageModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Send a Message</h2>
                    <form id="messageForm" action="send_message.php" method="POST">
                        <input type="hidden" id="recipientEmail" name="recipientEmail">
                        <input type="hidden" name="agent_id" value="">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                        <div class="input-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                        </div>
                        <div class="input-group">
                            <label for="email">Your Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        <div class="input-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Write your message here" required></textarea>
                        </div>
                        <button type="submit">Send Message</button>
                    </form>
                </div>
            </div>
    </main>

    

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Dada Properties. All rights reserved.</p>
            <div class="social-media">
                <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            
            </div>
        </div>
    </footer>

    <script>

function handleAgentClick(button, agentId) {
    fetch(`check_agent.php?agent_id=${agentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                openMessageModal(agentId);
            } else {
                alert("This agent is not available at the moment.");
            }
        })
        .catch(error => {
            console.error("Error checking agent availability:", error);
            alert("Something went wrong. Please try again later.");
        });
}

function openMessageModal(agentId) {
    // Set the agent_id in the hidden input field in the form
    document.querySelector('input[name="agent_id"]').value = agentId;
    // Show the modal
    document.getElementById('messageModal').style.display = "block";
}


// Modal close logic
var modal = document.getElementById("messageModal");
var span = document.getElementsByClassName("close")[0];
span.onclick = function () {
    modal.style.display = "none";
};
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};

    

    </script>
</body>
</html>