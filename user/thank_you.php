<?php
require_once '../includes/config.php';
session_start();

// Check if user is logged in and order information exists
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true ) {
    header('Location: ../user/login.php');
    exit;
}

$orderDetails = $_SESSION['order_details'];
$price = $_SESSION['order_price'];
$category = $_SESSION['order_category'];
$propertyCondition = $_SESSION['order_condition'];
$date = $_SESSION['order_date'];
$paymentMethod = $_SESSION['order_payment'];
$address = $_SESSION['order_address'];

// Get client ID from user session
$clientID = $_SESSION['client_id'];

// Update the database with order information
try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Parse address from session
    $addressParts = explode(', ', $address);
    $city = $addressParts[0];
    $municipality = $addressParts[1];
    $buildingAddress = isset($addressParts[2]) ? $addressParts[2] : '';
    
    // Insert order based on service category
    if ($category == 'private') {
        // Extract number of rooms from session details
        $numberOfRooms = intval(explode(' ', $orderDetails)[0]);
        
        $stmt = $pdo->prepare("INSERT INTO Orders (ClientID, ServiceCategory, NumberOfRooms, PropertyCondition, PaymentMethod, CleaningDate, City, Municipality, BuildingAddress) VALUES (:clientID, :category, :rooms, :condition, :paymentMethod, :cleaningDate, :city, :municipality, :buildingAddress)");
        
        $stmt->bindParam(':clientID', $clientID);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':rooms', $numberOfRooms);
        $stmt->bindParam(':condition', $propertyCondition);
        $stmt->bindParam(':paymentMethod', $paymentMethod);
        $stmt->bindParam(':cleaningDate', $date);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':municipality', $municipality);
        $stmt->bindParam(':buildingAddress', $buildingAddress);
    } else {
        // Extract area size and floors from session details
        preg_match('/(\d+) m² area, (\d+) floors/', $orderDetails, $matches);
        $areaSize = isset($matches[1]) ? intval($matches[1]) : 0;
        $numberOfFloors = isset($matches[2]) ? intval($matches[2]) : 0;
        
        $stmt = $pdo->prepare("INSERT INTO Orders (ClientID, ServiceCategory, AreaSize, NumberOfFloors, PropertyCondition, PaymentMethod, CleaningDate, City, Municipality, BuildingAddress) VALUES (:clientID, :category, :area, :floors, :condition, :paymentMethod, :cleaningDate, :city, :municipality, :buildingAddress)");
       
        $stmt->bindParam(':clientID', $clientID);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':area', $areaSize);
        $stmt->bindParam(':floors', $numberOfFloors);
        $stmt->bindParam(':condition', $propertyCondition);
        $stmt->bindParam(':paymentMethod', $paymentMethod);
        $stmt->bindParam(':cleaningDate', $date);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':municipality', $municipality);
        $stmt->bindParam(':buildingAddress', $buildingAddress);
    }
    
    // Execute the statement
    $stmt->execute();
    $orderID = $pdo->lastInsertId();

    // Create payment record
    $paymentStatus =  'pending';
    $paymentDate = ($paymentMethod == 'online') ? date('Y-m-d H:i:s') : null;
    
    $paymentStmt = $pdo->prepare("INSERT INTO Payments (OrderID, PaymentMethod, PaymentStatus, Amount, PaymentDate) 
                                  VALUES (:orderID, :paymentMethod, :paymentStatus, :amount, :paymentDate)");
    
    $paymentStmt->bindParam(':orderID', $orderID);
    $paymentStmt->bindParam(':paymentMethod', $paymentMethod);
    $paymentStmt->bindParam(':paymentStatus', $paymentStatus);
    $paymentStmt->bindParam(':amount', $price);
    $paymentStmt->bindParam(':paymentDate', $paymentDate);
    $paymentStmt->execute();
    
    $paymentID = $pdo->lastInsertId();

    // Commit the transaction
    $pdo->commit();
    
    // If there's an error with the database operation, it will be caught by the catch block
    $dbUpdateSuccess = true;
    
} catch (PDOException $e) {
    // Rollback the transaction if an error occurs
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    // Set error flag and message
    $dbUpdateSuccess = false;
    $errorMessage = "Could not save order to database: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Thank You - CleanAura</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/styleS3.css">
    <link rel="stylesheet" href="../css/styleS2.css">
    <link rel="icon" type="image/png" href="../images/image (4).png">
    <script src="https://kit.fontawesome.com/32c6516436.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary-color: #15c455;
            --primary-dark: #15c455;
            --text-color: #333;
            --background-color: rgba(255, 255, 255, 0.95);
            --white: #ffffff;
            --gray-light: #f1f1f1;
            --border-radius: 12px;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        .thank-you-container {
            max-width: 800px;
            margin: 3rem auto;
            padding: 3rem;
            background-color: var(--background-color);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            text-align: center;
        }
        
        .success-icon {
            font-size: 5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .error-icon {
            font-size: 5rem;
            color: #ff4444;
            margin-bottom: 1.5rem;
        }
        
        .thank-you-heading {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .order-confirmation {
            margin: 2rem 0;
            padding: 1.5rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            text-align: left;
        }
        
        .confirmation-detail {
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dotted #eee;
        }
        
        .confirmation-detail strong {
            display: inline-block;
            width: 120px;
            color: #555;
        }
        
        .buttons {
            margin-top: 2rem;
        }
        
        .action-button {
            display: inline-block;
            padding: 0.75rem 2rem;
            margin: 0 0.5rem;
            border-radius: 2rem;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .primary-button {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
        }
        
        .secondary-button {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .error-message {
            color: #ff4444;
            background-color: #ffeeee;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }
        .instructions {
            background: #fff;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            margin-bottom: 2rem;
        }
        
        .instructions h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 1rem;
        }
        
        .instructions ol {
            padding-left: 1.5rem;
        }
        
        .instructions li {
            margin-bottom: 0.75rem;
        }
        .bank-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
            margin-bottom: 2rem;
        }
        
        .bank-info h3 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 1rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-weight: bold;
        }
        
    </style>
</head>
<body>

<div class="thank-you-container">
    <?php if (isset($dbUpdateSuccess) && $dbUpdateSuccess === false): ?>
        <div class="error-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h1 class="thank-you-heading" style="color: #ff4444;">Order Processing Issue</h1>
        <div class="error-message">
            <?php echo $errorMessage; ?>
        </div>
        <p>Your order details have been received, but there was an issue saving them to our database. 
           Please contact customer service with your order details.</p>
    <?php else: ?>
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="thank-you-heading">Thank You for Your Order!</h1>
        <p>Your cleaning service has been successfully scheduled. We appreciate your business!</p>
    <?php endif; ?>
    
    <div class="order-confirmation">
        <h3>Order Confirmation #<?php echo isset($orderID) ? $orderID : 'N/A'; ?></h3>
        
        <div class="confirmation-detail">
            <strong>Service Type:</strong> <?php echo ucfirst($category); ?> Cleaning
        </div>
        
        <div class="confirmation-detail">
            <strong>Details:</strong> <?php echo $orderDetails; ?>
        </div>
        
        <div class="confirmation-detail">
            <strong>Date:</strong> <?php echo date('F j, Y', strtotime($date)); ?>
        </div>
        
        <div class="confirmation-detail">
            <strong>Address:</strong> <?php echo $address; ?>
        </div>
        
        <div class="confirmation-detail">
            <strong>Amount:</strong> DZD<?php echo number_format($price, 2); ?>
        </div>
        
        <div class="confirmation-detail">
            <strong>Payment Method:</strong> <?php echo ucfirst($paymentMethod); ?>
        </div>
        
        <div class="confirmation-detail">
            <strong>Payment Status:</strong> <?php echo isset($paymentStatus) ? ucfirst($paymentStatus) : ($paymentMethod == 'online' ? 'Completed' : 'Pending'); ?>
        </div>
        
        <div class="confirmation-detail">
            <strong>Order Status:</strong> Pending
        </div>
    </div>
    
    <?php if ($paymentMethod == 'online'): ?>
            <div class="bank-info">
                <h3><i class="fas fa-university"></i> Banking Information</h3>
                <p>Please use the information below to complete your payment:</p>
                <div class="info-item">
                    <span class="info-label">Bank Name:</span>
                    <span class="info-value">Algérie Poste</span>
                </div>
                <div class="info-item">
                    <span class="info-label">app used</span>
                    <span class="info-value">Baridi mob</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Account rip:</span>
                    <span class="info-value">0777799999040896</span>
                </div>
                
                
            </div>
            
            <div class="instructions">
                <h3><i class="fas fa-info-circle"></i> Important Instructions</h3>
                <p>Please follow these steps to complete your booking:</p>
                <ol>
                    <li>Make your payment using the banking details provided above.</li>
                    <li>Include your <span class="highlight">Order ID: <?php echo $orderID; ?></span> in the payment reference.</li>
                    <li>After completing the payment, please send the payment receipt to <span class="highlight">payments@cleanaura.com</span> with your Order ID in the subject line.</li>
                    <li>Once we receive and verify your payment, we'll send a confirmation email with more details about your scheduled cleaning service.</li>
                </ol>
                <p><strong>Note:</strong> Your booking is not confirmed until payment is received and verified. Please complete your payment within 24 hours to secure your booking date.</p>
            </div>
            <?php endif; ?>
    
    <div class="buttons">
        <a href="../user/profile.php" class="action-button primary-button">View My Orders</a>
        <a href="../user/index.php" class="action-button secondary-button">Back to Home</a>
    </div>
</div>

<?php
// Clear session variables related to the order
unset($_SESSION['order_details']);
unset($_SESSION['order_price']);
unset($_SESSION['order_category']);
unset($_SESSION['order_condition']);
unset($_SESSION['order_date']);
unset($_SESSION['order_payment']);
unset($_SESSION['order_address']);
?>

</body>
</html>6