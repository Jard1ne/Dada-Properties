
<?php
include 'db_connection.php';

// Get property ID from the URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($property_id > 0) {
    // Fetch property details from the database
    $property_query = "
        SELECT 
            p.title, 
            p.location, 
            p.price, 
            p.bedrooms,  
            p.garage, 
            p.size, 
            p.description 
        FROM properties p 
        WHERE p.id = ?";

    // Prepare the query
    $stmt = $conn->prepare($property_query);

    // Check if the prepare was successful
    if (!$stmt) {
        // Display error message if prepare failed
        die("SQL Error: " . $conn->error);
    }

    // Bind the parameter
    $stmt->bind_param('i', $property_id);
    $stmt->execute();
    $property_result = $stmt->get_result();

    if ($property_result->num_rows > 0) {
        $property = $property_result->fetch_assoc();
        
        // Fetch property images
        $image_query = "SELECT image_path FROM property_images WHERE property_id = ?";
        $stmt = $conn->prepare($image_query);
        $stmt->bind_param('i', $property_id);
        $stmt->execute();
        $image_result = $stmt->get_result();

        $images = [];
        while ($row = $image_result->fetch_assoc()) {
            $images[] = $row['image_path'];
        }
    } else {
        echo "Property not found.";
        exit;
    }
} else {
    echo "Invalid property ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - Golden Tigers Realtors</title>
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

        .back-button,
        .inquire-button {
            margin: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-button:hover,
        .inquire-button:hover {
            background-color: #555;
        }

        .property-details {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
        }

        .property-details img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .property-details h2 {
            margin-top: 0;
            font-size: 2rem;
            color: #333;
        }

        .property-details p {
            font-size: 1rem;
            margin: 0.5rem 0;
        }

        .property-details .price {
            font-size: 1.5rem;
            color: #ad4a03;
        }

        .property-icons {
            display: flex;
            justify-content: space-around;
            margin-top: 1rem;
        }

        .property-icons div {
            display: flex;
            align-items: center;
            font-size: 1.2rem;
            padding: 0 1rem;
        }

        .property-icons div i {
            margin-right: 0.5rem;
            font-size: 1.5rem;
        }

        .property-gallery {
            display: flex;
            overflow-x: auto;
            gap: 10px;
        }

        .property-gallery img {
            max-width: 200px;
            border-radius: 10px;
            margin-bottom: 20px;
            cursor: pointer;
            flex: 0 0 auto;
        }

        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90%;
        }

        .lightbox:target {
            display: flex;
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transform: translateY(-50%);
        }

        .lightbox-nav button {
            background: none;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            padding: 0 20px;
        }

        .lightbox-nav button:hover {
            color: #ccc;
        }

        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
        }

        .lightbox-close:hover {
            color: #ccc;
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

    <main>
    <section class="property-details">
        <h2><?php echo htmlspecialchars($property['title']); ?></h2>
        <p>Location: <?php echo htmlspecialchars($property['location']); ?></p>
        <p>Price: R<?php echo number_format($property['price'], 2); ?></p>
        <div class="property-icons">
            <div><i class="fas fa-bed"></i> <?php echo htmlspecialchars($property['bedrooms']); ?> Bedrooms</div>
            <div><i class="fas fa-car"></i> <?php echo htmlspecialchars($property['garage']); ?> Garage</div>
            <div><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($property['size']); ?> sq ft</div>
        </div>
        <p><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>

        <div class="property-gallery">
            <?php
            if (!empty($images)) {
                foreach ($images as $image) {
                    echo '<img src="' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($property['title']) . '">';
                }
            } else {
                echo '<p>No images available for this property.</p>';
            }
            ?>
        </div>
    </section>

        <div id="lightbox" class="lightbox">
            <div class="lightbox-nav">
                <button id="prev-btn" onclick="navigateLightbox(-1)">&#10094;</button>
                <button id="next-btn" onclick="navigateLightbox(1)">&#10095;</button>
            </div>
            <img id="lightbox-img" src="" alt="Enlarged Image" onclick="closeLightbox()">
            <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        </div>

        <button class="back-button" onclick="goBack()">Back</button>
        <button class="inquire-button" onclick="inquire()">Inquire</button>
    </main>

    

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Sue@GoldenTigersRealtors. All rights reserved.</p>
            <div class="social-media">
                <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

    <script>
        
    function inquire() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const propertyId = urlParams.get('id'); // Assuming the property ID is in the URL

        // Redirect to the inquiry page with the property ID
        window.location.href = `Inquiry.php?id=${propertyId}`;
    }

    // Function to display success message if the inquiry was submitted
    function showInquirySuccessMessage() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('inquiry') && urlParams.get('inquiry') === 'success') {
                alert('Your inquiry has been submitted successfully!');
            }
        }

        // Call the function to show the success message when the page loads
        window.onload = showInquirySuccessMessage;

        // Sample property data
        const properties = {
            'property1': {
                images: [
                    '_images/WhatsApp Image 2024-04-15 at 8.36.14 PM.jpeg',
                    '_images/ruimsig (2).png',
                    '_images/ruimsig (3).png',
                    '_images/ruimsig (4).png',
                    '_images/ruimsig (5).png',
                    '_images/ruimsig (6).png',
                    '_images/ruimsig (7).png',
                    '_images/ruimsig (8).png',
                    '_images/ruimsig (9).png',
                    '_images/ruimsig (10).png',
                    '_images/ruimsig (11).png',
                    '_images/ruimsig (12).png',
                    '_images/ruimsig (13).png',
                    '_images/ruimsig (14).png',
                    '_images/ruimsig (15).png',
                    '_images/ruimsig.png'
                ],
                title: 'Ruimsig House',
                location: 'Ruimsig, Roodepoort, Gauteng',
                price: 'R4 900 000',
                bedrooms: 4,
                bathrooms: 3,
                garage: 2,
                size: '450m²',
                description: "Welcome to this warm and lovingly maintained home in Serengity Golf Estate, centrally located in Ruimsig, just minutes from Hendrik Potgieter. This property boasts a prime location, secure setting, and excellent value. The impressive design includes ample parking, two double garages, and a luxurious entrance with a staircase and chandelier. The bright, open-plan kitchen features modern dark finishes, granite countertops, space for a double-door fridge, and a separate scullery. The dining and lounge areas lead to a study, built-in bar, and covered patio overlooking a sparkling pool and lush garden. Upstairs, the spacious master bedroom includes walk-in wardrobes, a full en-suite bathroom, and a sunny balcony. There are three additional generous bedrooms, all with en-suite bathrooms, and an extra music room that can also serve as a pajama lounge. Additional features include air conditioning, fiber readiness, staff quarters, and 24-hour security. Ruimsig is conveniently close to multiple private schools, numerous shopping centers, medical facilities, restaurants, and main road access."
            },
            'property2': {
                images: [
                    '_images/Struben1_House.png',
                    '_images/Struben1_garage.png',
                    '_images/Struben1_inside(b).png',
                    '_images/Struben1_inside(c).png',
                    '_images/Struben1_inside(d).png',
                    '_images/Struben1_inside(e).png',
                    '_images/Struben1_outside(a).png',
                    '_images/Struben1_garage.png'
                ],
                title: 'Strubensvalley House',
                location: 'Strubensvalley, Gauteng',
                price: 'R 3 299 000',
                bedrooms: 5,
                bathrooms: 3,
                garage: 3,
                size: '550m²',
                description: 'Elegance meets functionality in this 5-bedroom gem in Strubensvallei. Beyond the wrought-iron gates, this luxurious home features spacious bedrooms, 2.5 bathrooms, including an exclusive master en-suite, and a massive kitchen with modern finishes and a large scullery. Entertain effortlessly in the expansive family lounge, dedicated entertainment room, and outdoor space with a swimming pool, jacuzzi, and built-in braai. Additional amenities include a study, three-car garage, garden shed, and servants quarters. Located on a tranquil street, this property blends peace with accessibility, offering a refined living experience. Contact us for an exclusive viewing and experience luxurious living at its finest.'
            },
            'property3': {
                images: [
                    '_images/constantiaKloof (2).png',
                    '_images/constantiaKloof (3).png',
                    '_images/constantiaKloof (4).png',
                    '_images/constantiaKloof (5).png',
                    '_images/constantiaKloof (6).png',
                    '_images/constantiaKloof (7).png',
                    '_images/constantiaKloof (8).png',
                    '_images/constantiaKloof (9).png',
                    '_images/constantiaKloof (10).png',
                    '_images/constantiaKloof (11).png',
                    '_images/constantiaKloof (12).png',
                    '_images/constantiaKloof (13).png',
                    '_images/constantiaKloof (14).png',
                    '_images/constantiaKloof (15).png',
                    '_images/constantiaKloof (16).png',
                    '_images/constantiaKloof.png'
                ],
                title: 'Constantia Kloof House',
                location: 'Constantia Kloof, Roodepoort, Gauteng',
                price: 'R2 498 000',
                bedrooms: 5,
                bathrooms: 3,
                garage: 2,
                size: '450m²',
                description: "Discover the charm of this spacious 5-bedroom, 3.5-bathroom family home in Constantia Kloof. Set on a generous 1867m2 property, it offers ample space inside and out. Practicality meets timeless character with plenty of parking, a classic entrance hall, and a large entertainment room downstairs. Upstairs, enjoy a sunroom, dining room with courtyard views, and a spacious kitchen leading to the lounge and outdoor pool area. The master bedroom features a full en-suite with garden access, while additional bedrooms offer cozy retreats. With a big garden, storage facilities, and 2 domestic quarters, this home is ideal for family living. While not the epitome of luxury, its potential and classic charm make it a great opportunity. Schedule a viewing today and envision making this your own!"
            },
            'property4': {
                images: [
                    '_images/weltevreden (2).png',
                    '_images/weltevreden (3).png',
                    '_images/weltevreden (4).png',
                    '_images/weltevreden (5).png',
                    '_images/weltevreden (6).png',
                    '_images/weltevreden (7).png',
                    '_images/weltevreden (8).png',
                    '_images/weltevreden (9).png',
                    '_images/weltevreden (10).png',
                    '_images/weltevreden (11).png',
                    '_images/weltevreden (12).png',
                    '_images/weltevreden.png'
                ],
                title: 'Weltevreden Park House',
                location: 'Weltevreden Park, Roodepoort, Gauteng',
                price: 'R1 499 000',
                bedrooms: 3,
                bathrooms: 3,
                garage: 2,
                size: '457m²',
                description: "Immerse yourself in luxury and comfort with this exquisite 3-bedroom, 3-bathroom home, meticulously designed to enhance your lifestyle. Upon entering, the expansive living area welcomes you with warmth and ample natural light, perfect for relaxation or entertaining. A stylish dining room and separate bar area offer ideal settings for intimate dinners and social gatherings. The heart of the home, the spacious kitchen, boasts modern appliances and ample counter space, inspiring culinary creativity. A versatile fourth bedroom doubles as a spacious office with its ensuite bathroom, offering functionality and privacy. Outside, the wrap-around garden, complete with a stunning patio and large pool, provides a picturesque oasis for relaxation. A double carport ensures shelter for your vehicles, adding convenience to this epitome of modern living."
            },
            'property5': {
                images: [
                    '_images/little falls (9).png',
                    '_images/little falls (8).png',
                    '_images/little falls (7).png',
                    '_images/little falls (6).png',
                    '_images/little falls (5).png',
                    '_images/little falls (4).png',
                    '_images/little falls (3).png',
                    '_images/little falls (2).png',
                    '_images/little falls.png'
                ],
                title: 'Little Falls House',
                location: 'Little Falls, Roodepoort, Gauteng',
                price: 'R899 000',
                bedrooms: 3,
                bathrooms: 2,
                garage: 2,
                size: '350m²',
                description: "Welcome to this charming simplex townhouse nestled in a sought-after complex, promising comfort and convenience. Inside, an inviting open plan lounge awaits, ideal for relaxation or entertaining guests. The modern kitchen offers ample cupboard space and a dining area, perfect for family meals. Step onto the covered patio to enjoy the fresh air and overlook the neat, small garden, ideal for outdoor gatherings or unwinding. Three cozy bedrooms and a full bathroom cater to family needs, while a single carport, secured behind a gate, ensures convenient parking. Residents can also enjoy the communal swimming pool area for a refreshing oasis on sunny days. Don't miss the chance to call this delightful townhouse your new home sweet home!"
            },
            'property6': {
                images: [
                    '_images/Florida Glen (2).png',
                    '_images/Florida Glen (3).png',
                    '_images/Florida Glen (4).png',
                    '_images/Florida Glen (5).png',
                    '_images/Florida Glen (6).png',
                    '_images/Florida Glen (7).png',
                    '_images/Florida Glen (8).png',
                    '_images/Florida Glen (9).png',
                    '_images/Florida Glen (10).png',
                    '_images/Florida Glen (11).png',
                    '_images/Florida Glen (12).png',
                    '_images/Florida Glen (13).png',
                    '_images/Florida Glen (14).png',
                    '_images/Florida Glen (15).png',
                    '_images/Florida Glen (16).png',
                    '_images/Florida Glen (17).png',
                    '_images/Florida Glen (18).png',
                    '_images/Florida Glen (19).png',
                    '_images/Florida Glen (20).png',
                    '_images/Florida Glen (21).png',
                    '_images/Florida Glen.png'
                ],
                title: 'Florida Glen',
                location: 'Florida Glen, Roodepoort, Gauteng',
                price: 'R4 900 000',
                bedrooms: 6,
                bathrooms: 4,
                garage: 4,
                size: '650m²',
                description: "Welcome to this character-filled 4-bedroom double-story home featuring 2 self-contained cottages, a pool, patio, braai, and lapa. Upon entering through the remote driveway gate, you're greeted by spacious living areas on both floors. The lower level boasts a large tiled entrance hall and lounge, open-plan to an entertainer's dining area from a charming solid wood kitchen. Two sizable bedrooms and modern bathrooms are downstairs, while upstairs offers two more bedrooms, a study area, lounge, and guest bathroom. Enjoy the landscaped garden, pool, and lapa from the upstairs balcony. Two separate entrances lead to a double garage, carports, and additional features include character Dorma windows, an anthracite stove, exposed beam ceilings, staff quarters, and ample storage space. Situated near local amenities and schools, this property offers both convenience and comfort for the whole family."
            },
            'property7': {
                images: [
                    '_images/Blairgowrie house.png',
                    '_images/Blairgowrie (2).png',
                    '_images/Blairgowrie (3).png',
                    '_images/Blairgowrie (4).png',
                    '_images/Blairgowrie (5).png',
                    '_images/Blairgowrie (6).png',
                    '_images/Blairgowrie (7).png',
                    '_images/Blairgowrie (8).png',
                    '_images/Blairgowrie (9).png',
                    '_images/Blairgowrie (10).png',
                    '_images/Blairgowrie (11).png',
                    '_images/Blairgowrie (12).png',
                    '_images/Blairgowrie (13).png',
                    '_images/Blairgowrie (14).png',
                    '_images/Blairgowrie.png'
                ],
                title: 'Blairgowrie House',
                location: 'Blairgowrie, Randburg, Gauteng',
                price: 'R1 849 000',
                bedrooms: 3,
                bathrooms: 2,
                garage: 2,
                size: '350m²',
                description: 'Nestled on a tranquil tree-lined street in Blairgowrie, this property offers proximity to amenities and top schools, with easy access to Sandton and Rosebank. The spacious, modern kitchen and lounge boast direct garage access, granite tops, and a gas hob. Three bedrooms feature ample cupboard space, with Bedroom 1 & 2 sharing a full bathroom and the master bedroom enjoying a dressing room and private en-suite. A garden cottage with 1 bed, 1 bath, living room, kitchen, and en-suite bedroom provides rental income or space for extended family. The private garden hosts a swimming pool and patio, while leased solar panels offer energy efficiency, with the option to transfer or purchase the lease.'
            },
            'property8': {
                images: [
                    '_images/house krugersdorp.jpg',
                    'path_to_image1_3.jpg'
                ],
                title: 'Rangeview House',
                location: 'Rangeview, Krugersdorp, Gauteng',
                price: 'R2 995 000',
                bedrooms: 5,
                bathrooms: 4,
                garage: 2,
                size: '473m²',
                description: "Welcome to a remarkable modern family home in the prestigious RangeView neighborhood, where contemporary allure meets family comfort. Upon entry, you'll feel the exceptional character of this home, with an expansive family room seamlessly transitioning from the entrance hall. Adjacent is a spacious sunroom with a fireplace, offering tranquil views of the pool and the stunning RangeView valley. The large kitchen is a culinary masterpiece, featuring elegant CeaserStone worktops and a DeLonghi Stove, complemented by a separate laundry area for practical luxury living. Upstairs, a family room opens onto a sprawling balcony with breathtaking views, accompanied by an inspiring office with its balcony. Three generous bedrooms and two full bathrooms, including an air-conditioned main bedroom with a walk-in closet and en-suite bathroom, ensure comfort and sophistication. Outside, a double garage with direct entrance and a separate entrance to the front yard provide convenience and privacy. The large sparkling pool and ample lounging space offer perfect outdoor entertainment, with staff quarters for added convenience. Welcome to your dream family home in RangeView, blending modern elegance with timeless charm, both indoors and out."
            },
            'property9': {
                images: [
                    '_images/house helderkruin.jpg',
                    'path_to_image1_3.jpg'
                ],
                title: 'Helderkruin View House',
                location: 'Helderkruin View, Roodepoort, Gauteng',
                price: 'R4 900 000',
                bedrooms: 4,
                bathrooms: 3,
                garage: 2,
                size: '450m²',
                description: ""
            },
            'property10': {
                images: [
                    '_images/sandton (17).png',
                    '_images/sandton.png',
                    '_images/sandton (2).png',
                    '_images/sandton (3).png',
                    '_images/sandton (4).png',
                    '_images/sandton (5).png',
                    '_images/sandton (6).png',
                    '_images/sandton (7).png',
                    '_images/sandton (8).png',
                    '_images/sandton (9).png',
                    '_images/sandton (10).png',
                    '_images/sandton (11).png',
                    '_images/sandton (12).png',
                    '_images/sandton (13).png',
                    '_images/sandton (14).png',
                    '_images/sandton (15).png',
                    '_images/sandton (16).png'
                ],
                title: 'Sandton House',
                location: 'Sandton Central, Sandton, Gauteng',
                price: 'R4 900 000',
                bedrooms: 4,
                bathrooms: 3,
                garage: 2,
                size: '550m²',
                description: "Welcome to this grand family home featuring double-volume spaces, abundant glass, and light, set on an extraordinary double-sized stand. The entrance hall seamlessly flows into a lounge with a gas fireplace, leading to ultra-modern open living areas with glass stacking doors opening onto a manicured garden and a sparkling heated pool. The dining room, with seating for 10-12, connects to a family or TV room and an enclosed entertainers' patio with a braai. The contemporary kitchen, with a Smeg oven and gas hob, boasts a large separate scullery/laundry and walk-in pantry. Upstairs, four en-suite bedrooms, including the main bedroom with double-volume ceilings, His and Hers walk-in cupboards, and a balcony overlooking the garden and pool, offer luxurious living. Additional features include a pyjama lounge, four garages, screed flooring, air conditioning, underfloor heating, staff suite, and superior finishes. Located in a secure cluster estate within a 5-minute drive to Sandton CBD, this home offers both luxury and convenience. Viewing by appointment only, adhering to Covid protocols."
            },
            'property11': {
                images: [
                    '_images/house  rosebank.jpg',
                    '_images/rosebank1.jpeg',
                    '_images/rosebank2.png',
                    '_images/rosebank3.png',
                    '_images/rosebank4.png',
                    '_images/rosebank5.png',
                    '_images/rosebank6.png'
                ],
                title: 'Rosebank Apartment',
                location: 'Rosebank, Johannesburg , Gauteng',
                price: 'R1 595 000',
                bedrooms: 1,
                bathrooms: 1,
                garage: 1,
                size: '250m²',
                description: "Introducing a contemporary 1-bedroom apartment located in the heart of Rosebank, perfect for corporate letting. This first-floor apartment exudes modern elegance with its indescribable ambiance and specially built-in storage cabinets. Upon entry, a stunning wine rack display sets a stylish tone. The open-plan dining and living area, intertwined with the kitchen, create a perfect space for professionals and gatherings with loved ones. Luxury quality vinyl flooring enhances the apartment's charm. A large modern sliding door leads to the Juliet balcony, allowing for a beautiful summer breeze. The bedroom features a stunning built-in wall headboard for added luxury and comfort. The Jack and Jill bathroom doubles as a guest bathroom and boasts stunning features. Extras include a portable mini inverter, fiber readiness, prepaid utilities, 24-hour security, a pool in the complex, and a lift. Centrally located, the apartment is just 2 minutes from Rosebank Mall and 6 minutes from Sandton City. Don't miss out on experiencing the sophistication and convenience this apartment offers."
            },
            'property12': {
                images: [
                    '_images/house rand park.jpg',
                    'path_to_image1_3.jpg'
                ],
                title: 'Randpark Ridge House',
                location: 'Randpark Ridge, Randburg, Gauteng',
                price: 'R2 225 000',
                bedrooms: 4,
                bathrooms: 3,
                garage: 2,
                size: '450m²',
                description: 'A beautiful house in Randpark Ridge with 4 bedrooms, 3 bathrooms, and a large garden.'
            },
            'property13': {
                images: [
                    '_images/house green side.jpg',
                    'path_to_image1_3.jpg'
                ],
                title: 'Greenside House',
                location: 'Greenside, Johannesburg, Gauteng',
                price: 'R3 395 000',
                bedrooms: 3,
                bathrooms: 3,
                garage: 2,
                size: '370m²',
                description: "Step into the haven your family deserves with this 3-bedroom, 3-bathroom home boasting incredible features. Upon entering the wet room and into the entrance hall, you'll be enchanted by the cozy ambiance of the lounge, complete with a charming fireplace for chilly evenings. The kitchen features sleek composite countertops and a five-plate gas stove and oven, while flowing parquet floors add timeless elegance to every room. Outside, discover a lush vegetable garden and fruit trees, alongside a sunny patio with a built-in braai. Additional amenities include a double automated garage, staff quarters, outdoor laundry area, and a Zozo hut with electricity for hobbies or storage. This peaceful family haven offers balance, convenience, and tranquility. Contact us today to schedule a viewing and make it yours! Nestled in the heart of Johannesburg, Greenside offers a vibrant lifestyle with excellent schools, diverse dining options, and easy access to key areas like Rosebank."
            },
            // Add more property details as needed
        };

        // Get the property ID from the URL parameter
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const propertyId = urlParams.get('id');

        // Function to load property details
        function loadPropertyDetails(propertyId) {
            const property = properties[propertyId];
            if (property) {
                // Load images into the gallery
                const gallery = document.getElementById('property-gallery');
                gallery.innerHTML = '';
                property.images.forEach(imageSrc => {
                    const img = document.createElement('img');
                    img.src = imageSrc;
                    img.alt = property.title;
                    img.onclick = () => openLightbox(imageSrc);
                    gallery.appendChild(img);
                });

                // Load property details
                document.getElementById('property-title').textContent = property.title;
                document.getElementById('property-location').textContent = 'Location: ' + property.location;
                document.getElementById('property-price').textContent = 'Price: ' + property.price;
                document.getElementById('property-bedrooms').textContent = property.bedrooms;
                document.getElementById('property-bathrooms').textContent = property.bathrooms;
                document.getElementById('property-garage').textContent = property.garage;
                document.getElementById('property-size').textContent = property.size;
                document.getElementById('property-description').textContent = 'Description: ' + property.description;
            }
        }

        // Lightbox functions
        function openLightbox(imageSrc) {
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            lightboxImg.src = imageSrc;
            lightbox.style.display = 'flex';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.style.display = 'none';
        }

        function navigateLightbox(direction) {
            const currentSrc = document.getElementById('lightbox-img').src;
            const currentIndex = properties[propertyId].images.indexOf(currentSrc);
            let newIndex = currentIndex + direction;

            if (newIndex < 0) {
                newIndex = properties[propertyId].images.length - 1;
            } else if (newIndex >= properties[propertyId].images.length) {
                newIndex = 0;
            }

            document.getElementById('lightbox-img').src = properties[propertyId].images[newIndex];
        }

        // Close the lightbox when clicking outside the image or on the close button
        document.getElementById('lightbox').addEventListener('click', (e) => {
            if (e.target === e.currentTarget || e.target.classList.contains('lightbox-close')) {
                closeLightbox();
            }
        });

        // Load property details when the page loads
        window.onload = () => loadPropertyDetails(propertyId);
        
        function goBack() {
        if (document.referrer !== '') {
            // If there is a referrer (previous page), go back
            window.history.back();
        } else {
            // If there's no referrer, redirect to a fallback page (e.g., home page)
            window.location.href = 'index.php';
        }
    }


    </script>
</body>

</html>
