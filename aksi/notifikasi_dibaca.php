<?php
session_start();
include '../koneksi/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_warga'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$id_warga = mysqli_real_escape_string($koneksi, $_SESSION['id_warga']);

$update = mysqli_query($koneksi, "
    UPDATE notifikasi_warga 
    SET dibaca = 1 
    WHERE id_warga = '$id_warga' AND dibaca = 0
");

echo json_encode([
    'success' => $update ? true : false,
    'affected_rows' => mysqli_affected_rows($koneksi)
]);
?>