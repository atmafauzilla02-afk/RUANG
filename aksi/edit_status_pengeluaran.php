<?php
session_start();
header('Content-Type: application/json');
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $aksi = $_POST['aksi'];

    $status = ($aksi === 'setujui') ? 'Disetujui' : 'Ditolak';

    $sql = "UPDATE pengeluaran SET status_persetujuan = ? WHERE id_pengeluaran = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update status']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>