<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: ../login.php");
    exit();
if(isset($_POST['id_warga'])) {

    $id_warga = $_POST['id_warga'];
    $nama     = $_POST['nama_warga'];
    $nik      = $_POST['nik'];
    $alamat   = $_POST['alamat'];
    $no_telp  = $_POST['no_telp'];

    // 1. Ambil id_pengguna berdasarkan id_warga
    $q = mysqli_query($koneksi, "SELECT id_pengguna FROM warga WHERE id_warga='$id_warga'");
    $data = mysqli_fetch_assoc($q);
    $id_pengguna = $data['id_pengguna'];

    // 2. Update tabel pengguna
    $update = mysqli_query($koneksi, "
        UPDATE pengguna SET 
            nama='$nama',
            nik='$nik',
            alamat='$alamat',
            no_telp='$no_telp'
        WHERE id_pengguna='$id_pengguna'
    ");

    if($update){
        header("Location: ../kelola_warga.php");
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}

// Kalau langsung buka file ini tanpa POST → arahkan ke halaman utama
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../kelola_warga.php");
    exit();
}

// === MULAI PROSES UPDATE ===
$id_pengguna = (int)($_POST['id_pengguna'] ?? 0);
$nama        = trim($_POST['nama'] ?? '');
$nik         = trim($_POST['nik'] ?? '');
$alamat      = trim($_POST['alamat'] ?? '');
$no_telp     = trim($_POST['no_telp'] ?? '');

if ($id_pengguna <= 0 || empty($nama) || empty($nik)) {
    echo "<script>alert('Data tidak valid!'); history.back();</script>";
    exit();
}

// Cek NIK tidak dipakai orang lain
$cek = mysqli_prepare($koneksi, "SELECT id_pengguna FROM pengguna WHERE nik = ? AND id_pengguna != ?");
mysqli_stmt_bind_param($cek, "si", $nik, $id_pengguna);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);

if (mysqli_stmt_num_rows($cek) > 0) {
    echo "<script>alert('NIK sudah digunakan orang lain!'); history.back();</script>";
    exit();
}

// Update data
$stmt = mysqli_prepare($koneksi, 
    "UPDATE pengguna SET nama = ?, nik = ?, alamat = ?, no_telp = ? WHERE id_pengguna = ? AND role = 'warga'");
mysqli_stmt_bind_param($stmt, "ssssi", $nama, $nik, $alamat, $no_telp, $id_pengguna);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Data warga berhasil diperbarui!');
            window.location = '../kelola_warga.php';
          </script>";
} else {
    echo "<script>
            alert('Gagal update data!');
            history.back();
          </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>