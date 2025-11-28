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

// Cek apakah $conn ada dan koneksi berhasil
if (!isset($koneksi) || $koneksi->connect_error) {
    echo json_encode([
        'status'  => 'error', 
        'message' => 'Koneksi database gagal: ' . ($koneksi->connect_error ?? 'unknown')
    ]);
    ob_end_flush();
    exit;
}

// === INSERT KE DATABASE ===
$sql = "INSERT INTO pengeluaran (nama_pengeluaran, keterangan_pengeluaran, nominal_pengeluaran, jenis_pengeluaran, status_persetujuan, tanggal_pengeluaran) 
        VALUES (?, ?, ?, ?, 'Menunggu', CURDATE())";

if ($stmt = $koneksi->prepare($sql)) {
    $stmt->bind_param("ssis", $judul, $keterangan, $nominal, $kategori);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Pengajuan berhasil dikirim']);
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