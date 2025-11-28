<?php
ob_start();
header('Content-Type: application/json');

// Koneksi database
$conn = mysqli_connect("localhost", "root", "", "ruang");
if (!$conn) {
    echo json_encode(['error' => 'Koneksi database gagal']);
    exit;
}

$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Validasi tahun (biar aman)
if ($tahun < 2000 || $tahun > 3000) $tahun = date('Y');

$pemasukan   = array_fill(0, 12, 0);
$pengeluaran = array_fill(0, 12, 0);

$bulan_list = ['january','february','march','april','may','june','july','august','september','october','november','december'];

for ($i = 0; $i < 12; $i++) {
    $bln = $bulan_list[$i];

    // Pemasukan (lunas)
    $sql1 = "SELECT COALESCE(SUM(nominal_pembayaran),0) 
             FROM pembayaran 
             WHERE status_pembayaran='lunas' 
               AND tahun_pembayaran='$tahun' 
               AND bulan_pembayaran='$bln'";
    $res1 = mysqli_query($conn, $sql1);
    $pemasukan[$i] = $res1 && mysqli_num_rows($res1) > 0 ? (float)mysqli_fetch_array($res1)[0] / 1000000 : 0;

    // Pengeluaran (disetujui)
    $bulan_ke = $i + 1;
    $sql2 = "SELECT COALESCE(SUM(nominal_pengeluaran),0) 
             FROM pengeluaran 
             WHERE status_persetujuan='Disetujui' 
               AND YEAR(tanggal_pengeluaran)='$tahun' 
               AND MONTH(tanggal_pengeluaran)='$bulan_ke'";
    $res2 = mysqli_query($conn, $sql2);
    $pengeluaran[$i] = $res2 && mysqli_num_rows($res2) > 0 ? (float)mysqli_fetch_array($res2)[0] / 1000000 : 0;
}

// Keluarkan JSON bersih
echo json_encode([
    'pemasukan' => array_map('floatval', $pemasukan),
    'pengeluaran' => array_map('floatval', $pengeluaran)
]);

// Pastikan tidak ada output lain
ob_end_flush();
exit;
?>