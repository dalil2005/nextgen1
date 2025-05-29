<?php
require_once '../includes/config.php';
session_start();

// Check if order information exists in session
if (!isset($_SESSION['order_category']) || !isset($_SESSION['order_price'])) {
    // Redirect if no order data is found
    header('Location: simulation.php');
    exit;
}

// If user confirms order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_order'])) {
    if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true) {
        // User is logged in, proceed to thank you page
        header('Location: ../user/thank_you.php');
        exit;
    } else {
        // User is not logged in, store order information in session
        $_SESSION['pending_order'] = true;
        
        // Show login/signup options (handled via JavaScript below)
        // The modal will appear after form submission
    }
}

// If user selects login option
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_option'])) {
    // Store a flag that this is a pending order requiring login
    $_SESSION['pending_order'] = true;
    $_SESSION['redirect_after_login'] = '../user/thank_you.php';
    // Redirect to login page
    header('Location: ../user/login.php');
    exit;
}

// If user selects signup option
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup_option'])) {
    // Store a flag that this is a pending order requiring signup
    $_SESSION['pending_order'] = true;
    $_SESSION['redirect_after_signup'] = '../user/thank_you.php';
    // Redirect to signup page
    header('Location: ../user/signup.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Confirmation - CleanAura</title>
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
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            padding: 2rem 1rem;
            text-align: center;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 6px #6d9a78;
        }
        
        .container {
            max-width: 850px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: var(--background-color);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
        }
        
        .order-summary {
            margin-bottom: 2rem;
        }
        
        .order-details {
            border: 1px solid #ddd;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
        }
        
        .order-details h3 {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dotted #eee;
        }
        
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        
        .price-row {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }
        
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }
        
        .back-button, .confirm-button {
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .back-button {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .confirm-button {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            border: none;
            cursor: pointer;
        }
        
        .back-button:hover {
            background-color: #eee;
        }
        
        .confirm-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px #8FBB99;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            overflow: auto;
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 15% auto;
            padding: 2rem;
            border-radius: var(--border-radius);
            max-width: 500px;
            box-shadow: var(--card-shadow);
            position: relative;
        }
        
        .modal-title {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 1rem;
        }
        
        .modal-options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .option-button {
            padding: 1rem;
            border-radius: var(--border-radius);
            text-align: center;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .login-btn {
            background-color: var(--gray-light);
            color: var(--text-color);
            border: 1px solid #ddd;
        }
        
        .signup-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            border: none;
        }
        
        .login-btn:hover {
            background-color: #e5e5e5;
        }
        
        .signup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(21, 196, 85, 0.3);
        }
    </style>
</head>
<body>

<section class="header">
    <h1>Order Confirmation</h1>
    <div class="breadcrumb">
        <a href="../index.php">Home</a> > 
        <a href="simulation.php">Simulation</a> > 
        <span>Confirmation</span>
    </div>
</section>

<div class="container">
    <div class="order-summary">
        <h2>Please Review Your Order</h2>
        <p>Check the details below and confirm your cleaning service order.</p>
        
        <div class="order-details">
            <h3>Order Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Service Type:</span>
                <span><?php echo ucfirst($_SESSION['order_category']); ?> Cleaning</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Details:</span>
                <span><?php echo $_SESSION['order_details']; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Property Condition:</span>
                <span><?php echo ucfirst($_SESSION['order_condition']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Cleaning Date:</span>
                <span><?php echo date('F j, Y', strtotime($_SESSION['order_date'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Address:</span>
                <span><?php echo $_SESSION['order_address']; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span><?php echo $_SESSION['order_payment'] === 'online' ? 'Online Payment' : 'Cash on Delivery'; ?></span>
            </div>
            
            <div class="detail-row price-row">
                <span class="detail-label">Total Price:</span>
                <span> DZD<?php echo number_format($_SESSION['order_price'], 2); ?></span>
            </div>
        </div>
    </div>
    
    <form method="post" action="" id="confirmForm">
        <div class="buttons">
            <a href="sum.php" class="back-button">Back to Simulation</a>
            <button type="submit" name="confirm_order" class="confirm-button">Confirm Order</button>
        </div>
    </form>
</div>

<!-- Account Modal -->
<div id="accountModal" class="modal">
    <div class="modal-content">
        <h2 class="modal-title">Account Required</h2>
        <p>You need to be logged in to complete your order. Do you have an account?</p>
        
        <div class="modal-options">
            <form method="post" action="../user/logincom.php">
                <button type="submit" name="login_option" class="option-button login-btn">I have an account</button>
            </form>
            <form method="post" action="../user/singup.php">
                <button type="submit" name="signup_option" class="option-button signup-btn">I need to create an account</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("accountModal");
    
    // Show modal if form was submitted but user is not logged in
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_order']) && (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true)): ?>
        modal.style.display = "block";
    <?php endif; ?>
    
    // Close the modal if user clicks outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>