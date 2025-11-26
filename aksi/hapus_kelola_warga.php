<?php

include '../koneksi/koneksi.php';

if(isset($_POST['id'])){

    $id_warga = $_POST['id'];

    // 1. Ambil id_pengguna
    $q = mysqli_query($koneksi, "SELECT id_pengguna FROM warga WHERE id_warga='$id_warga'");
    $data = mysqli_fetch_assoc($q);
    $id_pengguna = $data['id_pengguna'];

    // 2. Hapus data warga
    mysqli_query($koneksi, "DELETE FROM warga WHERE id_warga='$id_warga'");

    // 3. Hapus data pengguna
    mysqli_query($koneksi, "DELETE FROM pengguna WHERE id_pengguna='$id_pengguna'");

    header("Location: ../kelola_warga.php");
    exit;
}
?>
