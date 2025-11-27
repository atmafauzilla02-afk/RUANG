<?php
session_start();
include '../koneksi/koneksi.php';
if (!isset($_SESSION['id_pengguna'])) exit(header("Location: ../login.php"));

$nama    = trim($_POST['nama'] ?? '');
$nik     = trim($_POST['nik'] ?? '');
$alamat  = trim($_POST['alamat'] ?? '');
$no_telp = trim($_POST['no_telp'] ?? '');

if (empty($nama) || empty($nik) || empty($alamat) || empty($no_telp)) {
    echo "<script>alert('Semua kolom wajib diisi!'); history.back();</script>"; exit();
}

// Cek NIK sudah ada?
$cek = mysqli_prepare($koneksi, "SELECT id_pengguna FROM pengguna WHERE nik = ?");
mysqli_stmt_bind_param($cek, "s", $nik);
mysqli_stmt_execute($cek);
if (mysqli_stmt_fetch($cek)) {
    echo "<script>alert('NIK sudah terdaftar!'); history.back();</script>"; exit();
}

$stmt = mysqli_prepare($koneksi, "INSERT INTO pengguna (nama, nik, alamat, no_telp, role) VALUES (?, ?, ?, ?, 'warga')");
mysqli_stmt_bind_param($stmt, "ssss", $nama, $nik, $alamat, $no_telp);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Warga berhasil ditambahkan!'); window.location='../kelola_warga.php';</script>";
} else {
    echo "<script>alert('Gagal menambah warga!'); history.back();</script>";
}
?>