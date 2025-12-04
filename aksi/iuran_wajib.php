<?php
session_start();
include '../koneksi/koneksi.php';

$bulan = $_POST['bulan'];
$tahun = $_POST['tahun'];
$kas = $_POST['nominal_kas'];
$keamanan = $_POST['nominal_keamanan'];
$kebersihan = $_POST['nominal_kebersihan'];

$jenis = ['kas', 'keamanan', 'kebersihan'];
$nominal = [$kas, $keamanan, $kebersihan];

// Cek apakah salah satu sudah ada
foreach($jenis as $j){
    $cek = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pembayaran FROM pembayaran WHERE jenis_pembayaran='$j' AND bulan_pembayaran='$bulan' AND tahun_pembayaran='$tahun'"));
    if($cek > 0){
        echo "<script>alert('Iuran $j untuk $bulan $tahun sudah pernah dibuat!'); window.history.back();</script>";
        exit;
    }
}

// Ambil semua warga
$warga = mysqli_query($koneksi, "SELECT id_warga FROM warga");

$berhasil = 0;
while($w = mysqli_fetch_assoc($warga)){
    for($i=0; $i<3; $i++){
        mysqli_query($koneksi, "INSERT INTO pembayaran 
            (id_warga, jenis_pembayaran, bulan_pembayaran, tahun_pembayaran, nominal_pembayaran, status_pembayaran)
            VALUES ('{$w['id_warga']}', '{$jenis[$i]}', '$bulan', '$tahun', '{$nominal[$i]}', 'belum')");
        $berhasil++;
    }
}

echo "<script>alert('Berhasil membuat 3 iuran wajib untuk semua warga! Total: $berhasil data'); window.location='../iuran.php';</script>";
?>