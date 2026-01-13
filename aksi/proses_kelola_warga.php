<?php
session_start();
include '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id       = (int)$_POST['id_pengguna'];
    $nama     = trim($_POST['nama']);
    $nik      = trim($_POST['nik']);
    $alamat   = trim($_POST['alamat']);
    $no_telp  = preg_replace('/[^0-9]/', '', $_POST['no_telp']);

    if (empty($nama) || empty($nik) || strlen($nik) != 16) {
        header("Location: ../kelola_warga.php?edit=$id&error=Data tidak valid");
        exit();
    }

    $stmt = mysqli_prepare($koneksi, "UPDATE pengguna SET nama=?, nik=?, alamat=?, no_telp=? WHERE id_pengguna=? AND role='warga'");
    mysqli_stmt_bind_param($stmt, "ssssi", $nama, $nik, $alamat, $no_telp, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../kelola_warga.php?success=1");
    } else {
        header("Location: ../kelola_warga.php?edit=$id&error=Gagal update");
    }
    exit();
}
?>