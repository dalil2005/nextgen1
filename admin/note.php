<?php
require '../includes/config.php';
include '../admin/count.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}

$successMessage = '';
$errorMessage = '';

// Handle form submission for sending a note
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $details = $_POST['details'];
    
    // Insert the note into the database
    $stmt = $pdo->prepare("INSERT INTO ManagementNotes (title, details) VALUES (:title, :details)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':details', $details);
    
    if ($stmt->execute()) {
        $successMessage = "Note sent successfully!";
    } else {
        $errorMessage = "Failed to send note. Please try again.";
    }
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
    .send-note {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-top: 20px;
}

.send-note h2 {
    color: #1e5b9f;
    margin-bottom: 20px;
    font-size: 24px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    resize: vertical; /* Allow vertical resizing for textarea */
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #4ba3e2;
    outline: none;
    box-shadow: 0 0 5px rgba(74, 163, 226, 0.5);
}

button {
    background-color: #4ba3e2;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background-color: #3a93d5;
}

.success-message, .error-message {
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 5px;
}

.success-message {
    background-color: #dff0d8;
    color: #3c763d;
    border: 1px solid #d6e9c6;
}

.error-message {
    background-color: #f2dede;
    color: #a94442;
    border: 1px solid #ebccd1;
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
            <p>send note</p>
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

        <!-- Form to send a note to all employers -->
        <div class="send-note">
            <h2>Send Note to All Employers</h2>
            <?php if ($successMessage): ?>
                <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
                <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="details">Details</label>
                    <textarea id="details" name="details" rows="5" required></textarea>
                </div>
                <button type="submit">Send Note</button>
            </form>
        </div>
    </div>
</body>
</html>