<?php
require '../includes/config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Prepare and execute the statement to find the user
    $stmt = $pdo->prepare("SELECT ID, FirstName, LastName, Password FROM CleaningWorkers WHERE Email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $employer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user exists
    if ($employer) {
        if (password_verify($password, $employer['Password'])) {
            $_SESSION['employer_loggedin'] = true;
            $_SESSION['employer_id'] = $employer['ID'];
            // Store first and last name separately and combined
            $_SESSION['first_name'] = $employer['FirstName'];
            $_SESSION['last_name'] = $employer['LastName'];
            $_SESSION['full_name'] = $employer['FirstName'] . ' ' . $employer['LastName'];
            header('Location: ../employer/Requests.php');
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid email";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/image (5).png" >
    <title>Login </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #c9d6ff;
            background: linear-gradient(to right, #004080, #40E0D0);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            padding: 20px;
            width: 400px;
            max-width: 100%;
            text-align: center;
        }

        img.logo {
            width: 120px; /* Increased size */
            height: auto; /* Maintain aspect ratio */
            margin-bottom: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        label {
            margin-top: 10px;
            display: block;
        }

        input[type="email"],
        input[type="password"],
        input[type="submit"] {
            background-color: #eee;
            border: none;
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
        }

        input[type="submit"] {
            background-color: #40E0D0;
            color: #fff;
            padding: 10px 45px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 20px;
            cursor: pointer;
        }

        .error {
            color: red;
            text-align: center;
        }
        .link-container1 {
            margin-top: 30px;
            
        }
        .link-container2 {
            margin-top: -20px;
            
        }
      
        a {
            color:#004080 ; /* Bootstrap green */
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #40E0D0; /* Darker green on hover */
        }
        p {
            margin: 15px 0;
            font-size: 18px;
        }
    </style>
</head>
<body>

    <div class="container">
        <img src="../images/Artboard 11.png" alt="Logo" class="logo">
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="../employer/login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Login">
            <div class="link-container1">
                <a href="../employer/employerform.php">Don't have an account yet? Sign up now!</a>
                </div>
                <p>Or</p>
                <div class="link-container2">
                    <a href="../user/forgot_password.php">Forgot your password?</a>
                </div>
        </form>
    </div>

</body>
</html>