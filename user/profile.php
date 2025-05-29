<?php
session_start();
require '../includes/config.php'; // Ensure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    header('Location: ../user/login.php'); // Redirect to login page if not logged in
    exit;
}

// Process rating submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $orderId = $_POST['order_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    try {
        $sql = "UPDATE Orders SET Rating = :rating, Comments = :comment WHERE ID = :orderId AND ClientID = :clientId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':clientId', $_SESSION['client_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        // Set a success message
        $_SESSION['rating_success'] = "Thank you! Your rating has been submitted.";
        
        // Redirect to avoid form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        $_SESSION['rating_error'] = "Error submitting rating: " . $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Process image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_image'])) {
    $orderId = $_POST['order_id'];
    
    // Check if file was uploaded without errors
    if (isset($_FILES["order_image"]) && $_FILES["order_image"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["order_image"]["name"];
        $filetype = $_FILES["order_image"]["type"];
        $filesize = $_FILES["order_image"]["size"];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $_SESSION['image_error'] = "Error: Please select a valid file format (JPG, JPEG, PNG, GIF).";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        
        // Verify file size - 5MB maximum
        $maxsize = 10 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $_SESSION['image_error'] = "Error: File size is larger than the allowed limit (5MB).";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        
        // Verify MIME type of the file
        if (in_array($filetype, $allowed)) {
            // Create unique filename
            $new_filename = uniqid() . "." . $ext;
            $upload_path = "../uploads/orders/" . $new_filename;
            
            // Check if directory exists, if not create it
            if (!file_exists("../uploads/orders/")) {
                mkdir("../uploads/orders/", 0777, true);
            }
            
            // Upload the file
            if (move_uploaded_file($_FILES["order_image"]["tmp_name"], $upload_path)) {
                // Insert image details into the database
                try {
                    $sql = "INSERT INTO OrderImages (OrderID, ImageURL) VALUES (:orderId, :imageUrl)";
                    $stmt = $pdo->prepare($sql);
                    $imageUrl = "uploads/orders/" . $new_filename; // Store relative path
                    $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                    $stmt->bindParam(':imageUrl', $imageUrl, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    $_SESSION['image_success'] = "Image uploaded successfully!";
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } catch (PDOException $e) {
                    $_SESSION['image_error'] = "Database error: " . $e->getMessage();
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
            } else {
                $_SESSION['image_error'] = "Error: There was a problem uploading your file. Please try again.";
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        } else {
            $_SESSION['image_error'] = "Error: There was a problem with the file type. Please try again.";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $_SESSION['image_error'] = "Error: " . $_FILES["order_image"]["error"];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch client info using the session client ID
$clientId = $_SESSION['client_id']; // Get the client ID from the session

try {
    $sql = "SELECT FirstName ,LastName, Email, PhoneNumber FROM Clients WHERE ID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $clientId, PDO::PARAM_INT);
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        $FirstName = $client['FirstName'];
        $LastName = $client['LastName'];
        $email = $client['Email'];
        $phoneNumber = $client['PhoneNumber'];
       
    } else {
        echo "Client not found.";
        exit;
    }
} catch (PDOException $e) {
    die("Error fetching client information: " . $e->getMessage());
}

// Fetch orders for the client
try {
    $sqlOrders = "SELECT * FROM Orders WHERE ClientID = :clientId ORDER BY OrderDate DESC";
    $stmtOrders = $pdo->prepare($sqlOrders);
    $stmtOrders->bindParam(':clientId', $clientId, PDO::PARAM_INT);
    $stmtOrders->execute();
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch images for each order
    foreach ($orders as $key => $order) {
        $sqlImages = "SELECT * FROM OrderImages WHERE OrderID = :orderId";
        $stmtImages = $pdo->prepare($sqlImages);
        $stmtImages->bindParam(':orderId', $order['ID'], PDO::PARAM_INT);
        $stmtImages->execute();
        $orders[$key]['images'] = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanAura - My Profile</title>
    <link rel="stylesheet" href="../css/profilcss.css">
    <link rel="stylesheet" href="../css/cssuser.css">
    <link rel="icon" type="image/png" href="../images/image (4).png">
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background-color: #9eff8d;
            background: linear-gradient(to right, #a8e6cf, #3cb371); /* Lighter green to a darker green */
            margin: 0;
            padding: 0;
            height: 100%;
            display: block;
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
            backdrop-filter: blur(10px);
            border-radius: 30px;
            background: linear-gradient(to bottom, #3cb371,rgba(168, 230, 207, 0.04));
            height: 80px;
        }
        .menu ul li a {
            color: white;
            text-decoration: none;
            text-transform: capitalize;
            font-weight: 500;
            font-size: 1.1rem; /* Increased font size */
            line-height: 1.5; /* Improved line height for clarity */
            transition: color 0.3s; /* Smooth transition for hover effects */
        }
        .menu ul li a:hover {
            color: #a8e6cf; /* Change color on hover for better visibility */
        }
        .profile-container {
            max-width: 1200px;
            margin: 130px auto 30px; /* Added top margin to prevent content hiding under navbar */
            padding: 20px;
        }
        .profile-header {
            background-color: #fff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #25D366;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 40px;
            margin-right: 30px;
        }
        .profile-info h2 {
            margin-bottom: 10px;
            color: #333;
        }
        .profile-info p {
            color: #666;
            margin: 5px 0;
            display: flex;
            align-items: center;
        }
        .profile-info p i {
            margin-right: 10px;
            color: #25D366;
            width: 20px;
        }
        .profile-tabs {
            display: flex;
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .profile-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        .profile-tab.active {
            border-bottom: 3px solid #25D366;
            font-weight: 600;
        }
        .profile-tab:hover {
            background-color: #f9f9f9;
        }
        .orders-container {
            background-color: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .order-card {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .order-number {
            font-weight: 600;
            color: #333;
        }
        .order-date {
            color: #888;
        }
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-scheduled {
            background-color: #cce5ff;
            color: #004085;
        }
        .order-details {
            margin-bottom: 15px;
        }
        .order-service {
            font-weight: 500;
            margin-bottom: 10px;
            color: #333;
        }
        .order-address, .order-time {
            color: #666;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .order-price {
            font-weight: 600;
            color: #25D366;
            font-size: 18px;
            margin-top: 10px;
        }
        .order-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }
        .btn {
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #25D366;
            color: white;
        }
        .btn-outline {
            background-color: transparent;
            border: 1px solid #25D366;
            color: #25D366;
        }
        .btn:hover {
            opacity: 0.85;
        }
        .empty-orders {
            text-align: center;
            padding: 40px 0;
            color: #888;
        }
        .empty-orders i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        /* Rating modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            width: 80%;
            max-width: 500px;
            position: relative;
            animation: modalFadeIn 0.3s;
        }
        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .close-modal {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }
        .close-modal:hover {
            color: #333;
        }
        .rating-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            direction: rtl;
        }
        .star-rating input {
            display: none;
        }
        .star-rating label {
            font-size: 30px;
            color: #ddd;
            cursor: pointer;
            padding: 0 5px;
            transition: all 0.2s ease;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffb700;
        }
        .comment-area {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }
        .submit-rating {
            width: 100%;
            background-color: #25D366;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .submit-rating:hover {
            background-color: #1ea855;
        }
        .rating-display {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        .rating-stars {
            color: #ffb700;
            margin-right: 10px;
        }
        .rating-text {
            color: #666;
            font-style: italic;
        }
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Image gallery styles */
        .order-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .order-image-item {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
        }
        .order-image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .order-image-item:hover img {
            transform: scale(1.05);
        }
        .image-gallery-title {
            font-weight: 500;
            margin-top: 20px;
            margin-bottom: 10px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .file-input-container {
            position: relative;
            margin-bottom: 20px;
        }
        .file-input-container input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
        }
        .file-input-container:hover input[type="file"] {
            border-color: #25D366;
        }
        
        /* Image modal */
        #imageModal {
            display: none;
            position: fixed;
            z-index: 1060;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .modal-image-container {
            margin: 5% auto;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }
        .modal-image {
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
        }
        .close-image-modal {
            position: absolute;
            right: 25px;
            top: 15px;
            font-size: 35px;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            z-index: 1070;
        }
        
        /* Footer styles */
        footer {
            background: linear-gradient(to top, #3cb371, rgba(168, 230, 207, 0.4));
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .footer-logo {
            margin-bottom: 20px;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }
        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: #a8e6cf;
        }
        .footer-social {
            margin-bottom: 20px;
        }
        .footer-social a {
            color: white;
            font-size: 24px;
            margin: 0 10px;
            transition: color 0.3s;
        }
        .footer-social a:hover {
            color: #a8e6cf;
        }
        .footer-copyright {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            .profile-avatar {
                margin: 0 0 20px 0;
            }
            .order-header {
                flex-direction: column;
                gap: 10px;
            }
            .order-actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
            .footer-links {
                flex-direction: column;
                gap: 15px;
            }
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
            .order-images {
                justify-content: center;
            }
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
            top: 8px; /* Adjust this value as needed */
        }
    </style>
</head>
<body>
    <section class="home">
        <div class="home-box">
            <nav>
                <div class="logo bars">
                    <div class="bar"></div>
                    <a href="../user/index.php">
                    <img src="../images/logo c.png" alt="CleanAura Logo" class="logo-img">
                </a>
                </div>
                <div class="menu">
                    <ul>
                        <li><a href="../user/index.php">Home</a></li>
                        <li><a href="../user/index.php#aboutUs">About</a></li>
                        <li><a href="../user/simulation.php">Simulation</a></li>
                        <li><a href="../user/contact.php" target="_blank">Contact</a></li>
                        <li><a href="../user/index.php#FAQ">FAQ</a></li>
                    </ul>
                </div>
                <div class="logout-btn">
                    <a href="../user/logout.php" title="Log Out">
                        <img src="../images/icons8-log-out-64.png" alt="Log Out" style="width: 50px; height: 50px;">
                    </a>
                </div>
            </nav>
        </div>
    </section>

    <div class="profile-container">
        <?php if (isset($_SESSION['rating_success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['rating_success']; 
                    unset($_SESSION['rating_success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['rating_error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['rating_error']; 
                    unset($_SESSION['rating_error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['image_success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['image_success']; 
                    unset($_SESSION['image_success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['image_error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['image_error']; 
                    unset($_SESSION['image_error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            
                <?php 
            echo '<div class="profile-avatar">' . substr($FirstName, 0, 1) . '</div>';
            ?>
            <div class="profile-info">
            <h2><?php echo htmlspecialchars($FirstName . ' ' . $LastName); ?></h2>

                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($email); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($phoneNumber); ?></p>
            </div>
        </div>

        <div class="profile-tabs">
            <div class="profile-tab active">My Orders</div>
        </div>

        <div class="orders-container">
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-number">Order #<?php echo htmlspecialchars($order['ID']); ?></div>
                            <div class="order-date">
                                <i class="far fa-calendar-alt"></i> 
                                <?php echo date('F d, Y', strtotime($order['CleaningDate'])); ?>
                            </div>
                            <?php if (isset($order['Status'])): ?>
                                <div class="order-status status-<?php echo strtolower($order['Status']); ?>">
                                    <?php echo htmlspecialchars($order['Status']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="order-details">
                            <div class="order-service">
                                <i class="fas fa-broom"></i> 
                                Service: <?php echo htmlspecialchars($order['ServiceCategory']); ?>
                            </div>
                            <div class="order-address">
                                <i class="fas fa-map-marker-alt"></i> 
                                Location: <?php echo htmlspecialchars($order['City'] . ', ' . $order['Municipality'] . ', ' . $order['BuildingAddress']); ?>

                            </div>
                            <?php if (isset($order['ServiceTime'])): ?>
                                <div class="order-time">
                                    <i class="far fa-clock"></i> 
                                    Time: <?php echo htmlspecialchars($order['ServiceTime']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="order-price">
                                <i class="fas fa-tag"></i> 
                                <?php
                                // Display Price if available, otherwise show PaymentMethod
                                if (isset($order['Price'])) {
                                    echo 'Price: $' . htmlspecialchars($order['Price']);
                                } else {
                                    echo 'Payment: ' . htmlspecialchars($order['PaymentMethod']);
                                }
                                ?>
                            </div>
                            
                            <?php if (!empty($order['images'])): ?>
                                <div class="image-gallery-title">
                                    <i class="fas fa-images"></i> Service Images
                                </div>
                                <div class="order-images">
                                    <?php foreach ($order['images'] as $image): ?>
                                        <div class="order-image-item" onclick="openImageModal('../<?php echo $image['ImageURL']; ?>')">
                                            <img src="../<?php echo $image['ImageURL']; ?>" alt="Order Image">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($order['Rating']): ?>
                                <div class="rating-display">
                                    <div class="rating-stars">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <?php if($i <= $order['Rating']): ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <?php if (!empty($order['Comments'])): ?>
                                        <div class="rating-text">
                                            "<?php echo htmlspecialchars($order['Comments']); ?>"
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="order-actions">
    <?php if ($order['Status'] === 'completed'): ?>
        <?php if (!$order['Rating']): ?>
            <button class="btn btn-primary rate-order-btn" 
                    data-order-id="<?php echo $order['ID']; ?>">
                <i class="fas fa-star"></i> Rate Order
            </button>
        <?php endif; ?>
        <button class="btn btn-outline upload-image-btn" 
                data-order-id="<?php echo $order['ID']; ?>">
            <i class="fas fa-camera"></i> Add Image
        </button>
    <?php else: ?>
        <p class="order-status-notice">
    <?php echo "Don't miss to rate our service, Mr. " . htmlspecialchars($FirstName) . "."; ?>
</p>

    <?php endif; ?>
</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-orders">
                    <i class="fas fa-shopping-cart"></i>
                    <p>No orders found. Book a cleaning service to see your orders here!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rating Modal -->
    <div id="ratingModal" class="modal">
    <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 class="rating-title">Rate Your Order</h3>
            <form id="ratingForm" method="POST" action="">
                <input type="hidden" name="order_id" id="modal-order-id">
                
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required>
                    <label for="star5" class="fas fa-star"></label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4" class="fas fa-star"></label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3" class="fas fa-star"></label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2" class="fas fa-star"></label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1" class="fas fa-star"></label>
                </div>
                
                <textarea name="comment" class="comment-area" placeholder="Share your experience with this service (optional)"></textarea>
                
                <button type="submit" name="submit_rating" class="submit-rating">Submit Rating</button>
            </form>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div id="imageUploadModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3 class="rating-title">Upload Service Image</h3>
            <form id="imageUploadForm" method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="order_id" id="image-modal-order-id">
                
                <div class="file-input-container">
                    <input type="file" name="order_image" id="order_image" accept="image/*" required>
                </div>
                
                <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                    <i class="fas fa-info-circle"></i> Accepted formats: JPG, JPEG, PNG, GIF. Max size: 10MB.
                    <br>
                    <i class="fas fa-info-circle"></i> If you have a problem or complaint, please upload an image
                </p>
                
                <button type="submit" name="submit_image" class="submit-rating">Upload Image</button>
            </form>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div id="imageModal" class="modal">
        <span class="close-image-modal">&times;</span>
        <div class="modal-image-container">
            <img class="modal-image" id="fullSizeImage" src="" alt="Full size image">
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <img src="../images/logo c.png" alt="CleanAura Logo" style="width: 180px;">
            </div>
            <div class="footer-links">
                <a href="../user/index.php">Home</a>
                <a href="../user/index.php#aboutUs">About Us</a>
                <a href="../user/simulation.php">Services</a>
                <a href="../user/contact.php">Contact</a>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> CleanAura. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Simple tab switching functionality
        document.querySelectorAll('.profile-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.profile-tab').forEach(t => {
                    t.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        // Rating modal functionality
        const ratingModal = document.getElementById('ratingModal');
        const rateButtons = document.querySelectorAll('.rate-order-btn');
        const closeRatingModal = ratingModal.querySelector('.close-modal');
        const orderIdInput = document.getElementById('modal-order-id');
        
        // Open rating modal and set order ID
        rateButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                orderIdInput.value = orderId;
                ratingModal.style.display = 'block';
            });
        });
        
        // Close rating modal when clicking X
        closeRatingModal.addEventListener('click', function() {
            ratingModal.style.display = 'none';
        });
        
        // Image upload modal functionality
        const imageUploadModal = document.getElementById('imageUploadModal');
        const uploadButtons = document.querySelectorAll('.upload-image-btn');
        const closeImageUploadModal = imageUploadModal.querySelector('.close-modal');
        const imageOrderIdInput = document.getElementById('image-modal-order-id');
        
        // Open image upload modal and set order ID
        uploadButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                imageOrderIdInput.value = orderId;
                imageUploadModal.style.display = 'block';
            });
        });
        
        // Close image upload modal when clicking X
        closeImageUploadModal.addEventListener('click', function() {
            imageUploadModal.style.display = 'none';
        });
        
        // Image viewer modal functionality
        const imageModal = document.getElementById('imageModal');
        const fullSizeImage = document.getElementById('fullSizeImage');
        const closeImageViewerModal = document.querySelector('.close-image-modal');
        
        // Open image viewer modal
        function openImageModal(src) {
            fullSizeImage.src = src;
            imageModal.style.display = 'block';
        }
        
        // Close image viewer modal when clicking X
        closeImageViewerModal.addEventListener('click', function() {
            imageModal.style.display = 'none';
        });
        
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target == ratingModal) {
                ratingModal.style.display = 'none';
            }
            if (event.target == imageUploadModal) {
                imageUploadModal.style.display = 'none';
            }
            if (event.target == imageModal) {
                imageModal.style.display = 'none';
            }
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>