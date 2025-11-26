<?php
include "../koneksi/koneksi.php";

$sql = "SELECT pembayaran.*, warga.nama 
        FROM pembayaran 
        JOIN warga ON pembayaran.id_warga = warga.id_warga
        ORDER BY tahun_pembayaran DESC, bulan_pembayaran DESC";

$result = mysqli_query($koneksi, $sql);

$data = [];
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>
