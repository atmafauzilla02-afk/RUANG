<?php
ob_start();                                   
header('Content-Type: application/json');

// === KONEKSI DATABASE ===
require_once '../koneksi/koneksi.php';   

// Cek koneksi
if (!$koneksi) {
    echo json_encode(['error' => 'Koneksi database gagal']);
    ob_end_flush();
    exit;
}

// === QUERY  ===
$sql = "SELECT 
            nama_pengeluaran AS judul,
            nominal_pengeluaran AS nominal,
            jenis_pengeluaran AS kategori,
            DATE_FORMAT(tanggal_pengeluaran, '%d %M %Y') AS tanggal,
            status_persetujuan AS status,
            keterangan_pengeluaran AS deskripsi
        FROM pengeluaran
        ORDER BY id_pengeluaran DESC";

$result = mysqli_query($koneksi, $sql);

if (!$result) {
    echo json_encode(['error' => 'Query gagal: ' . mysqli_error($koneksi)]);
    ob_end_flush();
    exit;
}

// === AMBIL DATA ===
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'judul'     => htmlspecialchars($row['judul']),
        'deskripsi' => $row['deskripsi'],           
        'nominal'   => (int)$row['nominal'],
        'kategori'  => htmlspecialchars($row['kategori']),
        'status'    => $row['status'],
        'tanggal'   => $row['tanggal']
    ];
}

ob_end_clean();

echo json_encode($data);
exit;
?>