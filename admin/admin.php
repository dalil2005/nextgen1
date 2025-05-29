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
    /* Client Reviews Section - Improved Styling */
    .client-reviews {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 12px;
        margin-top: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 22px;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e6e6e6;
    }

    .reviews-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .review-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    .review-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background-color: #f0f8ff;
        border-bottom: 1px solid #e6e6e6;
    }

    .client-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #4a90e2;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
    }

    .client-details h3 {
        margin: 0;
        font-size: 16px;
        color: #333;
        font-weight: 600;
    }

    .rating-stars {
        display: flex;
        align-items: center;
        margin-top: 5px;
    }

    .star {
        color: #d1d1d1;
        font-size: 18px;
    }

    .star.filled {
        color: #ffb700;
    }

    .rating-value {
        margin-left: 8px;
        font-size: 14px;
        color: #666;
    }

    .review-content {
        padding: 15px;
    }

    .comment-text {
        font-size: 14px;
        line-height: 1.6;
        color: #555;
        margin-bottom: 15px;
    }

    /* New order image gallery styles */
    .order-image-gallery {
        position: relative;
        margin-top: 15px;
        border-radius: 8px;
        overflow: hidden;
        background-color: #f7f7f7;
        padding: 12px;
        border: 1px solid #eaeaea;
    }

    .order-id-label {
        display: inline-block;
        background-color: #4a90e2;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .single-image-container img {
        width: 100%;
        border-radius: 6px;
        max-height: 200px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .single-image-container img:hover {
        transform: scale(1.03);
    }

    /* For multiple images */
    .image-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .image-item {
        position: relative;
        height: 120px;
        border-radius: 6px;
        overflow: hidden;
    }

    .image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .image-item:hover img {
        transform: scale(1.1);
    }

    /* Style for more than 4 images */
    .image-grid.many-images {
        grid-template-columns: repeat(3, 1fr);
    }

    .image-grid.many-images .image-item {
        height: 100px;
    }

    .no-reviews {
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

    .no-reviews-icon {
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
    <title>Admin</title>
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
        <div class="client-reviews">
            <?php
            try {



                $stmt = $pdo->prepare("SELECT o.ID as OrderID, o.Comments, o.Rating, c.FirstName,c.LastName
              FROM Orders o 
              JOIN Clients c ON o.ClientID = c.ID
              WHERE o.Comments IS NOT NULL AND o.Comments != ''
              ORDER BY o.OrderDate DESC
              ");
                $stmt->execute();

                echo '<h2 class="section-title">Recent Client Reviews</h2>';

                if ($stmt->rowCount() > 0) {
                    echo '<div class="reviews-container">';
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $orderID = $row['OrderID'];
                        $FirstName = htmlspecialchars($row['FirstName']);
                        $LastName = htmlspecialchars($row['LastName']);
                        $comments = htmlspecialchars($row['Comments']);
                        $rating = $row['Rating'];

                        // Get all images for this specific order
                        $imageQuery = "SELECT ImageURL FROM OrderImages WHERE OrderID = :orderID";
                        $imageStmt = $pdo->prepare($imageQuery);
                        $imageStmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
                        $imageStmt->execute();
                        $orderImages = $imageStmt->fetchAll(PDO::FETCH_COLUMN);

                        // Display the review with improved design
                        echo '<div class="review-card">';

                        echo '<div class="review-header">';
                        echo '<div class="client-avatar">' . substr($FirstName, 0, 1) . '</div>';
                        echo '<div class="client-details">';
                        
                        echo '<h3>' . $FirstName . ' ' . $LastName . '</h3>';

                        echo '<span class="order-id-label">Order #' . $orderID . '</span>';

                        // Display star rating with improved visual
                        echo '<div class="rating-stars">';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $rating) {
                                echo '<span class="star filled">⭐</span>';
                            } else {
                                echo '<span class="star">☆</span>';
                            }
                        }
                        echo ' <span class="rating-value">' . $rating . '/5</span>';
                        echo '</div>'; // End rating
                        echo '</div>'; // End client-details
                        echo '</div>'; // End review-header

                        echo '<div class="review-content">';
                        echo '<p class="comment-text">' . $comments . '</p>';

                        // Display all order images together
                        if (!empty($orderImages)) {
                            echo '<div class="order-image-gallery">';


                            if (count($orderImages) == 1) {
                                // Single image display
                                echo '<div class="single-image-container">';
                                echo '<img src="../' . htmlspecialchars($orderImages[0]) . '" alt="Order Image">';
                                echo '</div>';
                            } else {
                                // Multiple images gallery
                                echo '<div class="image-grid">';
                                foreach ($orderImages as $imageURL) {
                                    echo '<div class="image-item">';
                                    echo '<img src="../' . htmlspecialchars($imageURL) . '" alt="Order Image">';
                                    echo '</div>';
                                }
                                echo '</div>'; // End image-grid
                            }

                            echo '</div>'; // End order-image-gallery
                        }

                        echo '</div>'; // End review-content
                        echo '</div>'; // End review-card
                    }
                    echo '</div>'; // End reviews-container
                } else {
                    echo '<div class="no-reviews">';
                    echo '<img src="../images/icons8-no-chat-64.png" alt="No Reviews" class="no-reviews-icon">';
                    echo '<p>No client reviews available yet</p>';
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