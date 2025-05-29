<?php
require '../includes/config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $sex = $_POST['sex'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $specialty = $_POST['specialty'];
    $phoneNumber = $_POST['phoneNumber'];
    $city = $_POST['city'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo "Error: Passwords do not match.";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO CleaningWorkers (FirstName, LastName, Sex, Age, Email, Specialty, PhoneNumber, City, Password, Status) VALUES (:firstName, :lastName, :sex, :age, :email, :specialty, :phoneNumber, :city, :password, 'pending')");
    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':sex', $sex);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':specialty', $specialty);
    $stmt->bindParam(':phoneNumber', $phoneNumber);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        header("Location: ../employer/thanks.php");
        exit();
    } else {
        echo "Error: Unable to save the data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/image (5).png">
    <title>Employer Information Form</title>
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
            min-height: 100vh; /* ✅ Fixed scrolling issue */
            padding: 20px; /* ✅ Added padding for mobile spacing */
        }

        .container {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            padding: 20px;
            text-align: center;
        }

        img.logo {
            max-width: 100px;
            margin-bottom: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .form-column {
            flex: 1;
            margin: 10px;
            min-width: 220px;
        }

        label {
            margin-top: 10px;
            display: block;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="tel"],
        input[type="submit"],
        input[type="password"],
        select {
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
            font-size: 12px;
            padding: 10px 45px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 10px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }

        .error {
            color: red;
            text-align: center;
        }

        .name-container {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .name-container input {
            flex: 1;
        }
    </style>
</head>

<body>

    <div class="container">
        <img src="../images/Artboard 11.png" alt="Logo" class="logo">
        <form action="../employer/employerform.php" method="POST">
            <div class="form-row">
                <div class="form-column">
                    <label for="name">Name:</label>
                    <div class="name-container">
                        <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                        <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                    </div>

                    <label for="sex">Sex:</label>
                    <select id="sex" name="sex" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>

                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" min="18" required>

                    <label for="specialty">Specialty:</label>
                    <select id="specialty" name="specialty" required>
                        <option value="Residential Cleaning">Residential Cleaning</option>
                        <option value="Commercial Cleaning">Commercial Cleaning</option>
                    </select>

                    <label for="city">City:</label>
                    <select id="city" name="city" required>
                        <option value="" disabled selected>Select Wilaya</option>
                        <option value="Adrar">01 - Adrar</option>
                        <option value="Chlef">02 - Chlef</option>
                        <option value="Laghouat">03 - Laghouat</option>
                        <option value="Oum El Bouaghi">04 - Oum El Bouaghi</option>
                        <option value="Batna">05 - Batna</option>
                        <option value="Bejaia">06 - Bejaia</option>
                        <option value="Biskra">07 - Biskra</option>
                        <option value="Bechar">08 - Bechar</option>
                        <option value="Blida">09 - Blida</option>
                        <option value="Bouira">10 - Bouira</option>
                        <option value="Tamanrasset">11 - Tamanrasset</option>
                        <option value="Tebessa">12 - Tebessa</option>
                        <option value="Tlemcen">13 - Tlemcen</option>
                        <option value="Tiaret">14 - Tiaret</option>
                        <option value="Tizi Ouzou">15 - Tizi Ouzou</option>
                        <option value="Alger">16 - Alger</option>
                        <option value="Djelfa">17 - Djelfa</option>
                        <option value="Jijel">18 - Jijel</option>
                        <option value="Setif">19 - Setif</option>
                        <option value="Saida">20 - Saida</option>
                        <option value="Skikda">21 - Skikda</option>
                        <option value="Sidi Bel Abbes">22 - Sidi Bel Abbes</option>
                        <option value="Annaba">23 - Annaba</option>
                        <option value="Guelma">24 - Guelma</option>
                        <option value="Constantine">25 - Constantine</option>
                        <option value="Medea">26 - Medea</option>
                        <option value="Mostaganem">27 - Mostaganem</option>
                        <option value="MSila">28 - M'Sila</option>
                        <option value="Mascara">29 - Mascara</option>
                        <option value="Ouargla">30 - Ouargla</option>
                        <option value="Oran">31 - Oran</option>
                        <option value="El Bayadh">32 - El Bayadh</option>
                        <option value="Illizi">33 - Illizi</option>
                        <option value="Bordj Bou Arreridj">34 - Bordj Bou Arreridj</option>
                        <option value="Boumerdes">35 - Boumerdes</option>
                        <option value="El Tarf">36 - El Tarf</option>
                        <option value="Tindouf">37 - Tindouf</option>
                        <option value="Tissemsilt">38 - Tissemsilt</option>
                        <option value="El Oued">39 - El Oued</option>
                        <option value="Khenchela">40 - Khenchela</option>
                        <option value="Souk Ahras">41 - Souk Ahras</option>
                        <option value="Tipaza">42 - Tipaza</option>
                        <option value="Mila">43 - Mila</option>
                        <option value="Ain Defla">44 - Ain Defla</option>
                        <option value="Naama">45 - Naama</option>
                        <option value="Ain Temouchent">46 - Ain Temouchent</option>
                        <option value="Ghardaia">47 - Ghardaia</option>
                        <option value="Relizane">48 - Relizane</option>
                        <option value="Timimoun">49 - Timimoun</option>
                        <option value="Bordj Badji Mokhtar">50 - Bordj Badji Mokhtar</option>
                        <option value="Ouled Djellal">51 - Ouled Djellal</option>
                        <option value="Béni Abbès">52 - Béni Abbès</option>
                        <option value="In Salah">53 - In Salah</option>
                        <option value="In Guezzam">54 - In Guezzam</option>
                        <option value="Touggourt">55 - Touggourt</option>
                        <option value="Djanet">56 - Djanet</option>
                        <option value="El MGhair">57 - El M'Ghair</option>
                        <option value="El Meniaa">58 - El Meniaa</option>
                    </select>
                </div>

                <div class="form-column">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="phoneNumber">Phone Number:</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="0********" required>

                    <label for="password">Password:</label>
                    <input type="password" name="password" placeholder="Password"
                        required>

                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" name="confirmPassword" placeholder="Confirm Password"
                        title="Re-enter the same password"
                        required>
                </div>
            </div>
            <input type="submit" value="Submit">
        </form>
    </div>

</body>

</html>
