<?php

require '../includes/config.php';


try {
    $stmt = $pdo->query("SELECT COUNT(*) AS workerCount FROM CleaningWorkers WHERE Status = 'pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $workerCountp = $result['workerCount'];
} catch (PDOException $e) {
    $workerCountp = "Error";
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS user_count FROM clients");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $usersCount = $result['user_count'];
} catch (PDOException $e) {
    $usersCount = "Error";
}


try {
    $stmt = $pdo->query("SELECT COUNT(*) AS service_count FROM Services");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $servicecount = $result['service_count']; 
} catch (PDOException $e) {
    $servicecount = "Error"; 
}


try {
    $stmt = $pdo->query("SELECT COUNT(*) AS accepted_worker_count FROM CleaningWorkers WHERE Status = 'accepted'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $workerCount = $result['accepted_worker_count'];
} catch (PDOException $e) {
    $workerCount = "Error";
}
try {
    // Calculate total revenue from completed payments
    $stmt = $pdo->query("SELECT SUM(Amount) AS total_revenue FROM Payments WHERE PaymentStatus = 'completed'");
    $total_revenue = $stmt->fetchColumn() ?? 0; // Fetch total revenue or default to 0
} catch (PDOException $e) {
    $total_revenue = "Error";
}
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS order_count FROM Orders");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $orderCount = $result['order_count'];
} catch (PDOException $e) {
    $orderCount = "Error";
}
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS admin_count FROM Admin");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $adminsCount = $result['admin_count'];
} catch (PDOException $e) {
    $adminsCount = "Error";
}
try {
    $stmt = $pdo->query("SELECT SUM(Rating) AS total_rating, COUNT(Rating) AS rated_orders FROM Orders WHERE Rating IS NOT NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalRating = $result['total_rating'];
    $ratedOrders = $result['rated_orders'];

    $averageRating = $ratedOrders > 0 ? $totalRating / $ratedOrders : null;
} catch (PDOException $e) {
    $averageRating = "Error: " . $e->getMessage();
    $ratedOrders = 0; // Set to 0 in case of an error
}

// Now use the HTML code provided above to display the results.
?>