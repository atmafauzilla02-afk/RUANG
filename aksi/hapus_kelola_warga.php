<?php
session_start();
include '../koneksi/koneksi.php';

<<<<<<< HEAD
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit();
=======
if(isset($_POST['id'])){

    $id_warga = $_POST['id'];

    // 1. Ambil id_pengguna
    $q = mysqli_query($koneksi, "SELECT id_pengguna FROM warga WHERE id_warga='$id_warga'");
    $data = mysqli_fetch_assoc($q);
    $id_pengguna = $data['id_pengguna'];

    // 2. Hapus data warga
    mysqli_query($koneksi, "DELETE FROM warga WHERE id_warga='$id_warga'");

    // 3. Hapus data pengguna
    mysqli_query($koneksi, "DELETE FROM pengguna WHERE id_pengguna='$id_pengguna'");

    header("Location: ../kelola_warga.php");
    exit;
>>>>>>> 4af3ff4e0308603c2eeb100a3d3a10bb3c9c49b4
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