<?php
session_start();
include '../koneksi/koneksi.php';

if (isset($_POST['simpan'])) {

    $id = $_POST['id_pembayaran'];
    $jenis = $_POST['jenis'];

    // Upload Bukti
    $file = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];
    $folder = "../uploads/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $namaBaru = time() . "_" . $file;
    move_uploaded_file($tmp, $folder . $namaBaru);

    $query = mysqli_query($koneksi, "
        UPDATE pembayaran 
        SET 
            jenis_pembayaran='$jenis',
            bukti_pembayaran='$namaBaru',
            status_pembayaran='menunggu'
        WHERE id_pembayaran='$id'
    ");

    if ($query) {
        echo "<script>
            alert('Pembayaran berhasil diajukan! Menunggu konfirmasi.');
            window.location='../iuran.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menambahkan pembayaran!');
            window.location='../iuran.php';
        </script>";
    }
}
?>
