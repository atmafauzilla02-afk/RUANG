<?php
header('Content-Type: application/json');

require_once '../koneksi/koneksi.php';

$tahun = $_GET['tahun'] ?? '';
$bulan = $_GET['bulan'] ?? '';

$tahun = $tahun === '' ? '' : (int)$tahun;
$bulan = $bulan === '' ? '' : trim($bulan);

try {
    $sql = "SELECT 
                bulan_tahun,
                nama_file AS file,
                path_file AS path
            FROM laporan 
            WHERE 1=1";

    $params = [];
    $types = '';

    if ($tahun !== '') {
        $sql .= " AND tahun = ?";
        $params[] = $tahun;
        $types .= 'i';
    }
    if ($bulan !== '') {
        $sql .= " AND bulan_nama = ?";
        $params[] = $bulan;
        $types .= 's';
    }

    $sql .= " ORDER BY tahun DESC, bulan_angka DESC";

    $stmt = $koneksi->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        if (file_exists($row['path'])) {
            $data[] = [
                'bulan_tahun' => $row['bulan_tahun'],
                'file'        => $row['file'],
                'path'        => $row['path']
            ];
        }
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([]);
}
?>