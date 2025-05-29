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

// Fetch management notes from the database
$stmt = $pdo->prepare("SELECT * FROM ManagementNotes ORDER BY created_at DESC");
$stmt->execute();
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCo Employee Portal</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png" >
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

.menu ul li a:hover, .menu ul li a.active {
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

.menu:hover ~ .content {
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

/* Notes Section Styles */
.notes-container {
    margin-top: 20px;
}

.note-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: box-shadow 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.note-card:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.note-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.note-type {
    font-weight: bold;
    font-size: 18px;
    color: #1e5b9f;
}

.date {
    font-size: 0.9em;
    color: #888;
}

.note-card h3 {
    margin: 10px 0;
    font-size: 22px;
    color: #333;
}

.note-card p {
    font-size: 16px;
    line-height: 1.5;
    color: #555;
}

.note-footer {
    margin-top: 10px;
    font-size: 0.9em;
    color: #777;
    text-align: right;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .content {
        padding: 15px;
    }
    .title-info p {
        font-size: 20px;
    }
    .note-card {
        padding: 15px;
    }
}
    </style>
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
                <a href="../employer/Requests.php">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-notifications-64.png" alt="Cleaning Requests" style="width: 50px; height: 50px;">
                    <p>Cleaning Requests</p>
                </a>
            </li>
            <li>
                <a href="../employer/Notes.php" class="active">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-notes-64.png" alt="Management Notes" style="width: 50px; height: 50px;">
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
            <p>Management Notes</p>
        </div>

        <div class="notes-container">
            <?php foreach ($notes as $note): ?>
            <div class="note-card">
                <div class="note-header">
                    
                    <span class="date"><?php echo htmlspecialchars($note['created_at']); ?></span>
                </div>
                <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($note['details'])); ?></p>
                <div class="note-footer">
                    <span class="author">From: Cleanora Administration Team</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>