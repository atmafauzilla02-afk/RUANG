<?php
ob_clean();
header('Content-Type: application/json');
require_once '../koneksi/koneksi.php';

if (!isset($_FILES['file']) || !isset($_POST['bulan'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$bulanTahun = trim($_POST['bulan']);
$file = $_FILES['file'];

// Validasi file PDF
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    echo json_encode(['success' => false, 'message' => 'Hanya file PDF!']);
    exit;
}

$filename = "laporan_" . preg_replace("/[^a-zA-Z0-9]/", "_", $bulanTahun) . "_" . time() . ".pdf";
$uploadDir = "../laporan";
$fullPath = $uploadDir . $filename;

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (move_uploaded_file($file['tmp_name'], $fullPath)) {
    preg_match('/\d{4}/', $bulanTahun, $matches);
    $tahun = !empty($matches) ? $matches[0] : date('Y');

    $cek = mysqli_query($koneksi, "SELECT id_laporan FROM laporan WHERE bulan_tahun = '" . mysqli_real_escape_string($koneksi, $bulanTahun) . "'");
    
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($koneksi, "UPDATE laporan SET 
            nama_file = '$filename',
            path_file = '$fullPath',
            tahun = '$tahun'
            WHERE bulan_tahun = '" . mysqli_real_escape_string($koneksi, $bulanTahun) . "'");
    } else {
        mysqli_query($koneksi, "INSERT INTO laporan 
            (bulan_tahun, tahun, nama_file, path_file) 
            VALUES 
            ('" . mysqli_real_escape_string($koneksi, $bulanTahun) . "', '$tahun', '$filename', '$fullPath')");
    }

    echo json_encode(['success' => true, 'message' => 'Laporan berhasil disimpan!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal upload file']);
}
exit;
?>