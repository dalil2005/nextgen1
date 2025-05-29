<?php
require '../includes/config.php';
include '../admin/count.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: ../admin/index.php');
    exit;
}
$successMessage = '';

// Handle form submission for updating prices
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update PrivateService prices
    if (isset($_POST['update_private'])) {
        $emptyPrice = floatval($_POST['empty_room_price']);
        $furnishedPrice = floatval($_POST['furnished_room_price']);
        $newPrice = floatval($_POST['old_room_price']);
        
        try {
            // Update Empty room price
            $updatePrivateEmpty = $pdo->prepare("UPDATE PrivateService SET PricePerRoom = ? WHERE RoomType = 'Empty'");
            $updatePrivateEmpty->execute([$emptyPrice]);
            
            // Update Furnished room price
            $updatePrivateFurnished = $pdo->prepare("UPDATE PrivateService SET PricePerRoom = ? WHERE RoomType = 'Furnished'");
            $updatePrivateFurnished->execute([$furnishedPrice]);
            
            // Update New room price
            $updatePrivateNew = $pdo->prepare("UPDATE PrivateService SET PricePerRoom = ? WHERE RoomType = 'old'");
            $updatePrivateNew->execute([$newPrice]);
            
            $successMessage = 'Private service prices updated successfully!';
        } catch (PDOException $e) {
            $errorMessage = 'Error updating private service prices: ' . $e->getMessage();
        }
    }
    
    // Update ProfessionalService prices
    if (isset($_POST['update_professional'])) {
        $emptyBuildingPrice = floatval($_POST['empty_building_price']);
        $furnishedBuildingPrice = floatval($_POST['furnished_building_price']);
        $newBuildingPrice = floatval($_POST['old_building_price']);
        
        try {
            // Update Empty building price
            $updateProfessionalEmpty = $pdo->prepare("UPDATE ProfessionalService SET PricePer50m2 = ? WHERE BuildingType = 'Empty'");
            $updateProfessionalEmpty->execute([$emptyBuildingPrice]);
            
            // Update Furnished building price
            $updateProfessionalFurnished = $pdo->prepare("UPDATE ProfessionalService SET PricePer50m2 = ? WHERE BuildingType = 'Furnished'");
            $updateProfessionalFurnished->execute([$furnishedBuildingPrice]);
            
            // Update New building price
            $updateProfessionalNew = $pdo->prepare("UPDATE ProfessionalService SET PricePer50m2 = ? WHERE BuildingType = 'old'");
            $updateProfessionalNew->execute([$newBuildingPrice]);
            
            $successMessage = 'Professional service prices updated successfully!';
        } catch (PDOException $e) {
            $errorMessage = 'Error updating professional service prices: ' . $e->getMessage();
        }
    }
}

// Fetch current prices for PrivateService
try {
    $privateQuery = $pdo->query("SELECT RoomType, PricePerRoom FROM PrivateService");
    $privateServices = [];
    while ($row = $privateQuery->fetch(PDO::FETCH_ASSOC)) {
        $privateServices[$row['RoomType']] = $row['PricePerRoom'];
    }
} catch (PDOException $e) {
    $errorMessage = 'Error fetching private service prices: ' . $e->getMessage();
}

// Fetch current prices for ProfessionalService
try {
    $professionalQuery = $pdo->query("SELECT BuildingType, PricePer50m2 FROM ProfessionalService");
    $professionalServices = [];
    while ($row = $professionalQuery->fetch(PDO::FETCH_ASSOC)) {
        $professionalServices[$row['BuildingType']] = $row['PricePer50m2'];
    }
} catch (PDOException $e) {
    $errorMessage = 'Error fetching professional service prices: ' . $e->getMessage();
}

$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Update Prices</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png" >
    <style>
        .price-form-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .price-form {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            width: 48%;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .price-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, #4ba3e2, #1e5b9f);
        }

        .price-form h2 {
            text-align: center;
            color: #1e5b9f;
            font-size: 24px;
            margin-bottom: 25px;
            font-weight: 600;
            border-bottom: none;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #4ba3e2;
            box-shadow: 0 0 8px rgba(75, 163, 226, 0.3);
        }

        .submit-btn {
            background: linear-gradient(135deg, #4ba3e2, #1e5b9f);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(30, 91, 159, 0.4);
        }

        .submit-btn:active {
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

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            border-left: 4px solid #dc3545;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .price-form-container {
                flex-direction: column;
            }
            
            .price-form {
                width: 100%;
                padding: 20px;
                margin: 15px 0;
            }
            
            .price-form h2 {
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
            <p>Update Service Prices</p>
            <i class="fas fa-dollar-sign"></i>
        </div>
        
        <?php if (!empty($successMessage)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>
        
        <div class="price-form-container">
            <!-- Private Service Prices Form -->
            <div class="price-form">
                <h2>Private Service Prices</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="empty_room_price">Empty Room Price (DZD)</label>
                        <input type="number" id="empty_room_price" name="empty_room_price" step="0.01" value="<?php echo isset($privateServices['Empty']) ? htmlspecialchars($privateServices['Empty']) : '0.00'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="furnished_room_price">Furnished Room Price (DZD)</label>
                        <input type="number" id="furnished_room_price" name="furnished_room_price" step="0.01" value="<?php echo isset($privateServices['Furnished']) ? htmlspecialchars($privateServices['Furnished']) : '0.00'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="old_room_price">old Room Price (DZD)</label>
                        <input type="number" id="old_room_price" name="old_room_price" step="0.01" value="<?php echo isset($privateServices['old']) ? htmlspecialchars($privateServices['old']) : '0.00'; ?>" required>
                    </div>
                    <button type="submit" name="update_private" class="submit-btn">Update Private Service Prices</button>
                </form>
            </div>
            
            <!-- Professional Service Prices Form -->
            <div class="price-form">
                <h2>Professional Service Prices</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="empty_building_price">Empty Building Price per 50m² (DZD)</label>
                        <input type="number" id="empty_building_price" name="empty_building_price" step="0.01" value="<?php echo isset($professionalServices['Empty']) ? htmlspecialchars($professionalServices['Empty']) : '0.00'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="furnished_building_price">Furnished Building Price per 50m² (DZD)</label>
                        <input type="number" id="furnished_building_price" name="furnished_building_price" step="0.01" value="<?php echo isset($professionalServices['Furnished']) ? htmlspecialchars($professionalServices['Furnished']) : '0.00'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="old_building_price">old Building Price per 50m² (DZD)</label>
                        <input type="number" id="old_building_price" name="old_building_price" step="0.01" value="<?php echo isset($professionalServices['old']) ? htmlspecialchars($professionalServices['old']) : '0.00'; ?>" required>
                    </div>
                    <button type="submit" name="update_professional" class="submit-btn">Update Professional Service Prices</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>