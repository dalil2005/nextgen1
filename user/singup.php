<?php
session_start();
require '../includes/config.php'; 
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['email'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if the email is already in use
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Clients WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            $error = "Email is already in use.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO Clients (FirstName, LastName, Email, PhoneNumber, Password) VALUES (:firstName, :lastName, :email, :phoneNumber, :password)");
            
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':password', $hashedPassword);
            
            if ($stmt->execute()) {
                // Get the new user's ID
                $userId = $pdo->lastInsertId();
                
                // Set session variables to log the user in
                $_SESSION['user_loggedin'] = true;
                $_SESSION['client_id'] = $userId;
                
                
               
                    
                    // Check if coming from confirmation.php
                   
                        header('Location: ../user/thank_you.php');
                       
                
            } else {
                $error = "Error in registration. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>NextGen Store | Sign Up</title>
    <link rel="icon" type="image/png" href="../images/image (4).png" >
    <style>
        .powered-by {
            margin-top: 10px;   
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .powered-by img {
            width: 100px;
            height: auto;
            object-fit: contain;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
        .link-container {
            margin: 10px 0;
        }
        a {
            color: #28a745; /* Bootstrap green */
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #218838; /* Darker green on hover */
        }
        p {
            margin: 15px 0;
            font-size: 18px;
        }

        /* New style for side-by-side fields */
        .name-fields {
            display: flex;
            gap: 10px;
        }
        
        .name-fields input {
            flex: 1;
        }
    </style>
</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Sign Up</h1>
                <?php if (!empty($error)) : ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                
                <div class="name-fields">
                    <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                    <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                </div>
                <input type="email" name="email" placeholder="Email" required>
                <input type="tel" name="phoneNumber" placeholder="Phone Number">
                
                <input type="password" name="password" placeholder="Password"
       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}"
       title="Must contain at least 8 characters, including uppercase, lowercase, number, and special character"
       required>

                <input type="password" name="confirmPassword" placeholder="Confirm Password"
       title="Re-enter the same password"
       required>

                <button type="submit">Sign Up</button>
                <a href="../user/login.php">Already have an account? Log in here!</a>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Create Your Account</h1>
                    <p>Join us today and enjoy all the features we offer!</p>
                    <p>You need to have an account to place an order. Thank you for your understanding!</p>
                    <div class="pb">
                        <p>powered by</p>
                    </div>
                    <div class="powered-by">
                        <img class="pim" src="../images/Artboard 1.png" alt="Powered by Logo">
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>