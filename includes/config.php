<?php

$host = 'localhost';
$dbname = 'nextgen';
$username = 'root';
$password = '';

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE Orders 
            SET Status = 'in_progress' 
            WHERE CleaningDate = CURDATE() 
            AND Status = 'pending'";
    
    // Execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
   
    die("Connection failed: " . $e->getMessage());
}
?>