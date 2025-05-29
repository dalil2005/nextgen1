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

// Handle employer assignment if requested
if (isset($_POST['assign_employer']) && isset($_POST['order_id']) && isset($_POST['worker_id'])) {
    $orderId = $_POST['order_id'];
    $workerIds = $_POST['worker_id']; // This will now be an array

    foreach ($workerIds as $workerId) {
        // Check if this assignment already exists
        $checkQuery = "SELECT * FROM OrderWorkers WHERE OrderID = ? AND WorkerID = ?";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([$orderId, $workerId]);

        if ($checkStmt->rowCount() > 0) {
            $errorMessage = "One or more workers are already assigned to this order!";
            continue; // Skip to the next worker
        } else {
            // Insert new assignment
            $insertQuery = "INSERT INTO OrderWorkers (OrderID, WorkerID, AssignedDate) VALUES (?, ?, NOW())";
            $insertStmt = $pdo->prepare($insertQuery);

            try {
                $insertStmt->execute([$orderId, $workerId]);
            } catch (PDOException $e) {
                // Handle error as before
            }
        }
    }
    $successMessage = "Workers successfully assigned to Order #$orderId";
}

// Handle removing assigned worker
if (isset($_POST['remove_worker']) && isset($_POST['assignment_id'])) {
    $assignmentId = $_POST['assignment_id'];

    $removeQuery = "DELETE FROM OrderWorkers WHERE ID = ?";
    $removeStmt = $pdo->prepare($removeQuery);

    try {
        $removeStmt->execute([$assignmentId]);
        $successMessage = "Worker successfully removed from order";
    } catch (PDOException $e) {
        $errorMessage = "Error removing worker: " . $e->getMessage();
    }
}

// Handle payment status update
if (isset($_POST['update_payment_status']) && isset($_POST['order_id']) && isset($_POST['payment_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['payment_status'];

    $updateQuery = "UPDATE Payments SET PaymentStatus = ? WHERE OrderID = ?";
    $updateStmt = $pdo->prepare($updateQuery);

    try {
        $updateStmt->execute([$newStatus, $orderId]);
        $successMessage = "Payment status successfully updated for Order #$orderId";
    } catch (PDOException $e) {
        $errorMessage = "Error updating payment status: " . $e->getMessage();
    }
}

$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

// Process order search if submitted
$searchOrderId = null;
if (isset($_GET['search_order_id']) && !empty($_GET['search_order_id'])) {
    $searchOrderId = $_GET['search_order_id'];

    // Modified query for direct address fields in Orders table
    $query = "
        SELECT o.*, 
               CONCAT(c.FirstName, ' ', c.LastName) AS FullName, 
               c.PhoneNumber, 
               c.Email, 
               p.PaymentStatus, 
               p.Amount, 
               p.PaymentDate
        FROM Orders o
        JOIN Clients c ON o.ClientID = c.ID
        LEFT JOIN Payments p ON o.ID = p.OrderID
        WHERE o.ID = ?
        LIMIT 1
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$searchOrderId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($orders)) {
            $errorMessage = "No order found with ID #$searchOrderId";
        }
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    // Fetch all orders with client information and payment status
    $query = "
        SELECT o.*, 
               CONCAT(c.FirstName, ' ', c.LastName) AS FullName, 
               c.PhoneNumber, 
               c.Email, 
               p.PaymentStatus, 
               p.Amount, 
               p.PaymentDate
        FROM Orders o
        JOIN Clients c ON o.ClientID = c.ID
        LEFT JOIN Payments p ON o.ID = p.OrderID
        ORDER BY o.OrderDate DESC
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

// Get all available workers
$workersQuery = "SELECT ID, CONCAT(FirstName, ' ', LastName) AS FullName, Specialty ,City FROM CleaningWorkers WHERE Status = 'accepted'";
try {
    $workersStmt = $pdo->prepare($workersQuery);
    $workersStmt->execute();
    $allWorkers = $workersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $allWorkers = [];
    $errorMessage = "Error loading workers: " . $e->getMessage();
}

// Create OrderWorkers table if it doesn't exist
try {
    $checkTableQuery = "SELECT 1 FROM OrderWorkers LIMIT 1";
    $pdo->query($checkTableQuery);
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') {
        $createTableQuery = "
            CREATE TABLE OrderWorkers (
                ID INT AUTO_INCREMENT PRIMARY KEY,
                OrderID INT NOT NULL,
                WorkerID INT NOT NULL,
                AssignedDate DATETIME NOT NULL,
                FOREIGN KEY (OrderID) REFERENCES Orders(ID) ON DELETE CASCADE,
                FOREIGN KEY (WorkerID) REFERENCES CleaningWorkers(ID) ON DELETE CASCADE,
                UNIQUE KEY unique_assignment (OrderID, WorkerID)
            )
        ";
        $pdo->exec($createTableQuery);
    }
}

// Function to map service category to specialty type
function mapServiceCategoryToSpecialty($serviceCategory)
{
    if (strcasecmp($serviceCategory, 'private') === 0) {
        return 'Residential Cleaning';
    } elseif (strcasecmp($serviceCategory, 'professional') === 0) {
        return 'Commercial Cleaning';
    }
    return $serviceCategory; // Default return if no match
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png">
    <style>
        .orders-container {
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .order-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .order-id {
            font-weight: bold;
            font-size: 18px;
        }

        .order-date {
            color: #666;
        }

        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 15px;
        }

        .client-info,
        .order-info {
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            font-size: 16px;
        }

        .payment-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
        }

        .generate-btn,
        .assign-btn {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 0 5px;
            cursor: pointer;
            border-radius: 4px;
        }

        .assign-btn {
            background-color: #2196F3;
        }

        .remove-btn {
            background-color: #f44336;
            border: none;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            cursor: pointer;
            border-radius: 4px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .orders-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .assign-form {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
            flex-grow: 1;
        }

        .employers-section {
            margin-top: 20px;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
        }

        .no-workers-message {
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }

        /* Search form styles */
        .search-form {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 8px;
        }

        .search-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .search-btn {
            background-color: #4ba3e2;
            border: none;
            color: white;
            padding: 10px 20px;
            margin-left: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .reset-btn {
            background-color: #607D8B;
            border: none;
            color: white;
            padding: 10px 20px;
            margin-left: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
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
            <p>Orders Management</p>
            <i class="fas fa-chart-bar"></i>
        </div>

        <div class="data-info">

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

        <div class="orders-container">
            <?php if (!empty($successMessage)): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <h2 class="orders-title">All Orders</h2>

            <!-- Search Order Form -->
            <form method="get" class="search-form">
                <input type="number" name="search_order_id" placeholder="Enter Order ID" class="search-input" value="<?php echo htmlspecialchars($searchOrderId ?? ''); ?>">
                <button type="submit" class="search-btn">Find Order</button>
                <?php if (isset($searchOrderId)): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="reset-btn">Show All Orders</a>
                <?php endif; ?>
            </form>

            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order #<?php echo htmlspecialchars($order['ID']); ?></div>
                            <div class="order-date">Ordered on: <?php echo date('F j, Y, g:i a', strtotime($order['OrderDate'])); ?></div>
                        </div>

                        <div class="order-details">
                            <div class="client-info">
                                <div class="section-title" style="color: #2196F3;">Client Information</div>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['FullName']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['PhoneNumber']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['Email']); ?></p>
                                <BR></BR>
                                <div class="section-title" style="color: #2196F3;"> Order Address </div>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($order['City']); ?></p>
                                <p><strong>Municipality:</strong> <?php echo htmlspecialchars($order['Municipality']); ?></p>
                                <p><strong>Building Address:</strong> <?php echo htmlspecialchars($order['BuildingAddress']); ?></p>
                            </div>


                            <div class="order-info">
                                <div class="section-title" style="color: #2196F3;">Order Details</div>
                                <p><strong>Service Type:</strong> <?php echo htmlspecialchars(ucfirst($order['ServiceCategory'])); ?></p>

                                <?php if ($order['NumberOfRooms']): ?>
                                    <p><strong>Number of Rooms:</strong> <?php echo htmlspecialchars($order['NumberOfRooms']); ?></p>
                                <?php endif; ?>

                                <?php if ($order['AreaSize']): ?>
                                    <p><strong>Area Size (sqm):</strong> <?php echo htmlspecialchars($order['AreaSize']); ?></p>
                                <?php endif; ?>

                                <?php if ($order['NumberOfFloors']): ?>
                                    <p><strong>Number of Floors:</strong> <?php echo htmlspecialchars($order['NumberOfFloors']); ?></p>
                                <?php endif; ?>

                                <?php if ($order['CleaningDate']): ?>
                                    <p><strong>Cleaning Date:</strong> <?php echo htmlspecialchars(date('Y-m-d', strtotime($order['CleaningDate']))); ?></p>
                                <?php endif; ?>

                                <p><strong>Property Condition:</strong> <?php echo htmlspecialchars(ucfirst($order['PropertyCondition'])); ?></p>
                                <br>
                                <div class="section-title" style="color: #2196F3;">Order Status</div>
                                <p><strong>Status:</strong>
                                    <span style="color: <?php echo ($order['Status'] === 'canceled') ? 'red' : 'inherit'; ?>;">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $order['Status']))); ?>
                                    </span>
                                </p>

                                <?php if ($order['Status'] === 'canceled'): ?>
                                    <?php if (!empty($order['CancelReason'])): ?>
                                        <p><strong>Cancel Reason:</strong>
                                            <span class="cancel-reason"><?php echo htmlspecialchars($order['CancelReason']); ?></span>
                                        </p>
                                    <?php else: ?>
                                        <p>No cancel reason provided.</p>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <br>
                                <div class="section-title" style="color: #2196F3;">Order Feedback</div>
                                <div class="feedback-container">
                                    <?php if ($order['Rating']): ?>
                                        <p><strong>Rating:</strong>
                                            <span class="rating"><?php echo htmlspecialchars($order['Rating']); ?></span> / 5
                                        </p>
                                    <?php else: ?>
                                        <p>No rating provided.</p>
                                    <?php endif; ?>

                                    <?php if ($order['Comments']): ?>
                                        <p><strong>Comment:</strong>
                                            <span class="comment"><?php echo nl2br(htmlspecialchars($order['Comments'])); ?></span>
                                        </p>
                                    <?php else: ?>
                                        <p>No comments available.</p>
                                    <?php endif; ?>
                                </div>


                            </div>
                        </div>

                        <div class="payment-info">
                            <div class="section-title" style="color: #2196F3;">Payment Information</div>
                            <p>
                                <strong>Payment Method:</strong>
                                <?php echo htmlspecialchars(ucfirst($order['PaymentMethod'])); ?>
                            </p>
                            <p>
                                <strong>Payment Status:</strong>
                                <span class="payment-status status-<?php echo htmlspecialchars(strtolower($order['PaymentStatus'] ?? 'pending')); ?>">
                                    <?php echo htmlspecialchars(ucfirst($order['PaymentStatus'] ?? 'pending')); ?>
                                </span>
                            </p>
                            <?php if (isset($order['Amount'])): ?>
                                <p><strong>Amount:</strong> <?php echo htmlspecialchars($order['Amount']); ?> DZD</p>
                            <?php endif; ?>
                            <?php if (isset($order['PaymentDate']) && $order['PaymentDate']): ?>
                                <p><strong>Payment Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['PaymentDate'])); ?></p>
                            <?php endif; ?>

                            <?php if ($order['PaymentMethod'] === 'online'): ?>
                                <form method="post" class="payment-status-form" style="margin-top: 10px;">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['ID']); ?>">

                                    <select name="payment_status" required>
                                        <option value="">-- Change Payment Status --</option>
                                        <option value="pending" <?php echo ($order['PaymentStatus'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="completed" <?php echo ($order['PaymentStatus'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="failed" <?php echo ($order['PaymentStatus'] === 'failed') ? 'selected' : ''; ?>>Failed</option>
                                    </select>
                                    <button type="submit" name="update_payment_status" class="assign-btn">Update Status</button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <div class="employers-section">
                            <div class="section-title">Assigned Workers</div>

                            <?php
                            // Get assigned workers for this order
                            $assignedQuery = "
                                SELECT ow.ID, ow.AssignedDate, CONCAT(w.FirstName, ' ', w.LastName) AS FullName, w.ID as WorkerID
                                FROM OrderWorkers ow
                                JOIN CleaningWorkers w ON ow.WorkerID = w.ID
                                WHERE ow.OrderID = ?
                                ORDER BY ow.AssignedDate DESC
                            ";
                            try {
                                $assignedStmt = $pdo->prepare($assignedQuery);
                                $assignedStmt->execute([$order['ID']]);
                                $assignedWorkers = $assignedStmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                $assignedWorkers = [];
                            }

                            // Get current order's cleaning date
                            $currentOrderDate = isset($order['CleaningDate']) ? date('Y-m-d', strtotime($order['CleaningDate'])) : null;

                            // Get all workers already assigned to orders on the same date
                            $busyWorkersQuery = "
                                SELECT DISTINCT ow.WorkerID
                                FROM OrderWorkers ow
                                JOIN Orders o ON ow.OrderID = o.ID
                                WHERE o.CleaningDate = ? AND ow.OrderID != ?
                            ";
                            $busyWorkers = [];

                            if ($currentOrderDate) {
                                try {
                                    $busyWorkersStmt = $pdo->prepare($busyWorkersQuery);
                                    $busyWorkersStmt->execute([$currentOrderDate, $order['ID']]);
                                    $busyWorkers = $busyWorkersStmt->fetchAll(PDO::FETCH_COLUMN);
                                } catch (PDOException $e) {
                                    // Handle error silently
                                }
                            }

                            // Get IDs of workers already assigned to this order
                            $assignedWorkerIds = array_column($assignedWorkers, 'WorkerID');

                            // Create a combined list of unavailable worker IDs
                            $unavailableWorkerIds = array_merge($assignedWorkerIds, $busyWorkers);

                            // Map service category to specialty type
                            $requiredSpecialty = mapServiceCategoryToSpecialty($order['ServiceCategory']);
                            ?>

                            <?php if (count($assignedWorkers) > 0): ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Worker ID</th>
                                            <th>Name</th>
                                            <th>Assigned Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($assignedWorkers as $worker): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($worker['WorkerID']); ?></td>
                                                <td><?php echo htmlspecialchars($worker['FullName']); ?></td>
                                                <td><?php echo date('F j, Y, g:i a', strtotime($worker['AssignedDate'])); ?></td>
                                                <td>
                                                    <form method="post" style="display: inline;">
                                                        <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($worker['ID']); ?>">
                                                        <button type="submit" name="remove_worker" class="remove-btn">Remove</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No workers assigned to this order yet.</p>
                            <?php endif; ?>

                            <?php
                            // Filter workers by specialty matching the order's service category
                            $categorySpecificWorkers = array_filter($allWorkers, function ($worker) use ($requiredSpecialty) {
                                // Check if worker's specialty contains the required specialty (case-insensitive)
                                return stripos($worker['Specialty'], $requiredSpecialty) !== false;
                            });

                            // Then filter out unavailable workers
                            $availableWorkers = array_filter($categorySpecificWorkers, function ($worker) use ($unavailableWorkerIds) {
                                return !in_array($worker['ID'], $unavailableWorkerIds);
                            });
                            ?>

                            <?php if (count($availableWorkers) > 0): ?>
                                <form method="post" class="assign-form">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['ID']); ?>">
                                    <select name="worker_id[]" multiple required>
                                        <option value="">-- Select Available <?php echo htmlspecialchars($requiredSpecialty); ?> Workers --</option>
                                        <?php foreach ($availableWorkers as $worker): ?>
                                            <option value="<?php echo htmlspecialchars($worker['ID']); ?>">
                                                <?php echo htmlspecialchars($worker['FullName']); ?> (ID: <?php echo htmlspecialchars($worker['ID']); ?>)
                                                - <?php echo htmlspecialchars($worker['Specialty']); ?>
                                                <?php echo htmlspecialchars($worker['City']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="assign_employer" class="assign-btn">Assign Workers</button>
                                </form>
                            <?php else: ?>
                                <p class="no-workers-message">No available <?php echo htmlspecialchars($requiredSpecialty); ?> workers for this date.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No orders found in the system.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>