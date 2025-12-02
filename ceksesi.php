<?php
session_start();
//echo 'test';
if (!isset($_SESSION['user'])) {
    // Jika session tidak ada, redirect ke login
   // header("Location: login.php");
    exit();
}

// Jika session ada, lanjutkan
echo "Selamat datang, " . $_SESSION['user'];
echo '<br>';
echo "level : " . $_SESSION['level'];
?>
