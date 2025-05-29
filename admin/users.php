<?php
require '../includes/config.php';
include '../admin/count.php';

session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}

$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

// Initialize search variables
$search = "";
$searchField = "all";

// Process search query if submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $searchField = isset($_GET['searchField']) ? $_GET['searchField'] : 'all';
}

try {
    // Base query
    $query = "
        SELECT c.ID, c.firstName, c.lastName, c.Email, c.PhoneNumber, COUNT(o.ID) AS OrderCount
        FROM clients c
        LEFT JOIN orders o ON c.ID = o.ClientID
    ";
    
    $params = [];
    
    // Add search conditions if search is provided
    if (!empty($search)) {
        $query .= " WHERE ";
        
        switch ($searchField) {
            case 'id':
                $query .= "c.ID = ?";
                $params[] = $search;
                break;
            case 'firstName':
                $query .= "c.firstName LIKE ?";
                $params[] = "%$search%";
                break;
            case 'lastName':
                $query .= "c.lastName LIKE ?";
                $params[] = "%$search%";
                break;
            default: // 'all' option searches both first and last name
                $query .= "(c.firstName LIKE ? OR c.lastName LIKE ? OR c.ID = ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
                $params[] = $search;
                break;
        }
    }
    
    // Complete the query with GROUP BY
    $query .= " GROUP BY c.ID";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client List</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png">
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

        td[colspan] {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 25px 15px;
        }

        /* Search form styles */
        .search-form {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
        }

        .search-form input[type="text"] {
            flex: 1;
            min-width: 200px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border 0.3s ease;
        }

        .search-form input[type="text"]:focus {
            border-color: #4ba3e2;
        }

        .search-form select {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
            outline: none;
            min-width: 150px;
        }

        .search-form button {
            background: linear-gradient(135deg, #4ba3e2, #1e5b9f);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-form button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .search-form button:active {
            transform: translateY(0);
        }

        .search-results-info {
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }

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
            
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-form input, 
            .search-form select, 
            .search-form button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            th, td {
                padding: 10px 8px;
                font-size: 12px;
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
        <p>users</p>
        <i class="fas fa-chart-bar"></i>
    </div>
    <div class="data-info">
        <div class="box">
            <i class="fas fa-user"></i>
            <div class="data">
                <p>Users</p>
                <img src="../images/icons8-users-64.png" style="width: 50px; height: 50px;">
                <div><span><?php echo htmlspecialchars($usersCount); ?></span></div>
            </div>
        </div>
    </div>

    <br>
    <h2>Clients List</h2>
    
    <!-- Search Form -->
    <form class="search-form" method="GET" action="">
        <input type="text" name="search" placeholder="Search clients..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="searchField">
            <option value="all" <?php echo $searchField === 'all' ? 'selected' : ''; ?>>All Fields</option>
            <option value="id" <?php echo $searchField === 'id' ? 'selected' : ''; ?>>ID</option>
            <option value="firstName" <?php echo $searchField === 'firstName' ? 'selected' : ''; ?>>First Name</option>
            <option value="lastName" <?php echo $searchField === 'lastName' ? 'selected' : ''; ?>>Last Name</option>
        </select>
        <button type="submit">Search</button>
    </form>
    
    <?php if (!empty($search)): ?>
    <div class="search-results-info">
        <?php echo count($clients); ?> result(s) found for "<?php echo htmlspecialchars($search); ?>"
    </div>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Number of Orders</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($clients)): ?>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($client['ID']); ?></td>
                        <td><?php echo htmlspecialchars($client['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($client['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($client['Email']); ?></td>
                        <td><?php echo htmlspecialchars($client['PhoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($client['OrderCount']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No clients found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>