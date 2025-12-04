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

$cek = mysqli_query($koneksi, "SELECT id_pembayaran, status_pembayaran, nominal_pembayaran 
                               FROM pembayaran 
                               WHERE id_pembayaran = '$id_pembayaran'");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Data pembayaran tidak ditemukan!'); window.location='../iuran.php';</script>";
    exit;
}

$data = mysqli_fetch_assoc($cek);

if ($data['status_pembayaran'] !== 'menunggu') {
    echo "<script>alert('Status sudah bukan Menunggu! Tidak bisa dikonfirmasi ulang.'); window.location='../iuran.php';</script>";
    exit;
}

$update = mysqli_query($koneksi, "UPDATE pembayaran 
                                  SET status_pembayaran = 'lunas',
                                      tanggal_pembayaran = NOW()
                                  WHERE id_pembayaran = '$id_pembayaran'");

if ($update) {
    echo "<script>
        alert('Pembayaran berhasil dikonfirmasi!\\nStatus: LUNAS\\nNominal: Rp" . number_format($data['nominal_pembayaran'], 0, ',', '.') . "');
        window.location='../iuran.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal mengonfirmasi pembayaran!\\nError: " . mysqli_error($koneksi) . "');
        window.location='../iuran.php';
    </script>";
}
?>