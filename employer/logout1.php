<?php
session_start();
unset($_SESSION['employer_loggedin']);
session_destroy();
header("Location: ../employer/login.php");
exit();
?>