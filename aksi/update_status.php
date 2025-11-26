<?php
include "../koneksi/koneksi.php";

$id = $_POST['id'];
$status = $_POST['status'];

$sql = "UPDATE pembayaran SET status='$status' WHERE id_pembayaran='$id'";

echo mysqli_query($koneksi, $sql) ? "success" : "error";
?>
