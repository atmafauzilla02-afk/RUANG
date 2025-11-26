<?php
include "../koneksi/koneksi.php";

$result = mysqli_query($koneksi, "SELECT * FROM pengeluaran ORDER BY id_pengeluaran DESC");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
