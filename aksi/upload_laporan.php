<?php
header('Content-Type: application/json');
require '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

$bulanTahun = trim($_POST['bulan'] ?? '');
$file = $_FILES['file'] ?? null;

if (!$file || $file['error'] !== 0 || pathinfo($file['name'], PATHINFO_EXTENSION) !== 'pdf') {
    echo json_encode(['success' => false, 'message' => 'Harus upload file PDF!']);
    exit;
}
if (empty($bulanTahun)) {
    echo json_encode(['success' => false, 'message' => 'Bulan & tahun harus diisi']);
    exit;
}

// Validasi format: Januari 2025, Februari 2025, dst
if (!preg_match('/^(Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember)\s+\d{4}$/', $bulanTahun)) {
    echo json_encode(['success' => false, 'message' => 'Format salah! Contoh: Januari 2025']);
    exit;
}

$tahun = (int)substr($bulanTahun, -4);
$namaFile = "Laporan_" . str_replace(' ', '_', $bulanTahun) . ".pdf";
$folder   = "../assets/laporan/";
$pathFile = $folder . $namaFile;
$pathDB   = "assets/laporan/" . $namaFile;

// Cek apakah sudah ada laporan bulan ini
$cek = mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE bulan_tahun = '$bulanTahun'");

if (mysqli_num_rows($cek) > 0) {
    // UPDATE: ganti file lama
    if (file_exists($pathFile)) unlink($pathFile);
    move_uploaded_file($file['tmp_name'], $pathFile);

    $sql = "UPDATE laporan SET 
            tahun = $tahun,
            nama_file = '$namaFile',
            path_file = '$pathDB',
            tanggal_laporan = NOW()
            WHERE bulan_tahun = '$bulanTahun'";
    $pesan = "Laporan berhasil diperbarui!";
} else {
    // INSERT baru
    move_uploaded_file($file['tmp_name'], $pathFile);
    $sql = "INSERT INTO laporan (bulan_tahun, tahun, nama_file, path_file, tanggal_laporan) 
            VALUES ('$bulanTahun', $tahun, '$namaFile', '$pathDB', NOW())";
    $pesan = "Laporan berhasil diunggah!";
}

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => $pesan]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal simpan ke database']);
}
?>