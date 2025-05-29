<?php
require '../includes/config.php';
session_start();

if (!isset($_SESSION['employer_loggedin']) || $_SESSION['employer_loggedin'] !== true) {
    header('Location: ../employer/login.php');
    exit;
}

// Fetch employer's information from the database
$employerId = $_SESSION['employer_id']; // Assuming user ID is stored in the session

// Using PDO consistent with the login page
$query = "SELECT ID, FirstName, LastName, Sex, Age, Email, PhoneNumber, Specialty FROM CleaningWorkers WHERE ID = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $employerId, PDO::PARAM_INT);
$stmt->execute();
$employer = $stmt->fetch(PDO::FETCH_ASSOC);

$successMessage = '';
$errorMessage = '';

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $sex = $_POST['sex'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $specialty = $_POST['specialty'];

    // Update profile information
    $updateQuery = "UPDATE CleaningWorkers SET FirstName = :firstName, LastName = :lastName, Age = :age, Email = :email, PhoneNumber = :phoneNumber, Specialty = :specialty WHERE ID = :id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindParam(':firstName', $firstName);
    $updateStmt->bindParam(':lastName', $lastName);
    $updateStmt->bindParam(':age', $age, PDO::PARAM_INT);
    $updateStmt->bindParam(':email', $email);
    $updateStmt->bindParam(':phoneNumber', $phoneNumber);
    $updateStmt->bindParam(':specialty', $specialty);
    $updateStmt->bindParam(':id', $employerId, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        $successMessage = "Profile updated successfully!";

        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $passwordQuery = "UPDATE CleaningWorkers SET Password = :password WHERE ID = :id";
            $passwordStmt = $pdo->prepare($passwordQuery);
            $passwordStmt->bindParam(':password', $password);
            $passwordStmt->bindParam(':id', $employerId, PDO::PARAM_INT);
            $passwordStmt->execute();
        }

        // Refresh employer data
        $stmt->execute();
        $employer = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $errorMessage = "Error updating profile: " . implode(", ", $updateStmt->errorInfo());
    }
}

// Combine first and last name for display in the sidebar
$employerName = '';
if (isset($employer['FirstName']) && isset($employer['LastName'])) {
    $employerName = $employer['FirstName'] . ' ' . $employer['LastName'];
} else {
    $employerName = 'Employer';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Dashboard</title>
    <link rel="stylesheet" href="../css/cssdach.css">
    <link rel="icon" type="image/png" href="../images/image (5).png">
    <style>
        .profile-update {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-update h2 {
            color: #004080;
            /* Deep Blue */
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            /* Light Border */
            border-radius: 5px;
        }

        button[type="submit"] {
            background: #004080;
            /* Deep Blue */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button[type="submit"]:hover {
            background: #003366;
            /* Darker Blue */
        }

        .success-message {
            color: #27ae60;
            /* Green */
            margin-bottom: 15px;
        }

        .error-message {
            color: #e74c3c;
            /* Red */
            margin-bottom: 15px;
        }
        
        /* New style for name fields layout */
        .name-fields {
            display: flex;
            gap: 15px;
        }
        
        .name-fields .form-group {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="menu">
        <ul>
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
            <li>
                <a href="../employer/Requests.php">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-notifications-64.png" alt="Cleaning Requests" style="width: 50px; height: 50px;">
                    <p>Cleaning Requests</p>
                </a>
            </li>
            <li>
                <a href="../employer/Notes.php">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-notes-64.png" alt="Management Notes" style="width: 50px; height: 50px;">
                    <p>Management Notes</p>
                </a>
            </li>
            <li>
                <a href="../employer/index.php" class="active">
                    <i class="fas fa-sticky-note"></i>
                    <img src="../images/icons8-profile-64.png" alt="Employee Information" style="width: 50px; height: 50px;">
                    <p>Employee Information</p>
                </a>
            </li>
            <li class="fas ">
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
            <p>your information</p>
            <i class="fas fa-chart-bar"></i>
        </div>

        <div class="profile-update">
            <h2>Update Profile Information</h2>

            <?php if (!empty($successMessage)): ?>
                <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="name-fields">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($employer['FirstName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($employer['LastName']); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="sex">Sex</label>
                    <select id="sex" name="sex" required>
                        <option value="Male" <?php echo (isset($employer['Sex']) && $employer['Sex'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (isset($employer['Sex']) && $employer['Sex'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($employer['Age']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employer['Email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phoneNumber">Phone Number</label>
                    <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($employer['PhoneNumber']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="specialty">Specialty</label>
                    <select id="specialty" name="specialty" required>
                        <option value="">-- Select Specialty --</option>
                        <option value="Residential Cleaning" <?php echo (isset($employer['Specialty']) && $employer['Specialty'] == 'Residential Cleaning') ? 'selected' : ''; ?>>Residential Cleaning</option>
                        <option value="Commercial Cleaning" <?php echo (isset($employer['Specialty']) && $employer['Specialty'] == 'Commercial Cleaning') ? 'selected' : ''; ?>>Commercial Cleaning</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current)</label>
                    <input type="password" id="password" name="password" placeholder="Enter new password">
                </div>
                
                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>

</html>