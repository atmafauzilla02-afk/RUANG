<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../koneksi/koneksi.php';

if (!$koneksi) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'bendahara') {
    echo "<script>alert('Akses ditolak!'); window.location='../iuran.php';</script>";
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID pembayaran tidak valid!'); window.location='../iuran.php';</script>";
    exit;
}

$id_pembayaran = (int)$_GET['id'];

$cek = mysqli_query($koneksi, "SELECT id_pembayaran, status_pembayaran, nominal_pembayaran, jenis_pembayaran, bulan_pembayaran, tahun_pembayaran 
                               FROM pembayaran 
                               WHERE id_pembayaran = '$id_pembayaran'");

if (!$cek || mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Data pembayaran tidak ditemukan!'); window.location='../iuran.php';</script>";
    exit;
}

$data = mysqli_fetch_assoc($cek);

if ($data['status_pembayaran'] !== 'menunggu') {
    echo "<script>alert('Status sudah bukan Menunggu! Tidak bisa dikonfirmasi ulang.'); window.location='../iuran.php';</script>";
    exit;
}

$update = mysqli_query($koneksi, "UPDATE pembayaran 
                                  SET status_pembayaran = 'lunas'
                                  WHERE id_pembayaran = '$id_pembayaran'");

if ($update) {
    $nominal = number_format($data['nominal_pembayaran'], 0, ',', '.');
    echo "<script>
        alert('Pembayaran berhasil dikonfirmasi!\\nIuran {$data['jenis_pembayaran']} - {$data['bulan_pembayaran']} {$data['tahun_pembayaran']}\\nStatus: LUNAS\\nNominal: Rp{$nominal}');
        window.location='../iuran.php';
    </script>";
} else {
    $error = mysqli_error($koneksi);
    echo "<script>
        alert('Gagal mengonfirmasi pembayaran!\\nError: {$error}');
        window.location='../iuran.php';
    </script>";
}
?>