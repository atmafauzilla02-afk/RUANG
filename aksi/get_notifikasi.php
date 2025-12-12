<?php
session_start();
include '../koneksi/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_warga'])) {
    echo json_encode([
        'error' => 'Session id_warga tidak ditemukan',
        'notifikasi' => [], 
        'belum_dibaca' => 0
    ]);
    exit;
}

$id_warga = mysqli_real_escape_string($koneksi, $_SESSION['id_warga']);

$query = mysqli_query($koneksi, "
    SELECT * FROM notifikasi_warga 
    WHERE id_warga = '$id_warga' 
    ORDER BY tanggal DESC
    LIMIT 50
");

if (!$query) {
    echo json_encode([
        'error' => mysqli_error($koneksi), 
        'notifikasi' => [], 
        'belum_dibaca' => 0
    ]);
    exit;
}

$notifikasi = [];
$jumlah_belum_dibaca = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $notifikasi[] = $row;
    if ($row['dibaca'] == 0) {
        $jumlah_belum_dibaca++;
    }
}

echo json_encode([
    'notifikasi' => $notifikasi,
    'belum_dibaca' => $jumlah_belum_dibaca,
    'success' => true
]);
?>