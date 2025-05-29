<?php
session_start();
require '../includes/config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM Clients WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        if (password_verify($password, $client['Password'])) {
            $_SESSION['user_loggedin'] = true;
            $_SESSION['client_id'] = $client['ID'];
           
                // Get the referring URL
              
                    header('Location: ../user/thank_you.php');
                    
                
            
            
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/login.css">
    <title>cleanora</title>

    <link rel="icon" type="image/png" href="../images/image (4).png">
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

        .link-container1 {
            margin-top: 30px;

        }

        .link-container2 {
            margin-top: -25px;

        }

        a {
            color: #28a745;
            /* Bootstrap green */
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        a:hover {
            color: #218838;
            /* Darker green on hover */
        }

        p {
            margin: 15px 0;
            font-size: 18px;
        }
    </style>
</head>

<body>

    <div class="container" id="container">

        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Client Login</h1>
                <?php if (isset($error)) : ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <span></span>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                
                <div class="link-container2">
                    <a href="../user/forgot_password.php">Forgot your password?</a>
                </div>
                <p>Are you an employer? <a href="../employer/login.php" class="green-box">Employers' Space</a></p>


            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">

                <div class="toggle-panel toggle-right">
                    <h1>Welcome Back!</h1>
                    <p>Please enter your credentials to continue.</p>
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