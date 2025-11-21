<?php

include '../koneksi/koneksi.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $delete = mysqli_query($koneksi, "DELETE FROM warga WHERE id_warga='$id'");
    header("Location: ../kelola_warga.php");
    exit;
}
?>
