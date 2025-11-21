<?php

include '../koneksi/koneksi.php';

if(isset($_POST['id_warga'])){
    $id = $_POST['id_warga'];
    $nama = $_POST['nama_warga'];
    $nik = $_POST['nik'];
    $alamat = $_POST['alamat'];
    $no_telp = $_POST['no_telp'];

    $update = mysqli_query($koneksi, "UPDATE warga SET 
        nama_warga='$nama', 
        nik='$nik', 
        alamat='$alamat', 
        no_telp='$no_telp' 
        WHERE id_warga='$id'");

    if($update){
        header("Location: ../kelola_warga.php");
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}
?>
