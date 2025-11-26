<?php
include "../koneksi/koneksi.php";

$id = $_POST['id'];
$file = $_FILES['bukti']['name'];
$tmp = $_FILES['bukti']['tmp_name'];

$path = "../uploads/" . $file;

if(move_uploaded_file($tmp, $path)){
    $sql = "UPDATE pembayaran SET bukti='$file', status='Menunggu' WHERE id_pembayaran='$id'";
    echo mysqli_query($koneksi, $sql) ? "success" : "error";
} else {
    echo "failed";
}
?>
