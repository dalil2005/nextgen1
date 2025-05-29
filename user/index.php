<?php
session_start(); // Start the session
include '../admin/count.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cleanora</title>
    <link rel="stylesheet" href="../css/cssuser.css">
    <script src="https://kit.fontawesome.com/32c6516436.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/png" href="../images/image (4).png">
</head>
<style>
    .rating-container {
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 600px;
        margin: 20px auto;
        text-align: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        position: relative;
        overflow: hidden;
        border: 1px solid #e0e0e0;

    }

    .rating-container:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, rgb(5, 123, 35), rgb(85, 201, 107));
    }

    .rating-title {
        color: #333;
        margin-bottom: 20px;
        font-size: 22px;
        font-weight: 600;
    }

    .rating-star-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 15px;
        display: block;
        filter: drop-shadow(0 3px 5px rgba(255, 187, 0, 0.3));
        animation: pulse 2s infinite ease-in-out;
    }

    .rating-score {
        font-size: 48px;
        font-weight: 700;
        color: #333;
        margin: 0;
        line-height: 1;
        background: linear-gradient(90deg, #f7971e, #ffd200);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .rating-score-container {
        position: relative;
        margin: 15px 0 25px;
    }

    .rating-count {
        margin-top: 20px;
        padding: 12px 15px;
        background-color: #f9f9f9;
        border-radius: 10px;
        color: #666;
        font-size: 15px;
        border: 1px dashed #ddd;
    }

    .rating-count strong {
        color: #333;
        font-weight: 600;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    @media (max-width: 480px) {
        .rating-container {
            padding: 20px 15px;
        }

        .rating-score {
            font-size: 38px;
        }
    }

    nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        height: 80px;
        border-end-end-radius: 30px;
        border-end-start-radius: 30px;
    }

    .menu ul li a {
        color: white;
        text-decoration: none;
        text-transform: capitalize;
        font-weight: 500;
        font-size: 1.1rem;
        line-height: 1.5;
        transition: color 0.3s;
    }

    .menu ul li a:hover {
        color: #a8e6cf;
    }

    .logo-img {
        width: 160px;
        height: auto;
    }

    .logo.bars {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        top: 8px;
        /* Adjust this value as needed */
    }

    /* Green-themed Order Button CSS */
    .order-button {
        display: inline-block;
        background: linear-gradient(145deg, #4CAF50, #3e8e41);
        color: white;
        text-decoration: none;
        padding: 14px 28px;
        border-radius: 50px;
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        margin: 20px 0;
        text-align: center;
    }

    .order-button:hover {
        background: linear-gradient(145deg, #5dba61, #4CAF50);
        box-shadow: 0 6px 15px rgba(76, 175, 80, 0.6);
        transform: translateY(-2px);
    }

    .order-button:active {
        transform: translateY(1px);
        box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);
    }

    .order-button i {
        margin-left: 10px;
        position: relative;
        top: 1px;
        transition: transform 0.3s ease;
    }

    .order-button:hover i {
        transform: translateX(5px);
    }

    /* Add a subtle pulse animation */


    .order-button-animated {
        animation: pulse 2s infinite;
    }

    /* CSS for the reviews section - Simplified and clean */
    .reviews-container {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        padding: 25px;
        max-width: 600px;
        margin: 20px auto 40px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        border: 1px solid #eaeaea;
    }

    .section-title {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: 600;
    }

    .review-card {
        background-color: #f9f9f9;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #4CAF50;
    }

    .review-header {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }

    .client-avatar {
        width: 40px;
        height: 40px;
        background-color: #4CAF50;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        margin-right: 12px;
    }

    .client-details h3 {
        margin: 0 0 5px 0;
        font-size: 16px;
        color: #333;
    }

    .order-id-label {
        font-size: 12px;
        color: #777;
        display: block;
    }

    .rating-stars {
        margin-top: 5px;
    }

    .star {
        color: #ddd;
    }

    .star.filled {
        color: #ffc107;
    }

    .rating-value {
        margin-left: 5px;
        font-size: 13px;
        color: #666;
    }

    .review-content {
        margin-top: 10px;
    }

    .comment-text {
        color: #555;
        line-height: 1.5;
        margin-bottom: 10px;
    }

    .order-image-gallery {
        margin-top: 15px;
    }

    .single-image-container img {
        max-width: 100%;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 8px;
    }

    .image-item img {
        width: 100%;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .no-reviews {
        text-align: center;
        padding: 30px 0;
        color: #888;
    }

    .no-reviews-icon {
        width: 64px;
        height: 64px;
        margin-bottom: 10px;
        opacity: 0.7;
    }

    /* Responsive adjustments */
    @media (max-width: 480px) {
        .review-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .client-avatar {
            margin-bottom: 8px;
        }

        .image-grid {
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
        }
    }

    .faq-section {
        background-color: #f9f9f9;
        padding: 60px 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .faq-section .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .faq-section .section-title {
        text-align: center;
        color: #333;
        font-size: 32px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .faq-section .section-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 40px;
        font-size: 16px;
    }

    .faq-container {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .faq-item {
        border-bottom: 1px solid #eaeaea;
    }

    .faq-item:last-child {
        border-bottom: none;
    }

    .faq-question {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        background-color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .faq-question:hover {
        background-color: #f5f5f5;
    }

    .faq-question h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
        font-weight: 500;
    }

    .faq-icon {
        font-size: 24px;
        color: #4CAF50;
        transition: transform 0.3s ease;
    }

    .faq-item.active .faq-icon {
        transform: rotate(45deg);
    }

    .faq-answer {
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease;
        background-color: #f9f9f9;
    }

    .faq-item.active .faq-answer {
        padding: 20px 25px;
        max-height: 1000px;
    }

    .faq-answer p {
        margin: 0;
        line-height: 1.6;
        color: #555;
    }

    @media (max-width: 768px) {
        .faq-question h3 {
            font-size: 16px;
        }

        .faq-answer p {
            font-size: 14px;
        }
    }
</style>
</style>

<body>
    <section class="home">
        <div class="home-box">
            <nav>
                <div class="logo bars">
                    <div class="bar"></div>
                    <img src="../images/logo c.png"  alt="CleanAura Logo" class="logo-img">
                </div>

                <div class="menu">
                    <ul>
                        <li><a href="#">home</a></li>
                        <li><a href="#aboutUs">about</a></li>
                        <li><a href="../user/simulation.php" target="_blank">Simulation</a></li>
                        <li><a href="../user/contact.php" target="_blank">Contact</a></li>
                        <li><a href="#FAQ">FAQ</a></li>
                    </ul>
                </div>
                <div class="signup-login">
                    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                        <a href="../user/profile.php">My Account</a>
                    <?php else: ?>
                        <a href="../user/login1.php">Login</a>
                    <?php endif; ?>
                </div>


            </nav>
        </div>
    </section>

    <div class="slider">
        <div class="slides">
            <div class="slide">

                <div class="slide-content">
                    <h2>🏡Residential, 🏢Commercial & 🏭Industrial</h2>
                    <p>Your Trusted Cleaning Experts</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h2>Professional Cleaning Solutions</h2>
                    <p>Quality Service Guaranteed</p>
                </div>
            </div>
        </div>

        <button class="slider-button prev" onclick="changeSlide(-1)">❮</button>
        <button class="slider-button next" onclick="changeSlide(1)">❯</button>

        <div class="dots">
            <span class="dot active" onclick="goToSlide(0)"></span>
            <span class="dot" onclick="goToSlide(1)"></span>
        </div>
    </div>

    <section class="aboutUs" id="aboutUs">

        <div class="container">
            <div class="box box1">
                <img src="../images/Quality.png" alt="">
                <div class="content">
                    <h4>Quality</h4>
                    <p>Ensures that we use the best cleaning techniques, premium products, and professional equipment to achieve exceptional results. Whether it's a home, office, or industrial space, we guarantee a spotless and hygienic environment.</p>
                </div>
            </div>
            <div class="box box2">
                <img src="../images/expertise.png" alt="">
                <div class="content">
                    <h4>Experienced & Reliable Team</h4>
                    <p>Our skilled professionals have worked in homes, offices, hotels, hospitals, and more, adapting to every cleaning challenge. With attention to detail and a customer-first approach, they ensure spotless results every time.</p>
                </div>
            </div>
            <div class="box box3">
                <img src="../images/trust.png" alt="">
                <div class="content">
                    <h4>Trust</h4>
                    <p>Trust is the foundation of our business. Our clients rely on us to provide safe, reliable, and professional cleaning services. We value transparency, punctuality, and integrity, building long-term relationships based on confidence and satisfaction. Your space is in the hands of experts who care.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="services" id="services">
        <h4 class="label">Our Services</h4>
        <div class="container">
            <div class="container-box">
                <h2 class="heading">Contact us today for a customized cleaning solution!</h2>
                <div class="content">
                    <p>At CleanMe, we offer a wide range of cleaning services, including residential, commercial, office, hotel, hospital, and industrial cleaning. From deep cleaning to routine maintenance, our team ensures every space is spotless, hygienic, and refreshed.</p>
                    <a href="../user/simulation.php" class="order-button order-button-animated">
                        Place your order now! <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>
    </section>
    <div class="rating-container">
        <h3 class="rating-title">Average Rating</h3>
        <img src="../images/icons8-rate-64.png" alt="Star Icon" class="rating-star-icon">
        <div class="rating-score-container">
            <div class="rating-score">
                <?php echo htmlspecialchars($averageRating !== null ? number_format($averageRating, 1) : "N/A"); ?>
            </div>
        </div>
        <div class="rating-count">
            Total Rated Orders: <strong><?php echo htmlspecialchars($ratedOrders); ?></strong>
        </div>
    </div>
    <div class="client-reviews">
        <?php
        try {



            $stmt = $pdo->prepare("SELECT o.ID as OrderID, o.Comments, o.Rating, c.FirstName,c.LastName
              FROM Orders o 
              JOIN Clients c ON o.ClientID = c.ID
              WHERE o.Comments IS NOT NULL AND o.Comments != ''
              ORDER BY o.OrderDate DESC
              LIMIT 3");
            $stmt->execute();

            echo '<h2 class="section-title">Recent Client Reviews</h2>';

            if ($stmt->rowCount() > 0) {
                echo '<div class="reviews-container">';
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $orderID = $row['OrderID'];
                    $FirstName = htmlspecialchars($row['FirstName']);
                    $LastName = htmlspecialchars($row['LastName']);
                    $comments = htmlspecialchars($row['Comments']);
                    $rating = $row['Rating'];

                    // Get all images for this specific order
                    $imageQuery = "SELECT ImageURL FROM OrderImages WHERE OrderID = :orderID";
                    $imageStmt = $pdo->prepare($imageQuery);
                    $imageStmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
                    $imageStmt->execute();
                    $orderImages = $imageStmt->fetchAll(PDO::FETCH_COLUMN);

                    // Display the review with improved design
                    echo '<div class="review-card">';

                    echo '<div class="review-header">';
                    echo '<div class="client-avatar">' . substr($FirstName, 0, 1) . '</div>';
                    echo '<div class="client-details">';

                    echo '<h3>' . $FirstName . ' ' . $LastName . '</h3>';

                    echo '<span class="order-id-label">Order #' . $orderID . '</span>';

                    // Display star rating with improved visual
                    echo '<div class="rating-stars">';
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo '<span class="star filled">⭐</span>';
                        } else {
                            echo '<span class="star">☆</span>';
                        }
                    }
                    echo ' <span class="rating-value">' . $rating . '/5</span>';
                    echo '</div>'; // End rating
                    echo '</div>'; // End client-details
                    echo '</div>'; // End review-header

                    echo '<div class="review-content">';
                    echo '<p class="comment-text">' . $comments . '</p>';

                    // Display all order images together
                    if (!empty($orderImages)) {
                        echo '<div class="order-image-gallery">';


                        if (count($orderImages) == 1) {
                            // Single image display
                            echo '<div class="single-image-container">';
                            echo '<img src="../' . htmlspecialchars($orderImages[0]) . '" alt="Order Image">';
                            echo '</div>';
                        } else {
                            // Multiple images gallery
                            echo '<div class="image-grid">';
                            foreach ($orderImages as $imageURL) {
                                echo '<div class="image-item">';
                                echo '<img src="../' . htmlspecialchars($imageURL) . '" alt="Order Image">';
                                echo '</div>';
                            }
                            echo '</div>'; // End image-grid
                        }

                        echo '</div>'; // End order-image-gallery
                    }

                    echo '</div>'; // End review-content
                    echo '</div>'; // End review-card
                }
                echo '</div>'; // End reviews-container
            } else {
                echo '<div class="no-reviews">';
                echo '<img src="../images/icons8-no-chat-64.png" alt="No Reviews" class="no-reviews-icon">';
                echo '<p>No client reviews available yet</p>';
                echo '</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="error-message">';
            echo '<p>Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
    </div>

    <section id="contact" class="contact">
        <div class="container">
            <div class="section-title">
                <h2>Agency Locations</h2>
                <p>🗺️ Got a problem or something you want us to handle? Just swing by our agency — we’re here for you!</p>

            </div>
        </div>

        <div class="mapouter">
            <div class="gmap_canvas">
                <iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=670&amp;height=363&amp;hl=en&amp;q=University 08 Mai 1945 Guelmar&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
            </div>
        </div>

    </section>
    <!-- FAQ Section -->
    <section class="faq-section" id="FAQ">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Here are answers to the most common questions about our services</p>

            <div class="faq-container">
                <!-- Question 1 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What cleaning services do you offer?</h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>We offer two types of services: private and professional. Our private service is designed for residences and small homes, while our professional service caters to business buildings like restaurants and other commercial spaces.</p>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How can I book a cleaning service?</h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>You can easily place your order through our Simulation page on our website. It's a simple process that allows you to specify your cleaning needs and schedule your preferred date and time.</p>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do you use eco-friendly cleaning products?</h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Of course! We use high-quality, environmentally friendly cleaning products in all our services. We care about the health of our clients and staff as well as the environment, so we choose products that are effective yet safe and environmentally responsible.</p>
                    </div>
                </div>

                <!-- Question 4 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How long does the cleaning process take?</h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>The maximum time our cleaning service takes is 1 day, regardless of the space size or type of service required. We ensure efficient and thorough cleaning within this timeframe to minimize disruption to your routine.</p>
                    </div>
                </div>

                <!-- Question 5 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do I need to be present during cleaning?</h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>No, it's not necessary for you to be present during the cleaning process. You can arrange to provide keys to our team, and we'll ensure the security and confidentiality of your space. However, if you prefer to be present during the service, you are welcome to do so.</p>
                    </div>
                </div>

                <!-- Question 6 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What payment methods do you accept?</h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>You can pay with Baridi Mob and send the receipt to our email, or you can pay cash upon completion of the service. We strive to offer convenient payment options to suit your preferences.</p>
                    </div>
                </div>

                <!-- Question 7 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do you provide a guarantee for your services? </h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we offer a satisfaction guarantee on all our services. If you're not completely satisfied with the cleaning results, please let us know within 24 hours and we'll return to re-clean areas that didn't meet your expectations at no additional cost.</p>
                    </div>
                </div>

                <!-- Question 8 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do I need to provide cleaning supplies?</h3>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>No, our team comes equipped with all necessary materials and equipment for cleaning. We use high-quality professional products and specialized equipment to ensure the best results. However, if you have a preference for certain products in your home, we can use them upon your request.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <section class="footer">
            <div class="container">
                <div class="detail">
                    <h3>Cleanora</h3>
                    <p>At Cleanora, we provide top-quality cleaning services tailored to your needs. Whether it's residential, commercial, or industrial cleaning, our dedicated team ensures a spotless and hygienic environment.</p>
                    <h5>Get in Touch</h5>
                    <a href="mailto:example@mail.com">cleanora@mail.com</a>
                    <div class="social">
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                    </div>
                </div>
                <div class="about-us">
                    <h4>Help</h4>
                    <ul>
                        <li><a href="#FAQ">FAQ</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Site Map</a></li>
                        <li><a href="../user/contact.php" target="_blank">Contact as</a></li>
                    </ul>
                </div>
                <div class="about-us">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#services">Gallery</a></li>
                        <li><a href="#">Offers</a></li>
                    </ul>
                </div>
            </div>

            <div class="copyright">
                <div>
                    &copy; 2024 Cleanora, Inc. All rights reserved.
                </div>
                <div>
                    &copy; Developed by NextGen Coders.
                </div>
                <div>
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Privacy Policy</a>
                </div>
            </div>
        </section>
    </footer>

    <a href="https://wa.me/1234567890" class="whatsapp-button" target="_blank" rel="noopener noreferrer">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
        </svg>
    </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.4/ScrollTrigger.min.js"></script>
    <script src="../js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // الحصول على جميع عناصر الأسئلة
            const faqItems = document.querySelectorAll('.faq-item');

            // إضافة مستمع حدث النقر لكل سؤال
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');

                question.addEventListener('click', () => {
                    // التحقق مما إذا كان العنصر الحالي نشطًا
                    const isActive = item.classList.contains('active');

                    // إغلاق جميع العناصر النشطة الأخرى
                    faqItems.forEach(faqItem => {
                        faqItem.classList.remove('active');
                    });

                    // فتح العنصر الحالي إذا لم يكن نشطًا بالفعل
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>

</html>