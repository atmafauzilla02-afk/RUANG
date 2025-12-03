<?php
include '../koneksi/koneksi.php';

$id_warga = $_POST['id_warga'];
$jenis = $_POST['jenis'];
$bulan = $_POST['bulan'];
$tahun = $_POST['tahun'];
$nominal = $_POST['nominal'];

mysqli_query($koneksi,"INSERT INTO pembayaran 
(id_warga, jenis_pembayaran, bulan_pembayaran, tahun_pembayaran, nominal_pembayaran, status_pembayaran)
VALUES ('$id_warga','$jenis','$bulan','$tahun','$nominal','belum')");

header("Location: ../iuran.php");