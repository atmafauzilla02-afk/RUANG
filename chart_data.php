<?php
header('Content-Type: application/json');
$conn = mysqli_connect("localhost", "root", "", "ruang");
if (!$conn) exit(json_encode(['pemasukan'=>array_fill(0,12,0), 'pengeluaran'=>array_fill(0,12,0)]));

$tahun = isset($_GET['tahun']) ? max(2023, min(2025, (int)$_GET['tahun'])) : date('Y');

$pemasukan   = array_fill(0, 12, 0);
$pengeluaran = array_fill(0, 12, 0);

$bulan_map = ['Januari'=>0,'Februari'=>1,'Maret'=>2,'April'=>3,'Mei'=>4,'Juni'=>5,'Juli'=>6,'Agustus'=>7,'September'=>8,'Oktober'=>9,'November'=>10,'Desember'=>11];

// Pemasukan
$q1 = mysqli_prepare($conn, "SELECT (bulan_pembayaran) as bln, SUM(nominal_pembayaran) as total FROM pembayaran WHERE status_pembayaran='lunas' AND tahun_pembayaran=? GROUP BY bulan_pembayaran");
mysqli_stmt_bind_param($q1, "i", $tahun);
mysqli_stmt_execute($q1);
$res1 = mysqli_stmt_get_result($q1);
while ($r = mysqli_fetch_assoc($res1)) {
    if (isset($bulan_map[$r['bln']])) $pemasukan[$bulan_map[$r['bln']]] = (int)$r['total'];
}

// Pengeluaran
$q2 = mysqli_prepare($conn, "SELECT MONTH(tanggal_pengeluaran)-1 as idx, SUM(nominal_pengeluaran) as total FROM pengeluaran WHERE status_persetujuan='Disetujui' AND YEAR(tanggal_pengeluaran)=? GROUP BY MONTH(tanggal_pengeluaran)");
mysqli_stmt_bind_param($q2, "i", $tahun);
mysqli_stmt_execute($q2);
$res2 = mysqli_stmt_get_result($q2);
while ($r = mysqli_fetch_assoc($res2)) {
    $pengeluaran[$r['idx']] = (int)$r['total'];
}

echo json_encode(['pemasukan' => $pemasukan, 'pengeluaran' => $pengeluaran]);
?>