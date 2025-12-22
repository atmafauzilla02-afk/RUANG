<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bendahara') {
    echo "<script>alert('Akses ditolak!'); window.location.href = '../index.php';</script>";
    exit;
}

$nama    = trim($_POST['nama_warga'] ?? '');
$nik     = trim($_POST['nik'] ?? '');
$alamat  = trim($_POST['alamat'] ?? '');
$no_telp = trim($_POST['no_telp'] ?? '');

if ($nama === '' || $nik === '' || $alamat === '' || $no_telp === '') {
    echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
    exit;
}

if (!preg_match('/^\d{16}$/', $nik)) {
    echo "<script>alert('NIK harus 16 digit angka!'); window.history.back();</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT id_pengguna FROM pengguna WHERE nik = ?");
$stmt->bind_param("s", $nik);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    echo "<script>alert('NIK $nik sudah terdaftar! Gunakan NIK lain.'); window.history.back();</script>";
    exit;
}
$stmt->close();

$password_hash = password_hash($nik, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("
    INSERT INTO pengguna 
    (nik, nama, alamat, no_telp, password, role, status) 
    VALUES 
    (?, ?, ?, ?, ?, 'warga', 'Aktif')
");
$stmt->bind_param("sssss", $nik, $nama, $alamat, $no_telp, $password_hash);

if (!$stmt->execute()) {
    $stmt->close();
    echo "<script>alert('Gagal menambah akun pengguna!'); window.history.back();</script>";
    exit;
}

$id_pengguna_baru = $koneksi->insert_id;
$stmt->close();

$stmt = $koneksi->prepare("INSERT INTO warga (id_pengguna) VALUES (?)");
$stmt->bind_param("i", $id_pengguna_baru);

if ($stmt->execute()) {
    $stmt->close();
    echo "<script>
        alert('Warga berhasil ditambahkan!');
        window.location.href = '../kelola_warga.php';
    </script>";
} else {
    $koneksi->query("DELETE FROM pengguna WHERE id_pengguna = $id_pengguna_baru");
    $stmt->close();
    echo "<script>alert('Gagal menambah data warga! Silakan coba lagi.'); window.history.back();</script>";
}
?>