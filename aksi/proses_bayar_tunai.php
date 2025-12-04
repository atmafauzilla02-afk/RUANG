<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'bendahara') {
    die("Akses ditolak!");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

$id_pembayaran = $_POST['id_pembayaran'] ?? '';
$tanggal_bayar = $_POST['tanggal_bayar'] ?? date('Y-m-d');

if (empty($id_pembayaran) || !is_numeric($id_pembayaran)) {
    die("ID pembayaran tidak valid!");
}

if (!isset($_FILES['bukti_pembayaran']) || $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK) {
    $error_code = $_FILES['bukti_pembayaran']['error'] ?? 'unknown';
    die("Error upload file! Kode: $error_code");
}

$file = $_FILES['bukti_pembayaran'];
$allowed = ['jpg','jpeg','png','gif'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    die("File harus JPG/PNG/GIF!");
}
if ($file['size'] > 2 * 1024 * 1024) {
    die("File maksimal 2MB!");
}

$uploadDir = "../uploads/iuran/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$newFilename = "bukti_tunai_{$id_pembayaran}_" . time() . ".$ext";
$targetPath = $uploadDir . $newFilename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    die("Gagal menyimpan file bukti!");
}

$id_pembayaran = mysqli_real_escape_string($koneksi, $id_pembayaran);
$tanggal_bayar = mysqli_real_escape_string($koneksi, $tanggal_bayar);
$newFilename = mysqli_real_escape_string($koneksi, $newFilename);

$sql = "UPDATE pembayaran SET 
        status_pembayaran = 'lunas',
        bukti_pembayaran = '$newFilename',
        tanggal_pembayaran = '$tanggal_bayar',
        metode_pembayaran = 'tunai'
        WHERE id_pembayaran = '$id_pembayaran' 
        AND status_pembayaran = 'belum'";

$result = mysqli_query($koneksi, $sql);

if ($result && mysqli_affected_rows($koneksi) > 0) {
    echo "<script>
        alert('Pembayaran tunai berhasil disimpan!\\nStatus: LUNAS');
        window.location = '../iuran.php';
    </script>";
} else {
    if (file_exists($targetPath)) unlink($targetPath);
    
    $error = mysqli_error($koneksi);
    echo "<script>
        alert('Gagal update status!\\nError: $error');
        history.back();
    </script>";
}
?>