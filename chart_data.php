<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "ruang");
if (!$conn) {
    exit(json_encode([
        'error' => 'Database connection failed',
        'pemasukan' => array_fill(0, 12, 0), 
        'pengeluaran' => array_fill(0, 12, 0)
    ]));
}

$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
if ($tahun < 2000 || $tahun > 2100) {
    $tahun = date('Y');
}

$pemasukan   = array_fill(0, 12, 0);
$pengeluaran = array_fill(0, 12, 0);

$bulan_map = [
    'Januari' => 0, 'Februari' => 1, 'Maret' => 2, 'April' => 3,
    'Mei' => 4, 'Juni' => 5, 'Juli' => 6, 'Agustus' => 7,
    'September' => 8, 'Oktober' => 9, 'November' => 10, 'Desember' => 11
];

$q1 = mysqli_prepare($conn, "SELECT bulan_pembayaran as bln, SUM(nominal_pembayaran) as total FROM pembayaran WHERE status_pembayaran='lunas' AND tahun_pembayaran=? GROUP BY bulan_pembayaran");

if (!$q1) {
    mysqli_close($conn);
    exit(json_encode([
        'error' => 'Pemasukan query preparation failed',
        'pemasukan' => array_fill(0, 12, 0), 
        'pengeluaran' => array_fill(0, 12, 0)
    ]));
}

mysqli_stmt_bind_param($q1, "i", $tahun);

if (!mysqli_stmt_execute($q1)) {
    mysqli_stmt_close($q1);
    mysqli_close($conn);
    exit(json_encode([
        'error' => 'Pemasukan query execution failed',
        'pemasukan' => array_fill(0, 12, 0), 
        'pengeluaran' => array_fill(0, 12, 0)
    ]));
}

$res1 = mysqli_stmt_get_result($q1);
while ($r = mysqli_fetch_assoc($res1)) {
    if (isset($bulan_map[$r['bln']])) {
        $pemasukan[$bulan_map[$r['bln']]] = (int)$r['total'];
    }
}
mysqli_stmt_close($q1);

$q2 = mysqli_prepare($conn, "SELECT (MONTH(tanggal_pengeluaran)-1) as idx, SUM(nominal_pengeluaran) as total FROM pengeluaran WHERE status_persetujuan='Disetujui' AND YEAR(tanggal_pengeluaran)=? GROUP BY MONTH(tanggal_pengeluaran)");

if (!$q2) {
    mysqli_close($conn);
    exit(json_encode([
        'error' => 'Pengeluaran query preparation failed',
        'pemasukan' => $pemasukan, 
        'pengeluaran' => array_fill(0, 12, 0)
    ]));
}

mysqli_stmt_bind_param($q2, "i", $tahun);

if (!mysqli_stmt_execute($q2)) {
    mysqli_stmt_close($q2);
    mysqli_close($conn);
    exit(json_encode([
        'error' => 'Pengeluaran query execution failed',
        'pemasukan' => $pemasukan, 
        'pengeluaran' => array_fill(0, 12, 0)
    ]));
}

$res2 = mysqli_stmt_get_result($q2);
while ($r = mysqli_fetch_assoc($res2)) {
    $idx = (int)$r['idx'];
    if ($idx >= 0 && $idx <= 11) {
        $pengeluaran[$idx] = (int)$r['total'];
    }
}
mysqli_stmt_close($q2);

mysqli_close($conn);

echo json_encode([
    'tahun' => $tahun,
    'pemasukan' => $pemasukan, 
    'pengeluaran' => $pengeluaran
]);
?>