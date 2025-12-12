<?php
ob_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Hanya POST yang diizinkan']);
    ob_end_flush();
    exit;
}

$judul      = $_POST['judul'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$nominal    = $_POST['nominal'] ?? '';
$kategori   = $_POST['kategori'] ?? '';

$judul      = trim($judul);
$keterangan = trim($keterangan);
$nominal    = trim($nominal);
$kategori   = trim($kategori);

// Validasi input
if ($judul === '' || $keterangan === '' || $nominal === '' || $kategori === '') {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi']);
    ob_end_flush();
    exit;
}
if (!is_numeric($nominal) || $nominal <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Nominal harus angka positif']);
    ob_end_flush();
    exit;
}

$koneksi_path = '../koneksi/koneksi.php';
if (!file_exists($koneksi_path)) {
    echo json_encode(['status' => 'error', 'message' => 'File koneksi.php tidak ditemukan!']);
    ob_end_flush();
    exit;
}

require_once $koneksi_path;

if (!isset($koneksi) || $koneksi->connect_error) {
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Koneksi database gagal: ' . ($koneksi->connect_error ?? 'unknown')
    ]);
    ob_end_flush();
    exit;
}

$sql_pemasukan = "SELECT COALESCE(SUM(nominal_pembayaran), 0) as total_pemasukan 
                  FROM pembayaran 
                  WHERE status_pembayaran = 'lunas'";

$result_pemasukan = $koneksi->query($sql_pemasukan);
$total_pemasukan = 0;
if ($result_pemasukan && $row = $result_pemasukan->fetch_assoc()) {
    $total_pemasukan = (float)$row['total_pemasukan'];
}

$sql_pengeluaran = "SELECT COALESCE(SUM(nominal_pengeluaran), 0) as total_pengeluaran 
                    FROM pengeluaran 
                    WHERE status_persetujuan = 'Disetujui'";

$result_pengeluaran = $koneksi->query($sql_pengeluaran);
$total_pengeluaran = 0;
if ($result_pengeluaran && $row = $result_pengeluaran->fetch_assoc()) {
    $total_pengeluaran = (float)$row['total_pengeluaran'];
}

$saldo_kas = $total_pemasukan - $total_pengeluaran;

error_log("SALDO DEBUG: Pemasukan = $total_pemasukan | Pengeluaran = $total_pengeluaran | Saldo = $saldo_kas");

if ($nominal > $saldo_kas) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Saldo kas tidak mencukupi! Saldo saat ini: Rp' . number_format($saldo_kas, 0, ',', '.') .
                     ' (Pemasukan: Rp' . number_format($total_pemasukan, 0, ',', '.') . 
                     ', Pengeluaran: Rp' . number_format($total_pengeluaran, 0, ',', '.') . ')'
    ]);
    $koneksi->close();
    ob_end_flush();
    exit;
}

// === INSERT KE DATABASE (jika saldo mencukupi) ===
$sql = "INSERT INTO pengeluaran (nama_pengeluaran, keterangan_pengeluaran, nominal_pengeluaran, jenis_pengeluaran, status_persetujuan, tanggal_pengeluaran) 
        VALUES (?, ?, ?, ?, 'Menunggu', CURDATE())";

if ($stmt = $koneksi->prepare($sql)) {
    $stmt->bind_param("ssis", $judul, $keterangan, $nominal, $kategori);
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Pengajuan berhasil dikirim. Sisa saldo: Rp' . number_format($saldo_kas - $nominal, 0, ',', '.')
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal insert: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Prepare gagal: ' . $koneksi->error]);
}

$koneksi->close();
ob_end_flush();
exit;