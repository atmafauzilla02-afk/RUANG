<?php
session_start();
include '../koneksi/koneksi.php';

$id_warga = $_POST['id_warga'];
$jenis = strtolower(trim($_POST['jenis_kustom']));
$bulan = $_POST['bulan'];
$tahun = $_POST['tahun'];
$nominal = $_POST['nominal'];

$cek = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pembayaran FROM pembayaran WHERE id_warga='$id_warga' AND jenis_pembayaran='$jenis' AND bulan_pembayaran='$bulan' AND tahun_pembayaran='$tahun'"));
if($cek > 0){
    echo "<script>alert('Iuran ini sudah ada!'); window.history.back();</script>";
    exit;
}

mysqli_query($koneksi, "INSERT INTO pembayaran 
    (id_warga, jenis_pembayaran, bulan_pembayaran, tahun_pembayaran, nominal_pembayaran, status_pembayaran)
    VALUES ('$id_warga', '$jenis', '$bulan', '$tahun', '$nominal', 'belum')");

echo "<script>alert('Iuran kustom berhasil ditambahkan!'); window.location='../iuran.php';</script>";
?>