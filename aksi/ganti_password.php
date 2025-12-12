<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../login.php';
    </script>";
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];
$password_lama = mysqli_real_escape_string($koneksi, $_POST['password_lama']);
$password_baru = mysqli_real_escape_string($koneksi, $_POST['password_baru']);
$password_konfirmasi = mysqli_real_escape_string($koneksi, $_POST['password_konfirmasi']);

// Validasi password baru dan konfirmasi
if ($password_baru !== $password_konfirmasi) {
    echo "<script>
        alert('❌ Password baru dan konfirmasi tidak sama!');
        window.history.back();
    </script>";
    exit;
}

// Validasi panjang password
if (strlen($password_baru) < 6) {
    echo "<script>
        alert('❌ Password minimal 6 karakter!');
        window.history.back();
    </script>";
    exit;
}

// Cek password lama
$cek_password = mysqli_query($koneksi, "
    SELECT password FROM pengguna 
    WHERE id_pengguna = '$id_pengguna'
");

$data = mysqli_fetch_assoc($cek_password);

// Verifikasi password lama (dengan password_verify jika pakai hashing)
if ($data['password'] !== $password_lama) {
    echo "<script>
        alert('❌ Password lama salah!');
        window.history.back();
    </script>";
    exit;
}

// Update password baru
$update = mysqli_query($koneksi, "
    UPDATE pengguna 
    SET password = '$password_baru' 
    WHERE id_pengguna = '$id_pengguna'
");

if ($update) {
    echo "<script>
        alert('✅ Password berhasil diubah!\\n\\nSilakan login kembali dengan password baru.');
        window.location.href = '../logout.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Gagal mengubah password: " . mysqli_error($koneksi) . "');
        window.history.back();
    </script>";
}
?>