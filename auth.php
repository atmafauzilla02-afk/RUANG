<?php
session_start();

if (!isset($_SESSION['nik'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['role'])) {
    $allowedRole = $_GET['role'];
    
    if ($_SESSION['role'] != $allowedRole) {
        echo "<script>
                alert('Akses ditolak! Halaman khusus $allowedRole.');
                window.location='index.php';
              </script>";
        exit;
    }
}
?>
