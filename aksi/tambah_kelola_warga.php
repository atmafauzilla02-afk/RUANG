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
    echo "<script>alert('❌ Semua wajib diisi!'); window.history.back();</script>";
    exit;
}

if (!preg_match('/^[0-9]{16}$/', $nik)) {
    echo "<script>
        alert('❌ NIK harus berisi 16 digit angka!\\n\\nContoh: 1234567890123456');
        window.history.back();
    </script>";
    exit;
}

if (!preg_match('/^[0-9]{10,20}$/', $no_telp)) {
    echo "<script>
        alert('❌ Nomor telepon harus berisi angka!');
        window.history.back();
    </script>";
    exit;
}

if (!preg_match('/^[a-zA-Z\s.\',-]+$/', $nama)) {
    echo "<script>
        alert('❌ Nama hanya boleh berisi huruf, spasi, dan karakter titik, koma, apostrof, atau strip!');
        window.history.back();
    </script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT id_pengguna FROM pengguna WHERE nik = ?");
$stmt->bind_param("s", $nik);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    echo "<script>
        alert('❌ NIK $nik sudah terdaftar!');
        window.history.back();
    </script>";
    exit;
}
$stmt->close();

$password_hash = password_hash($nik, PASSWORD_DEFAULT);

$koneksi->begin_transaction();

try {
    $stmt = $koneksi->prepare("
        INSERT INTO pengguna 
        (nik, nama, alamat, no_telp, password, role, status) 
        VALUES 
        (?, ?, ?, ?, ?, 'warga', 'Aktif')
    ");
    $stmt->bind_param("sssss", $nik, $nama, $alamat, $no_telp, $password_hash);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal menambah akun pengguna: " . $stmt->error);
    }
    
    $id_pengguna_baru = $koneksi->insert_id;
    $stmt->close();
    
    $stmt = $koneksi->prepare("INSERT INTO warga (id_pengguna) VALUES (?)");
    $stmt->bind_param("i", $id_pengguna_baru);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal menambah data warga: " . $stmt->error);
    }
    
    $stmt->close();
    
    $koneksi->commit();
    
    echo "<script>
        alert('✅ Warga berhasil ditambahkan!\\n\\nNama: $nama\\nNIK: $nik');
        window.location.href = '../kelola_warga.php';
    </script>";
    
} catch (Exception $e) {

    $koneksi->rollback();
    
    echo "<script>
        alert('❌ Gagal menambah data warga!\\n\\nError: " . addslashes($e->getMessage()) . "');
        window.history.back();
    </script>";
}

$koneksi->close();
?>