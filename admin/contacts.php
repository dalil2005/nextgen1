<?php
require '../includes/config.php'; // Adjust path as needed
include '../admin/count.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}

$successMessage = '';

// Fetch messages from the contact_form_submissions table
try {
    $sql = "SELECT first_name, last_name, email, phone, message, submitted_at FROM contact_form_submissions ORDER BY submitted_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all messages
} catch (PDOException $e) {
    echo "Error fetching messages: " . $e->getMessage();
}

$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png" >
</head>
<style>
    .messages-section {
    margin-top: 20px;
}

.messages-section table {
    width: 100%;
    border-collapse: collapse;
}

.messages-section th, .messages-section td {
    border: 1px solid #ddd;
    padding: 8px;
}

.messages-section th {
    background-color: #f2f2f2;
}
thead {
    background: linear-gradient(135deg, #4ba3e2, #1e5b9f);
}
th {
    padding: 15px;
    text-align: left;
    color: #1e5b9f;
    font-weight: 600;
    font-size: 15px;
    text-transform: uppercase;
}
tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid#1e5b9f;
}
</style>
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
        <div class="messages-section">
    <h2>User Messages</h2>
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Submitted At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="6">No messages found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($message['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($message['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['phone']); ?></td>
                        <td><?php echo htmlspecialchars($message['message']); ?></td>
                        <td><?php echo htmlspecialchars($message['submitted_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    </div>
</body>
</html>