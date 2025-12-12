<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>
        alert('Anda harus login sebagai bendahara!');
        window.location.href = '../login.php';
    </script>";
    exit;
}

$id_warga = mysqli_real_escape_string($koneksi, $_POST['id_warga']);
$nama     = mysqli_real_escape_string($koneksi, $_POST['nama_warga']);
$nik      = mysqli_real_escape_string($koneksi, $_POST['nik']);
$alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
$no_telp  = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

$cek_nik = mysqli_query($koneksi, "
    SELECT id_pengguna 
    FROM pengguna 
    WHERE nik = '$nik' 
      AND id_pengguna != (SELECT id_pengguna FROM warga WHERE id_warga = '$id_warga')
");

if (mysqli_num_rows($cek_nik) > 0) {
    echo "<script>
        alert('❌ NIK $nik sudah digunakan oleh warga lain!\\n\\nGunakan NIK yang berbeda.');
        window.history.back();
    </script>";
    exit;
}

$q = mysqli_query($koneksi, "SELECT id_pengguna FROM warga WHERE id_warga = '$id_warga'");

if (mysqli_num_rows($q) == 0) {
    echo "<script>
        alert('❌ Data warga tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

$row = mysqli_fetch_assoc($q);
$id_pengguna = $row['id_pengguna'];

$update = mysqli_query($koneksi, "
    UPDATE pengguna SET 
        nama     = '$nama',
        nik      = '$nik',
        alamat   = '$alamat',
        no_telp  = '$no_telp'
    WHERE id_pengguna = '$id_pengguna'
");

if ($update) {
    echo "<script>
        alert('✅ Data warga berhasil diupdate!.');
        window.location.href = '../kelola_warga.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Gagal update data!\\n\\nError: " . mysqli_error($koneksi) . "');
        window.history.back();
    </script>";
}
?>