<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'bendahara') {
    echo "<script>alert('Akses ditolak!'); window.location='../iuran.php';</script>";
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID pembayaran tidak valid!'); window.location='../iuran.php';</script>";
    exit;
}

$id_pembayaran = mysqli_real_escape_string($koneksi, $_GET['id']);

$cek = mysqli_query($koneksi, "SELECT id_pembayaran, status_pembayaran, bukti_pembayaran 
                               FROM pembayaran 
                               WHERE id_pembayaran = '$id_pembayaran'");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='../iuran.php';</script>";
    exit;
}

$data = mysqli_fetch_assoc($cek);

if ($data['status_pembayaran'] !== 'menunggu') {
    echo "<script>alert('Status sudah bukan Menunggu! Tidak bisa ditolak.'); window.location='../iuran.php';</script>";
    exit;
}

$update = mysqli_query($koneksi, "UPDATE pembayaran 
                                  SET status_pembayaran = 'belum',
                                      bukti_pembayaran = NULL,
                                      tanggal_pembayaran = NULL
                                  WHERE id_pembayaran = '$id_pembayaran'");

if ($update && !empty($data['bukti_pembayaran'])) {
    $file_path = "../uploads/" . $data['bukti_pembayaran'];
    if (file_exists($file_path)) {
        unlink($file_path);
}

if ($update) {
    echo "<script>
        alert('Pembayaran telah ditolak!\\nStatus dikembalikan ke BELUM BAYAR\\nBukti pembayaran dihapus.');
        window.location='../iuran.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menolak pembayaran!\\nError: " . mysqli_error($koneksi) . "');
        window.location='../iuran.php';
    </script>";
}
?>