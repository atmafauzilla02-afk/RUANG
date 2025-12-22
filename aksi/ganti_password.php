<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../index.php';
    </script>";
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

$password_lama = $_POST['password_lama'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';
$password_konfirmasi = $_POST['password_konfirmasi'] ?? '';

if ($password_baru !== $password_konfirmasi) {
    echo "<script>
        alert('❌ Password baru dan konfirmasi tidak sama!');
        window.history.back();
    </script>";
    exit;
}

if (strlen($password_baru) < 6) {
    echo "<script>
        alert('❌ Password minimal 6 karakter!');
        window.history.back();
    </script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT password FROM pengguna WHERE id_pengguna = ?");
$stmt->bind_param("i", $id_pengguna);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
        alert('❌ Terjadi kesalahan sistem.');
        window.history.back();
    </script>";
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password_lama, $data['password'])) {
    echo "<script>
        alert('❌ Password lama salah!');
        window.history.back();
    </script>";
    exit;
}

$hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);

$stmt = $koneksi->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
$stmt->bind_param("si", $hash_baru, $id_pengguna);

if ($stmt->execute()) {
    $stmt->close();
    echo "<script>
        alert('✅ Password berhasil diubah!\\nSilakan login kembali dengan password baru.');
        window.location.href = '../logout.php';
    </script>";
} else {
    $stmt->close();
    echo "<script>
        alert('❌ Gagal mengubah password.');
        window.history.back();
    </script>";
}
?>