<?php
session_start();
include "../koneksi/koneksi.php";

$id_bendahara = $_SESSION['id_bendahara'];

// Ambil data POST
$judul = isset($_POST['judul']) ? trim($_POST['judul']) : '';
$keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
$nominal = isset($_POST['nominal']) ? trim($_POST['nominal']) : '';
$kategori = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';

// Validasi input
if ($judul === '' || $keterangan === '' || $nominal === '' || $kategori === '') {
    echo "<script>alert('Input tidak lengkap!'); window.history.back();</script>";
    exit;
}

// Query insert pengeluaran
$query = "INSERT INTO pengeluaran 
    (id_bendahara, nama_pengeluaran, jenis_pengeluaran, nominal_pengeluaran, tanggal_pengeluaran, status_persetujuan) 
    VALUES ('$id_bendahara', '$judul', '$kategori', '$nominal', NOW(), 'menunggu')";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Pengajuan berhasil dikirim!'); window.location='../dashboardBendahara.php';</script>";
} else {
    echo "<script>alert('Gagal mengirim pengajuan: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
}
?>
