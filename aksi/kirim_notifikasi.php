<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit;
}

$id_warga = mysqli_real_escape_string($koneksi, $_POST['id_warga']);
$judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
$isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
$jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
$bulan = mysqli_real_escape_string($koneksi, $_POST['bulan']);
$tahun = mysqli_real_escape_string($koneksi, $_POST['tahun']);

$cek_duplikat = mysqli_query($koneksi, "
    SELECT id FROM notifikasi_warga 
    WHERE id_warga = '$id_warga' 
      AND jenis_iuran = '$jenis'
      AND bulan_iuran = '$bulan'
      AND tahun_iuran = '$tahun'
      AND tanggal >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
");

if (mysqli_num_rows($cek_duplikat) > 0) {
    echo "<script>
        alert('⚠️ Notifikasi untuk Iuran $jenis - $bulan $tahun sudah dikirim dalam 24 jam terakhir!\\n\\nHarap tunggu sebelum mengirim lagi.');
        window.location.href = '../iuran.php';
    </script>";
    exit;
}

$tanggal = date('Y-m-d H:i:s');

$insert = mysqli_query($koneksi, "
    INSERT INTO notifikasi_warga 
    (id_warga, judul, isi, jenis_iuran, bulan_iuran, tahun_iuran, tanggal, dibaca) 
    VALUES 
    ('$id_warga', '$judul', '$isi', '$jenis', '$bulan', '$tahun', '$tanggal', 0)
");

if ($insert) {
    echo "<script>
        alert('✅ Notifikasi berhasil dikirim!');
        window.location.href = '../iuran.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Gagal mengirim notifikasi!');
        window.location.href = '../iuran.php';
    </script>";
}
?>