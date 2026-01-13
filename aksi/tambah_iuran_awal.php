<?php
session_start();
include '../koneksi/koneksi.php';

$id_warga = $_POST['id_warga'];
$jenis    = $_POST['jenis'];
$bulan    = $_POST['bulan'];
$tahun    = $_POST['tahun'];
$nominal  = $_POST['nominal'];

$cek = mysqli_query($koneksi, "
    SELECT * FROM pembayaran 
    WHERE id_warga='$id_warga' 
    AND jenis_pembayaran='$jenis'
    AND bulan_pembayaran='$bulan'
    AND tahun_pembayaran='$tahun'
");

if (mysqli_num_rows($cek) > 0) {

    echo "<script>
        alert('Iuran untuk warga ini di bulan $bulan $tahun pada kategori $jenis sudah dibuat!');
        window.location.href='../iuran.php';
    </script>";
    exit;
}

mysqli_query($koneksi, "
    INSERT INTO pembayaran 
    (id_warga, jenis_pembayaran, bulan_pembayaran, tahun_pembayaran, nominal_pembayaran, status_pembayaran)
    VALUES 
    ('$id_warga','$jenis','$bulan','$tahun','$nominal','belum')
");

echo "<script>
    alert('Iuran berhasil ditambahkan!');
    window.location.href='../iuran.php';
</script>";

?>
