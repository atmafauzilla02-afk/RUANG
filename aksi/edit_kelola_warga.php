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

$id_warga = trim($_POST['id_warga']);
$nama     = trim($_POST['nama_warga']);
$nik      = trim($_POST['nik']);
$alamat   = trim($_POST['alamat']);
$no_telp  = trim($_POST['no_telp']);

if (empty($id_warga) || empty($nama) || empty($nik) || empty($alamat) || empty($no_telp)) {
    echo "<script>
        alert('❌ Semua harus diisi!');
        window.history.back();
    </script>";
    exit;
}

if (!preg_match('/^[0-9]{16}$/', $nik)) {
    echo "<script>
        alert('❌ NIK harus berisi 16 digit angka!\\n\\nContoh: 1234567890123456');
        window.history.back();
    </script>";
    exit;
}

if (!preg_match('/^[0-9]{1,20}$/', $no_telp)) {
    echo "<script>
        alert('❌ Nomor telepon harus angka dengan maksimal 20 digit!\\n\\nContoh: 081234567890');
        window.history.back();
    </script>";
    exit;
}

$stmt_cek = $koneksi->prepare("
    SELECT p.id_pengguna 
    FROM pengguna p
    INNER JOIN warga w ON p.id_pengguna = w.id_pengguna
    WHERE p.nik = ? 
      AND w.id_warga != ?
");
$stmt_cek->bind_param("si", $nik, $id_warga);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    echo "<script>
        alert('❌ NIK $nik sudah ada');
        window.history.back();
    </script>";
    exit;
}
$stmt_cek->close();

$stmt_warga = $koneksi->prepare("SELECT id_pengguna FROM warga WHERE id_warga = ?");
$stmt_warga->bind_param("i", $id_warga);
$stmt_warga->execute();
$result_warga = $stmt_warga->get_result();

if ($result_warga->num_rows == 0) {
    echo "<script>
        alert('❌ Data warga tidak ditemukan!');
        window.history.back();
    </script>";
    exit;
}

$row = $result_warga->fetch_assoc();
$id_pengguna = $row['id_pengguna'];
$stmt_warga->close();

$stmt_update = $koneksi->prepare("
    UPDATE pengguna SET 
        nama     = ?,
        nik      = ?,
        alamat   = ?,
        no_telp  = ?
    WHERE id_pengguna = ?
");
$stmt_update->bind_param("ssssi", $nama, $nik, $alamat, $no_telp, $id_pengguna);

if ($stmt_update->execute()) {
    echo "<script>
        alert('✅ Data warga berhasil diupdate!');
        window.location.href = '../kelola_warga.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Gagal update data!\\n\\nError: " . addslashes($stmt_update->error) . "');
        window.history.back();
    </script>";
}

$stmt_update->close();
$koneksi->close();
?>