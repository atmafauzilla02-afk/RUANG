<?php
session_start();
include '../koneksi/koneksi.php';

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$nama    = mysqli_real_escape_string($koneksi, $_POST['nama_warga']);
$nik     = mysqli_real_escape_string($koneksi, $_POST['nik']);
$alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
$no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

if (empty(trim($nama)) || empty(trim($nik)) || empty(trim($alamat)) || empty(trim($no_telp))) {
    echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
    exit;
}

$cek_nik = mysqli_query($koneksi, "SELECT id_pengguna FROM pengguna WHERE nik = '$nik'");
if (!$cek_nik) {
    echo "<script>alert('Error query: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    exit;
}
if (mysqli_num_rows($cek_nik) > 0) {
    echo "<script>alert('NIK $nik sudah terdaftar! Gunakan NIK lain.'); window.history.back();</script>";
    exit;
}

$insert_pengguna = mysqli_query($koneksi, "INSERT INTO pengguna 
    (nik, nama, alamat, no_telp, password, role, status) 
    VALUES 
    ('$nik', '$nama', '$alamat', '$no_telp', '$nik', 'warga', 'Aktif')");

if (!$insert_pengguna) {
    echo "<script>alert('Gagal menambah akun pengguna!\\nError: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    exit;
}

$id_pengguna_baru = mysqli_insert_id($koneksi);

$insert_warga = mysqli_query($koneksi, "INSERT INTO warga (id_pengguna) VALUES ('$id_pengguna_baru')");

if ($insert_warga) {
    echo "<script>
        alert('Warga berhasil ditambahkan!\\n\\nUsername (NIK): $nik\\nPassword: $nik\\nStatus: Aktif\\n\\nWarga bisa langsung login dengan NIK sebagai username & password.');
        window.location='../kelola_warga.php';
    </script>";
} else {
    mysqli_query($koneksi, "DELETE FROM pengguna WHERE id_pengguna = '$id_pengguna_baru'");
    echo "<script>alert('Gagal menambah data warga!\\nError: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
}
?>