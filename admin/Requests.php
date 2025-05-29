<?php
// Include your database connection
require '../includes/config.php';
session_start();
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}
// Initialize a success message variable
$successMessage = '';


// Handle Accept or Decline actions
if (isset($_POST['action'], $_POST['id'])) {
    $action = $_POST['action'];
    $id = $_POST['id'];

    if ($action === 'accept') {
    // Update the worker's status to 'accepted'
    $stmt = $pdo->prepare("UPDATE CleaningWorkers SET Status = 'accepted', AcceptedAt = NOW() WHERE ID = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Fetch worker's first and last name
    $stmt = $pdo->prepare("SELECT FirstName, LastName FROM CleaningWorkers WHERE ID = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);

    $fullName = $worker ? $worker['FirstName'] . ' ' . $worker['LastName'] : 'New Worker';

    // Insert welcome message into ManagementNotes table
    $welcomeTitle = 'Welcome to the Team!';
    $welcomeDetails = "We're excited to welcome $fullName to the company. We look forward to achieving great things together!";
    
    $stmt = $pdo->prepare("INSERT INTO ManagementNotes (title, details) VALUES (:title, :details)");
    $stmt->bindParam(':title', $welcomeTitle);
    $stmt->bindParam(':details', $welcomeDetails);
    $stmt->execute();



    } elseif ($action === 'decline') {
        // Remove the worker from CleaningWorkers
        $stmt = $pdo->prepare("DELETE FROM CleaningWorkers WHERE ID = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}

// Fetch pending requests
$stmt = $pdo->query("SELECT * FROM CleaningWorkers WHERE Status = 'pending'");
$pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// count 
include '../admin/count.php';


$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png" >
</head>
     <style>
    
h2 {
    color: #1e5b9f;
    font-size: 24px;
    margin-bottom: 20px;
    font-weight: 600;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 30px;
}

thead {
    background: linear-gradient(135deg, #4ba3e2, #1e5b9f);
}

th {
    padding: 15px;
    text-align: left;
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    text-transform: uppercase;
}

tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f0f0f0;
}

tbody tr:last-child {
    border-bottom: none;
}

tbody tr:hover {
    background-color: #f9fbff;
}

td {
    padding: 15px;
    color: #555;
    font-size: 14px;
}

/* Button Styles */
td form {
    display: inline-block;
    margin-right: 5px;
}

td button {
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

td button[value="accept"] {
    background-color: #4ba3e2;
    color: white;
}

td button[value="accept"]:hover {
    background-color: #3a93d5;
    box-shadow: 0 2px 8px rgba(74, 163, 226, 0.3);
}

td button[value="decline"] {
    background-color: #ff5a5f;
    color: white;
}

td button[value="decline"]:hover {
    background-color: #e84b50;
    box-shadow: 0 2px 8px rgba(255, 90, 95, 0.3);
}

/* Responsive styles */
@media (max-width: 992px) {
    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}

@media (max-width: 768px) {
    h2 {
        font-size: 20px;
    }
    
    th, td {
        padding: 12px 10px;
        font-size: 13px;
    }
    
    td button {
        padding: 6px 12px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    th, td {
        padding: 10px 8px;
        font-size: 12px;
    }
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
            <p>Requests</p>
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="data-info">
        
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
           
        </div>

        <!-- Table for Pending Requests -->
        <h2>Pending Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Sex</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Specialty</th> <!-- Added Specialty column -->
                    <th>Phone Number</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingRequests as $request): ?>
                <tr>
                <td><?php echo htmlspecialchars($request['FirstName'] . ' ' . $request['LastName']); ?></td>

                    <td><?php echo htmlspecialchars($request['Sex']); ?></td>
                    <td><?php echo htmlspecialchars($request['Age']); ?></td>
                    <td><?php echo htmlspecialchars($request['Email']); ?></td>
                    <td><?php echo htmlspecialchars($request['Specialty']); ?></td> <!-- Display Specialty -->
                    <td><?php echo htmlspecialchars($request['PhoneNumber']); ?></td>
                    <td><?php echo htmlspecialchars($request['City']); ?></td>
                    <td>
                        <form action="../admin/Requests.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $request['ID']; ?>">
                            <button type="submit" name="action" value="accept">Accept</button>
                        </form>
                        <form action="../admin/Requests.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $request['ID']; ?>">
                            <button type="submit" name="action" value="decline">Decline</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>