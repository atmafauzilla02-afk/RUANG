<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo "<script>alert('Akses ditolak!'); window.location='../kelola_warga.php';</script>";
    exit();
}

$id = (int)$_POST['id'];

// HAPUS PERMANEN DARI DATABASE
$stmt = mysqli_prepare($koneksi, "DELETE FROM pengguna WHERE id_pengguna = ? AND role = 'warga'");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Data warga berhasil DIHAPUS PERMANEN!');
            window.location = '../kelola_warga.php';
          </script>";
} else {
    echo "<script>
            alert('Gagal menghapus data!');
            window.location = '../kelola_warga.php';
          </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>