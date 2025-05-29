<?php
session_start();
require '../includes/config.php'; 
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM Admin WHERE Username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        if (password_verify($password, $admin['Password'])) {
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['username'] = $admin['Username'];
            header('Location: admin.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../css/logincss.css">
    <title>LOGIN ADMIN</title>
    <link rel="icon" type="image/png" href="../images/image (5).png" >
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
    </style>
</head>

<body>

    <div class="container" id="container">
        
        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Login</h1>
                <?php if (isset($error)) : ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <span></span>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Admin!</h1>
                    <p>Welcome back! Please enter your credentials to continue.</p>
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