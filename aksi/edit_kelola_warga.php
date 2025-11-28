<?php

include '../koneksi/koneksi.php';

if(isset($_POST['id_warga'])) {

    $id_warga = $_POST['id_warga'];
    $nama     = $_POST['nama_warga'];
    $nik      = $_POST['nik'];
    $alamat   = $_POST['alamat'];
    $no_telp  = $_POST['no_telp'];

    // 1. Ambil id_pengguna berdasarkan id_warga
    $q = mysqli_query($koneksi, "SELECT id_pengguna FROM warga WHERE id_warga='$id_warga'");
    $data = mysqli_fetch_assoc($q);
    $id_pengguna = $data['id_pengguna'];

    // 2. Update tabel pengguna
    $update = mysqli_query($koneksi, "
        UPDATE pengguna SET 
            nama='$nama',
            nik='$nik',
            alamat='$alamat',
            no_telp='$no_telp'
        WHERE id_pengguna='$id_pengguna'
    ");

    if($update){
        header("Location: ../kelola_warga.php");
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}
?>