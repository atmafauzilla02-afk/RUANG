<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../koneksi/koneksi.php';

// Check database connection
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

$id_pembayaran = mysqli_real_escape_string($koneksi, $_GET['id']);

$cek = mysqli_query($koneksi, "SELECT id_pembayaran, status_pembayaran, nominal_pembayaran 
                               FROM pembayaran 
                               WHERE id_pembayaran = '$id_pembayaran'");

if (!$cek) {
    die("Query error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Data pembayaran tidak ditemukan!'); window.location='../iuran.php';</script>";
    exit;
}

$data = mysqli_fetch_assoc($cek);

if ($data['status_pembayaran'] !== 'menunggu') {
    echo "<script>alert('Status sudah bukan Menunggu! Tidak bisa dikonfirmasi ulang.'); window.location='../iuran.php';</script>";
    exit;
}

// Get current year and month
$tahun_pembayaran = date('Y'); // e.g., 2025
$bulan_pembayaran = strtolower(date('F')); // e.g., 'december'

$update = mysqli_query($koneksi, "UPDATE pembayaran 
                                  SET status_pembayaran = 'lunas',
                                      tahun_pembayaran = '$tahun_pembayaran',
                                      bulan_pembayaran = '$bulan_pembayaran'
                                  WHERE id_pembayaran = '$id_pembayaran'");

if ($update) {
    $nominal = number_format($data['nominal_pembayaran'], 0, ',', '.');
    echo "<script>
        alert('Pembayaran berhasil dikonfirmasi!\\nStatus: LUNAS\\nNominal: Rp{$nominal}');
        window.location='../iuran.php';
    </script>";
} else {
    $error = addslashes(mysqli_error($koneksi));
    echo "<script>
        alert('Gagal mengonfirmasi pembayaran!\\nError: {$error}');
        window.location='../iuran.php';
    </script>";
}
?>