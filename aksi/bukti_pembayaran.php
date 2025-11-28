<?php
$query = mysqli_query($koneksi, "
  SELECT pembayaran.*, warga.id_pengguna, pengguna.nama 
  FROM pembayaran 
  JOIN warga ON warga.id_warga = pembayaran.id_warga 
  JOIN pengguna ON warga.id_pengguna = pengguna.id_pengguna
");
?>
