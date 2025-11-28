<?php
header('Content-Type: application/json');
require '../koneksi/koneksi.php';

$tahun = $_GET['tahun'] ?? '';
$bulan = $_GET['bulan'] ?? '';

$sql = "SELECT bulan_tahun, tahun, nama_file, path_file FROM laporan WHERE nama_file IS NOT NULL";

if ($tahun !== '') {
    $sql .= " AND tahun = " . (int)$tahun;
}
if ($bulan !== '') {
    $escaped = mysqli_real_escape_string($conn, $bulan);
    $sql .= " AND bulan_tahun LIKE '%$escaped%'";
}

$sql .= " ORDER BY tahun DESC, 
         FIELD(SUBSTRING_INDEX(bulan_tahun,' ',1),
               'Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember') DESC";

$result = mysqli_query($conn, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'bulan_tahun' => $row['bulan_tahun'],
        'tahun'       => $row['tahun'],
        'file'        => $row['nama_file'],
        'path'        => $row['path_file']
    ];
}

echo json_encode($data);
?>