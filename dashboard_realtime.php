<?php
session_start();
require '../koneksi/koneksi.php';

header('Content-Type: application/json');

$bulan_ini = date('Y-m');
$tahun_ini = date('Y');
$bulan = date('m');

$saldo = $pdo->query("
    SELECT 
        COALESCE(SUM(CASE WHEN jenis_pembayaran = 'kas' AND status_pembayaran = 'lunas' THEN jumlah ELSE 0 END), 0) 
        - 
        COALESCE((SELECT SUM(nominal_pengeluaran) FROM pengeluaran WHERE status_persetujuan = 'disetujui'), 0) AS saldo
    FROM pembayaran
")->fetchColumn();

$pemasukan = $pdo->prepare("
    SELECT COALESCE(SUM(jumlah), 0)
    FROM pembayaran 
    WHERE jenis_pembayaran = 'kas' 
      AND status_pembayaran = 'lunas'
      AND YEAR(tanggal_bayar) = ? AND MONTH(tanggal_bayar) = ?
");
$pemasukan->execute([$tahun_ini, $bulan]);
$pemasukan_bulan = $pemasukan->fetchColumn();

$pengeluaran = $pdo->prepare("
    SELECT COALESCE(SUM(nominal_pengeluaran), 0)
    FROM pengeluaran 
    WHERE status_persetujuan = 'disetujui'
      AND YEAR(tanggal_pengeluaran) = ? AND MONTH(tanggal_pengeluaran) = ?
");
$pengeluaran->execute([$tahun_ini, $bulan]);
$pengeluaran_bulan = $pengeluaran->fetchColumn();

echo json_encode([
    'saldo'              => 'Rp ' . number_format($saldo, 0, ',', '.'),
    'saldo_raw'          => (float)$saldo,
    'pemasukan_bulan'    => 'Rp ' . number_format($pemasukan_bulan, 0, ',', '.'),
    'pemasukan_raw'      => (float)$pemasukan_bulan,
    'pengeluaran_bulan'  => 'Rp ' . number_format($pengeluaran_bulan, 0, ',', '.'),
    'pengeluaran_raw'    => (float)$pengeluaran_bulan,
    'updated_at'         => date('H:i:s')
]);
?>