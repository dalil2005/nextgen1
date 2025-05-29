<?php
require '../includes/config.php';
include '../admin/count.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}
$successMessage = '';


$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<style>
/* Payment History Section - Styled */
.payment-history {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 12px;
    margin-top: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 22px;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e6e6e6;
}

.payments-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.payment-card {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

.payment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    background-color: #f0f8ff;
    border-bottom: 1px solid #e6e6e6;
}

.payment-info {
    display: flex;
    flex-direction: column;
}

.payment-id {
    font-weight: bold;
    color: #333;
}

.payment-date {
    font-size: 13px;
    color: #666;
    margin-top: 3px;
}

.payment-status {
    padding: 5px 10px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.payment-content {
    padding: 15px;
}

.payment-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 8px;
    border-bottom: 1px dashed #eee;
}

.detail-label {
    font-size: 14px;
    color: #666;
}

.detail-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.payment-amount {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    text-align: right;
    margin-top: 15px;
}

.payment-method {
    display: flex;
    align-items: center;
    gap: 5px;
}

.payment-method-icon {
    width: 20px;
    height: 20px;
}

.no-payments {
    text-align: center;
    color: #777;
    padding: 30px;
    background-color: white;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.no-payments-icon {
    width: 80px;
    height: 80px;
    opacity: 0.5;
}

.error-message {
    background-color: #fff0f0;
    border-left: 4px solid #ff5252;
    padding: 15px;
    color: #d32f2f;
    border-radius: 4px;
    margin-top: 20px;
}
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>payments history</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png">
</head>

<body>
    <div class="menu">
        <ul>
            <li class="Profile">
                <div class="img-box">
                    <img src="../images/icons8-admin-64 (3).png" alt="profile">
                </div>
                <h2><?php echo htmlspecialchars($adminName); ?></h2>
            </li>
            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);
            include '../admin/menu.php';
            ?>

        </ul>
    </div>

    <div class="content">
        <div class="title-info">
            <p>Dashboard</p>
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="data-info">
            <div class="box">
                <i class="fas fa-user"></i>
                <div class="data">
                    <p>Users</p>
                    <img src="../images/icons8-users-64.png" alt="Description of Image" style="width: 50px; height: 50px;">
                    <div>
                        <span><?php echo htmlspecialchars($usersCount); ?></span>
                    </div>
                </div>
            </div>
            <div class="box">
                <i class="fas fa-table"></i>
                <div class="data">
                    <p>orders</p>
                    <img src="../images/icons8-create-order-64.png" alt="Description of Image" style="width: 50px; height: 50px;">
                    <div>
                        <span><?php echo htmlspecialchars($orderCount); ?></span>
                    </div>
                </div>
            </div>
            <div class="box">
                <i class="fas fa-user-group"></i>
                <div class="data">
                    <p>Workers</p>
                    <img src="../images/icons8-cleaner-64.png" alt="Description of Image" style="width: 50px; height: 50px;">
                    <div>
                        <span><?php echo htmlspecialchars($workerCount); ?></span>
                    </div>
                </div>
            </div>
            <div class="box">
                <i class="fas fa-star"></i>
                <div class="data">
                    <p>Average Rating</p>
                    <img src="../images/icons8-rate-64.png" alt="Star Icon" style="width: 50px; height: 50px;">
                    <div>
                        <span><?php echo htmlspecialchars($averageRating !== null ? number_format($averageRating, 1) : "No ratings available"); ?></span>
                    </div>
                    <p>Total Rated Orders: <strong><?php echo htmlspecialchars($ratedOrders); ?></strong></p>
                </div>
            </div>
            <div class="box">
                <i class="fas fa-dollar"></i>
                <div class="data">
                    <p>Revenue</p>
                    <img src="../images/icons8-revenue-64.png" alt="Description of Image" style="width: 50px; height: 50px;">
                    <div>
                        <span><?php echo htmlspecialchars($total_revenue); ?> DZD</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="payment-history">
        <?php
try {
    // Query to get only completed payments with order and client details
    $stmt = $pdo->prepare("SELECT p.PaymentID, p.OrderID, p.PaymentMethod, p.PaymentStatus, 
                         p.Amount, p.PaymentDate, c.firstName,c.lastName
                         FROM Payments p
                         JOIN Orders o ON p.OrderID = o.ID
                         JOIN Clients c ON o.ClientID = c.ID
                         WHERE p.PaymentStatus = 'completed'
                         ORDER BY p.PaymentDate DESC
                         LIMIT 5");
    $stmt->execute();
    
    echo '<h2 class="section-title">Recent Completed Payments</h2>';
    
    if ($stmt->rowCount() > 0) {
        echo '<div class="payments-container">';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $paymentID = $row['PaymentID'];
            $orderID = $row['OrderID'];
            $paymentMethod = $row['PaymentMethod'];
            $amount = $row['Amount'];
            $paymentDate = date('M d, Y H:i', strtotime($row['PaymentDate']));
            $firstName = htmlspecialchars($row['firstName']);
            $lastName = htmlspecialchars($row['lastName']);
            
            // Choose appropriate icon for payment method
            $methodIcon = ($paymentMethod == 'online') ? 
                'icons8-card-payment-64.png' : 'icons8-money-64.png';
            
            echo '<div class="payment-card">';
            
            echo '<div class="payment-header">';
            echo '<div class="payment-info">';
            echo '<span class="payment-id">Payment #' . $paymentID . '</span>';
            echo '<span class="payment-date">' . $paymentDate . '</span>';
            echo '</div>';
            echo '<span class="payment-status status-completed">Completed</span>';
            echo '</div>'; // End payment-header
            
            echo '<div class="payment-content">';
            echo '<div class="payment-details">';
            
            echo '<div class="detail-row">';
            echo '<span class="detail-label">Order ID</span>';
            echo '<span class="detail-value">#' . $orderID . '</span>';
            echo '</div>';
            
            echo '<div class="detail-row">';
            echo '<span class="detail-label">Client</span>';
            echo '<span class="detail-value">' . $firstName . ' ' . $lastName . '</span>';

            echo '</div>';
            
            echo '<div class="detail-row">';
            echo '<span class="detail-label">Method</span>';
            echo '<div class="payment-method detail-value">';
           
            echo ' ' . ucfirst($paymentMethod);
            echo '</div>'; // End payment-method
            echo '</div>'; // End detail-row
            
            echo '</div>'; // End payment-details
            
            echo '<div class="payment-amount">' . number_format($amount, 2) . ' DZD</div>';
            
            echo '</div>'; // End payment-content
            echo '</div>'; // End payment-card
        }
        echo '</div>'; // End payments-container
    } else {
        echo '<div class="no-payments">';
        echo '<img src="../images/icons8-no-payment-64.png" alt="No Payments" class="no-payments-icon">';
        echo '<p>No completed payments available yet</p>';
        echo '</div>';
    }
} catch (PDOException $e) {
    echo '<div class="error-message">';
    echo '<p>Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
}
?>
        </div>
    </div>
</body>

</html>