<?php
include '../koneksi/koneksi.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID tidak ditemukan!'); window.location='../iuran.php';</script>";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($koneksi, "
    UPDATE pembayaran 
    SET status_pembayaran='lunas'
    WHERE id_pembayaran='$id'
");

if ($query) {
    echo "<script>
        alert('Pembayaran berhasil dikonfirmasi!');
        window.location='../iuran.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal mengkonfirmasi!');
        window.location='../iuran.php';
    </script>";
}
?>
