<?php
ob_clean();
header('Content-Type: application/json');

require_once '../koneksi/koneksi.php';

if (!$koneksi) {
    echo json_encode(['error' => 'Koneksi database gagal']);
    exit;
}

$tahun = isset($_GET['tahun']) ? trim($_GET['tahun']) : '';
$bulan = isset($_GET['bulan']) ? trim($_GET['bulan']) : '';

$sql = "SELECT 
            bulan_tahun, 
            tahun,
            nama_file AS file,
            path_file AS path
        FROM laporan";

$where = [];

if ($tahun !== '') {
    $where[] = "tahun = '" . mysqli_real_escape_string($koneksi, $tahun) . "'";
}
if ($bulan !== '') {
    $where[] = "bulan_tahun = '" . mysqli_real_escape_string($koneksi, $bulan) . "'";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY tahun DESC, bulan_tahun DESC";

$result = mysqli_query($koneksi, $sql);

if (!$result) {
    echo json_encode(['error' => 'Query error: ' . mysqli_error($koneksi)]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'bulan_tahun' => $row['bulan_tahun'],
        'file'        => $row['file'],
        'path'        => $row['path']
    ];
}

echo json_encode($data);
exit;
?>