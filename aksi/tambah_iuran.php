<?php
session_start();
include '../koneksi/koneksi.php';

if (isset($_POST['simpan'])) {

    $id = $_POST['id_pembayaran'];
    $jenis = $_POST['jenis'];

    /* Upload Foto */
$bukti_pembayaran = null;

if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
    $folder = "uploads";
    $ext = pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('profile_') . '.' . $ext;
    $fullpath = $folder . $filename;

    if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $fullpath)) {
        $bukti_pembayaran = $filename;
    } else {
        $bukti_pembayaran = null;
    }
} else {
    $bukti_pembayaran = null;
}

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
