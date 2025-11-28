<?php
header('Content-Type: application/json');
require '../koneksi/koneksi.php';

// Hanya ambil yang statusnya Menunggu atau null (untuk jaga-jaga)
$sql = "SELECT 
            id_pengeluaran as id,
            nama_pengeluaran as judul,
            CONCAT('Bendahara') as pengaju,  -- bisa diganti nama bendahara nanti kalau mau
            keterangan_pengeluaran as deskripsi,
            DATE_FORMAT(tanggal_pengeluaran, '%d %M %Y') as tanggal,
            IFNULL(status_persetujuan, 'Menunggu') as status
        FROM pengeluaran 
        WHERE status_persetujuan IS NULL 
           OR status_persetujuan = 'Menunggu'
           OR status_persetujuan = 'Disetujui' 
           OR status_persetujuan = 'Ditolak'
        ORDER BY tanggal_pengeluaran DESC";

$result = mysqli_query($koneksi, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Ubah "Menunggu" jadi "Menunggu Persetujuan" biar sesuai tampilan
    if ($row['status'] == '' || $row['status'] == null || $row['status'] == 'Menunggu') {
        $row['status'] = 'Menunggu Persetujuan';
    }
    $data[] = $row;
}

echo json_encode($data);
?>