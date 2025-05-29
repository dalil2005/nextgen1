<?php
require '../includes/config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['employer_loggedin']) || $_SESSION['employer_loggedin'] !== true) {
    header('Location: ../employer/login.php');
    exit;
}

// Get the employer's full name from the session
$employerName = $_SESSION['full_name'];
$employerID = $_SESSION['employer_id']; // Assuming you store employer ID in session

// Process cancellation reason if form submitted
if (isset($_POST['submit_cancel_reason'])) {
    $orderID = $_POST['order_id'];
    $cancelReason = $_POST['cancel_reason'];
    
    // Update both the cancel reason AND the status to canceled in one query
    $updateOrder = $pdo->prepare("
        UPDATE Orders 
        SET CancelReason = :reason, Status = 'canceled' 
        WHERE ID = :id
    ");
    
    $updateOrder->execute([
        'reason' => $cancelReason,
        'id' => $orderID
    ]);
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Process payment status update if form submitted
if (isset($_POST['update_payment'])) {
    $paymentID = $_POST['payment_id'];
    $newStatus = $_POST['payment_status'];
    
    $updatePayment = $pdo->prepare("
        UPDATE Payments 
        SET PaymentStatus = :status 
        WHERE PaymentID = :id
    ");
    
    $updatePayment->execute([
        'status' => $newStatus,
        'id' => $paymentID
    ]);
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Process order status update if form submitted
if (isset($_POST['update_order'])) {
    $orderID = $_POST['order_id'];
    $newStatus = $_POST['order_status'];
    
    // Only update status directly if it's not being changed to canceled
    // For canceled status, we'll handle it through the modal form
    if ($newStatus != 'canceled') {
        $updateOrder = $pdo->prepare("
            UPDATE Orders 
            SET Status = :status 
            WHERE ID = :id
        ");
        
        $updateOrder->execute([
            'status' => $newStatus,
            'id' => $orderID
        ]);
        
        // Redirect to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    // If status is changed to canceled, we don't update the database yet
    // Instead, the JavaScript will show the modal for entering a reason
}

// Process search if form submitted
$searchTerm = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = trim($_GET['search']);
}

// Fetch orders assigned to this employer/worker with payment information and search functionality
if (!empty($searchTerm)) {
    // Search by Order ID (exact match) OR Client Name (partial match)
    $stmt = $pdo->prepare("
        SELECT 
            o.ID, 
            o.CleaningDate, 
            o.ServiceCategory, 
            o.NumberOfRooms, 
            o.AreaSize, 
            o.NumberOfFloors, 
            o.PropertyCondition,
            o.Status AS OrderStatus,
            o.PaymentMethod,
            o.CancelReason,
            CONCAT(c.FirstName, ' ', c.LastName) AS ClientName,
            o.City, 
            o.Municipality, 
            o.BuildingAddress,
            p.PaymentID,
            p.PaymentStatus,
            p.Amount
        FROM Orders o
        JOIN OrderWorkers ow ON o.ID = ow.OrderID
        JOIN Clients c ON o.ClientID = c.ID
        LEFT JOIN Payments p ON o.ID = p.OrderID
        WHERE ow.WorkerID = :employerID 
        AND (
            o.ID = :searchId 
            OR c.FirstName LIKE :searchName 
            OR c.LastName LIKE :searchName 
            OR CONCAT(c.FirstName, ' ', c.LastName) LIKE :searchName
        )
        ORDER BY o.CleaningDate DESC
    ");
    
    $stmt->execute([
        'employerID' => $employerID,
        'searchId' => is_numeric($searchTerm) ? $searchTerm : 0,
        'searchName' => '%' . $searchTerm . '%'
    ]);
} else {
    // Original query when no search term
    $stmt = $pdo->prepare("
        SELECT 
            o.ID, 
            o.CleaningDate, 
            o.ServiceCategory, 
            o.NumberOfRooms, 
            o.AreaSize, 
            o.NumberOfFloors, 
            o.PropertyCondition,
            o.Status AS OrderStatus,
            o.PaymentMethod,
            o.CancelReason,
            CONCAT(c.FirstName, ' ', c.LastName) AS ClientName,
            o.City, 
            o.Municipality, 
            o.BuildingAddress,
            p.PaymentID,
            p.PaymentStatus,
            p.Amount
        FROM Orders o
        JOIN OrderWorkers ow ON o.ID = ow.OrderID
        JOIN Clients c ON o.ClientID = c.ID
        LEFT JOIN Payments p ON o.ID = p.OrderID
        WHERE ow.WorkerID = :employerID
        ORDER BY o.CleaningDate DESC
    ");
    
    $stmt->execute(['employerID' => $employerID]);
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleanora Employee Portal</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            background-color: #f5f7fa;
            height: 100vh;
            overflow: hidden;
        }

        /* Menu Styles */
        .menu {
            width: 80px;
            background: linear-gradient(135deg, #4ba3e2, #1e5b9f);
            height: 100%;
            padding: 20px 0;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            transition: all 0.3s ease;
            position: fixed;
            left: 0;
            z-index: 999;
            -ms-overflow-style: none;
            scrollbar-width: none;
            border-top-right-radius: 40px;
        }

        .menu::-webkit-scrollbar {
            display: none;
        }

        .menu:hover {
            width: 260px;
        }

        .menu ul {
            list-style: none;
            padding: 0;
        }

        .menu .Profile {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
            white-space: nowrap;
        }

        .menu .Profile .img-box {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 10px;
            border: 3px solid #fff;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .menu:hover .Profile .img-box {
            width: 80px;
            height: 80px;
        }

        .menu .Profile .img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu .Profile h2 {
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }

        .menu:hover .Profile h2 {
            opacity: 1;
        }

        .menu ul li a {
            display: flex;
            align-items: center;
            padding: 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            border-left: 5px solid transparent;
            white-space: nowrap;
            overflow: hidden;
        }

        .menu ul li a:hover,
        .menu ul li a.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 5px solid #f5f7fa;
        }

        .menu ul li a img {
            min-width: 50px;
            transition: all 0.3s ease;
        }

        .menu ul li a p {
            margin-left: 15px;
            font-size: 15px;
            font-weight: 500;
            opacity: 0;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }

        .menu:hover ul li a p {
            opacity: 1;
        }

        .menu ul li a .count {
            position: absolute;
            right: 20px;
            background: #fff;
            color: #4ba3e2;
            padding: 3px 8px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .menu:hover ul li a .count {
            opacity: 1;
        }

        .menu ul .log-out {
            position: absolute;
            bottom: 20px;
            width: 100%;
        }

        .menu ul .log-out a {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu ul .log-out a:hover {
            background-color: #ff5a5f;
            border-left: 5px solid #ff5a5f;
        }

        /* Content Styles */
        .content {
            flex: 1;
            margin-left: 70px;
            padding: 25px;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .menu:hover~.content {
            margin-left: 260px;
        }

        .title-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .title-info p {
            font-size: 25px;
            font-weight: 600;
            color: #1e5b9f;
        }

        /* Orders Section Styles */
        .orders-container {
            margin-top: 20px;
        }

        .order-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: box-shadow 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .order-card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .order-type {
            font-weight: bold;
            font-size: 18px;
            color: #1e5b9f;
        }

        .date {
            font-size: 0.9em;
            color: #888;
        }

        .order-card h3 {
            margin: 10px 0;
            font-size: 22px;
            color: #333;
        }

        .order-card p {
            font-size: 16px;
            line-height: 1.5;
            color: #555;
            margin-bottom: 5px;
        }

        .order-footer {
            margin-top: 15px;
            font-size: 0.9em;
            color: #777;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .order-details {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
        }

        .badge-private {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .badge-professional {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .badge-cash {
            background-color: #fff3e0;
            color: #e65100;
        }

        .badge-online {
            background-color: #e0f2f1;
            color: #00796b;
        }

        .badge-pending {
            background-color: #ffecb3;
            color: #ff8f00;
        }

        .badge-completed {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .badge-failed {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .badge-in-progress {
            background-color: #e1f5fe;
            color: #0288d1;
        }

        .badge-canceled {
            background-color: #f5f5f5;
            color: #616161;
        }

        /* Status Update Forms */
        .status-forms {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .status-form {
            margin-bottom: 10px;
        }

        .status-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .status-form select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background-color: #fff;
            width: 150px;
            margin-right: 10px;
        }

        .status-form button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background-color: #4ba3e2;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .status-form button:hover {
            background-color: #1e5b9f;
        }

        /* Cancellation Form Styles */
        .cancel-reason-form {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border: 1px solid #e0e0e0;
        }

        .cancel-reason-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 100px;
            margin-bottom: 10px;
            resize: vertical;
        }

        .cancel-reason-form button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background-color: #ff5a5f;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cancel-reason-form button:hover {
            background-color: #e04146;
        }

        .cancel-reason-display {
            margin-top: 10px;
            font-style: italic;
            color: #e04146;
        }

        /* Modal Styles for Cancellation Reason */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: modalopen 0.4s;
        }

        @keyframes modalopen {
            from {opacity: 0; transform: translateY(-60px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .content {
                padding: 15px;
            }

            .title-info p {
                font-size: 20px;
            }

            .order-card {
                padding: 15px;
            }
            
            .modal-content {
                width: 90%;
                margin: 30% auto;
            }
        }
        .search-container {
    background: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.search-input {
    flex: 1;
    min-width: 250px;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #4ba3e2;
}

.search-btn {
    padding: 12px 25px;
    background: linear-gradient(135deg, #4ba3e2, #1e5b9f);
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(75, 163, 226, 0.4);
}

.clear-btn {
    padding: 12px 20px;
    background: #f5f5f5;
    color: #666;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.clear-btn:hover {
    background: #e0e0e0;
    color: #333;
}

.search-results-info {
    margin-top: 15px;
    padding: 10px 15px;
    background: #e3f2fd;
    border-radius: 8px;
    color: #1976d2;
    font-weight: 500;
}

@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input {
        min-width: auto;
        width: 100%;
    }
    
    .search-btn,
    .clear-btn {
        width: 100%;
    }
}
.scan{
    background-color: white;
    border-color:rgba(227, 242, 253, 0);
}
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for order status changes
            const orderStatusSelects = document.querySelectorAll('select[name="order_status"]');
            
            orderStatusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value === 'canceled') {
                        // Find the order ID from the form
                        const form = this.closest('form');
                        const orderId = form.querySelector('input[name="order_id"]').value;
                        
                        // Show the modal for this order
                        document.getElementById('cancelModal_' + orderId).style.display = 'block';
                        
                        // Prevent the regular form submission
                        form.addEventListener('submit', function(e) {
                            if (select.value === 'canceled') {
                                e.preventDefault();
                            }
                        });
                    }
                });
            });
            
            // Close modal when clicking the X
            const closeButtons = document.querySelectorAll('.close-modal');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Reset the select value when the modal is closed without submitting
                    const orderId = this.closest('.modal').id.replace('cancelModal_', '');
                    const select = document.getElementById('order_status_' + orderId);
                    
                    // Get the current status from the badge
                    const statusBadge = document.querySelector(`[data-order-id="${orderId}"]`);
                    const currentStatus = statusBadge ? statusBadge.dataset.status : 'pending';
                    
                    // Reset the select value
                    select.value = currentStatus;
                    
                    // Hide the modal
                    this.closest('.modal').style.display = 'none';
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    if (event.target === modal) {
                        // Reset the select value when the modal is closed without submitting
                        const orderId = modal.id.replace('cancelModal_', '');
                        const select = document.getElementById('order_status_' + orderId);
                        
                        // Get the current status from the badge
                        const statusBadge = document.querySelector(`[data-order-id="${orderId}"]`);
                        const currentStatus = statusBadge ? statusBadge.dataset.status : 'pending';
                        
                        // Reset the select value
                        select.value = currentStatus;
                        
                        // Hide the modal
                        modal.style.display = 'none';
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="menu">
        <li class="Profile">
            <div class="img-box">
                <?php if (isset($employer['Sex']) && strtolower($employer['Sex']) == 'female'): ?>
                    <img src="../images/icons8-female-user-64.png" alt="female profile">
                <?php else: ?>
                    <img src="../images/icons8-male-user-64.png" alt="male profile">
                <?php endif; ?>
            </div>
            <h2><?php echo htmlspecialchars($employerName); ?></h2>
        </li>
        <ul>
            <li>
                <a href="../employer/Requests.php" class="active">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-notifications-64.png" alt="Cleaning Requests" style="width: 50px; height: 50px;">
                    <p>Cleaning Requests</p>
                </a>
            </li>
            <li>
                <a href="../employer/Notes.php">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-notes-64.png" alt="Cleaning Orders" style="width: 50px; height: 50px;">
                    <p>Management Notes</p>
                </a>
            </li>
            <li>
                <a href="../employer/index.php">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-profile-64.png" alt="Employee Information" style="width: 50px; height: 50px;">
                    <p>Employee Information</p>
                </a>
            </li>
            <li class="fas">
                <a href="../employer/logout1.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <img src="../images/icons8-log-out-64.png" alt="Description of Image" style="width: 50px; height: 50px;">
                    <p>Log out</p>
                </a>
            </li>
        </ul>
    </div>

    <div class="content">
        <div class="title-info">
            <p>My Cleaning Orders</p>
        </div>
         <div class="search-container">
            <form class="search-form" method="GET" action="">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search by Order ID or Client Name..." 
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                >
                <button type="submit" class="search-btn">
                    🔍 Search
                </button>
                <button type="" class="scan">
                    <img src="../images/icons8-qr-code-64.png" alt="">
                </button>
                <?php if (!empty($searchTerm)): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="clear-btn">
                        ✕ Clear
                    </a>
                <?php endif; ?>
            </form>
            
            <?php if (!empty($searchTerm)): ?>
                <div class="search-results-info">
                    <?php if (count($orders) > 0): ?>
                        Found <?php echo count($orders); ?> order(s) matching "<?php echo htmlspecialchars($searchTerm); ?>"
                    <?php else: ?>
                        No orders found matching "<?php echo htmlspecialchars($searchTerm); ?>"
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="orders-container">
            <?php if (empty($orders)): ?>
                <div class="order-card">
                    <p>You currently have no assigned cleaning orders.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span class="order-type">
                                <span class="badge <?php echo $order['ServiceCategory'] === 'private' ? 'badge-private' : 'badge-professional'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($order['ServiceCategory'])); ?>
                                </span>
                                <span class="badge badge-<?php echo strtolower($order['OrderStatus']); ?>" 
                                      data-order-id="<?php echo $order['ID']; ?>" 
                                      data-status="<?php echo $order['OrderStatus']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($order['OrderStatus']))); ?>
                                </span>
                                Order #<?php echo htmlspecialchars($order['ID']); ?>
                            </span>
                            <span class="badge badge-<?php echo strtolower($order['PaymentMethod']); ?>">
                                <?php echo ucfirst(htmlspecialchars($order['PaymentMethod'])); ?> Payment
                            </span>
                        </div>
                        <h3>
                            <?php
                            echo htmlspecialchars($order['City']) . ', ' .
                                htmlspecialchars($order['Municipality']) . ', ' .
                                htmlspecialchars($order['BuildingAddress']);
                            ?>
                        </h3>
                        <div class="order-details">
                            <p><strong>Client:</strong> <?php echo htmlspecialchars($order['ClientName']); ?></p>
                            <?php if ($order['NumberOfRooms']): ?>
                                <p><strong>Rooms:</strong> <?php echo htmlspecialchars($order['NumberOfRooms']); ?></p>
                            <?php endif; ?>
                            <?php if ($order['AreaSize']): ?>
                                <p><strong>Area Size:</strong> <?php echo htmlspecialchars($order['AreaSize']); ?> m²</p>
                            <?php endif; ?>
                            <?php if ($order['NumberOfFloors']): ?>
                                <p><strong>Floors:</strong> <?php echo htmlspecialchars($order['NumberOfFloors']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($order['CleaningDate'])): ?>
                                <p class="date">
                                    <strong>Cleaning Date:</strong> <?php echo date('F j, Y', strtotime($order['CleaningDate'])); ?>
                                </p>
                            <?php endif; ?>
                            <p><strong>Property Condition:</strong> <?php echo ucfirst(htmlspecialchars($order['PropertyCondition'])); ?></p>
                            
                            <?php if (isset($order['PaymentStatus'])): ?>
                                <p>
                                    <strong>Payment Status:</strong> 
                                    <span class="badge badge-<?php echo strtolower($order['PaymentStatus']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($order['PaymentStatus'])); ?>
                                    </span>
                                </p>
                                <p><strong>Amount:</strong> <?php echo htmlspecialchars($order['Amount']); ?> €</p>
                            <?php endif; ?>
                            
                            <?php if ($order['OrderStatus'] == 'canceled' && !empty($order['CancelReason'])): ?>
                                <p class="cancel-reason-display">
                                    <strong>Cancellation Reason:</strong> <?php echo htmlspecialchars($order['CancelReason']); ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="status-forms">
                            <!-- Order Status Update Form -->
                            <form class="status-form" method="post" action="">
                                <label for="order_status_<?php echo $order['ID']; ?>">Update Order Status:</label>
                                <input type="hidden" name="order_id" value="<?php echo $order['ID']; ?>">
                                <select name="order_status" id="order_status_<?php echo $order['ID']; ?>">
                                    <option value="pending" <?php echo $order['OrderStatus'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="in_progress" <?php echo $order['OrderStatus'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo $order['OrderStatus'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="canceled" <?php echo $order['OrderStatus'] == 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                                </select>
                                <button type="submit" name="update_order">Update</button>
                            </form>

                            <!-- Payment Status Update Form (Only for cash payments) -->
                            <?php if ($order['PaymentMethod'] == 'cash' && isset($order['PaymentID'])): ?>
                                <form class="status-form" method="post" action="">
                                    <label for="payment_status_<?php echo $order['PaymentID']; ?>">Update Payment Status:</label>
                                    <input type="hidden" name="payment_id" value="<?php echo $order['PaymentID']; ?>">
                                    <select name="payment_status" id="payment_status_<?php echo $order['PaymentID']; ?>">
                                        <option value="pending" <?php echo $order['PaymentStatus'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="completed" <?php echo $order['PaymentStatus'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="failed" <?php echo $order['PaymentStatus'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                    </select>
                                    <button type="submit" name="update_payment">Update</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Modal for Cancellation Reason -->
                        <div id="cancelModal_<?php echo $order['ID']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close-modal">&times;</span>
                                <h3>Enter Cancellation Reason</h3>
                                <form class="cancel-reason-form" method="post" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order['ID']; ?>">
                                    <textarea name="cancel_reason" placeholder="Please provide a reason for cancellation..." required><?php echo !empty($order['CancelReason']) ? htmlspecialchars($order['CancelReason']) : ''; ?></textarea>
                                    <button type="submit" name="submit_cancel_reason">Submit Reason</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>