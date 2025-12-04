<?php
header('Content-Type: application/json');
include '../koneksi/koneksi.php';

$tahun = $_GET['tahun'] ?? date('Y');
$bulan = $_GET['bulan'] ?? '';
$jenis = $_GET['jenis'] ?? '';

$where = "status_pembayaran IN ('belum','menunggu') AND tahun_pembayaran = '$tahun'";

if (!empty($bulan)) {
    $where .= " AND bulan_pembayaran = '$bulan'";
}
if (!empty($jenis)) {
    $where .= " AND jenis_pembayaran = '$jenis'";
}

$query = "SELECT COUNT(*) AS jumlah FROM pembayaran WHERE $where";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo json_encode(['jumlah' => 0, 'error' => mysqli_error($koneksi)]);
    exit;
}

$row = mysqli_fetch_assoc($result);
$jumlah = (int)$row['jumlah'];

echo json_encode(['jumlah' => $jumlah]);
exit;
?>