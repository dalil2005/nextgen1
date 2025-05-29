<?php

require '../includes/config.php';
session_start();
$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (:username, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        $successMessage = "Admin added successfully!";
    } else {
        $successMessage = "Error adding admin.";
    }
}

include '../admin/count.php';

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
    
    <style>
       
/* Form Container Styles */
.form-container {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    max-width: 500px;
    margin: 30px auto;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.form-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(to right, #4ba3e2, #1e5b9f);
}

.form-container h2 {
    text-align: center;
    color: #1e5b9f;
    font-size: 24px;
    margin-bottom: 25px;
    font-weight: 600;
}

.form-container form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-container input {
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
    outline: none;
}

.form-container input:focus {
    border-color: #4ba3e2;
    box-shadow: 0 0 8px rgba(75, 163, 226, 0.3);
}

.form-container button {
    background: linear-gradient(135deg, #4ba3e2, #1e5b9f);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-container button:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(30, 91, 159, 0.4);
}

.form-container button:active {
    transform: translateY(0);
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 500;
    border-left: 4px solid #28a745;
}

/* Responsive styles */
@media (max-width: 768px) {
    .form-container {
        padding: 20px;
        margin: 15px;
    }
    
    .form-container h2 {
        font-size: 20px;
    }
}
    </style>
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
            <p>Add admin</p>
            <i class="fas fa-chart-bar"></i>
        </div>

        <div class="data-info">
        
            <div class="box">
                <i class="fas fa-dollar"></i>
                <div class="data">
                    <p>Admins</p>
                    <img src="../images/icons8-admin-64 (2).png" alt="Description of Image" style="width: 50px; height: 50px;">
                    <div>
                        <span><?php echo htmlspecialchars($adminsCount); ?> </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form for adding a new admin -->
        <div class="form-container">
            <h2>Add Admin</h2>
            <?php if ($successMessage): ?>
                <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password"
       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}"
       title="Must contain at least 8 characters, including uppercase, lowercase, number, and special character"
       required>
                <button type="submit">Add Admin</button>
            </form>
        </div>
    </div>
</body>
</html>