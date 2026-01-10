<?php
session_start();
include '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$nik      = trim($_POST['nik'] ?? '');
$password = $_POST['password'] ?? '';

if ($nik === '' || $password === '') {
    echo "<script>
            alert('NIK dan password harus diisi!');
            window.location.href = '../index.php';
          </script>";
    exit;    
}

$stmt = $koneksi->prepare("SELECT id_pengguna, nik, nama, password, role FROM pengguna WHERE nik = ? LIMIT 1");
$stmt->bind_param("s", $nik);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
            alert('NIK tidak ditemukan!');
            window.location.href = '../index.php';
          </script>";
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

$password_valid = false;

if (password_verify($password, $user['password'])) {
    $password_valid = true;
} elseif ($user['password'] === $password) {
    $password_valid = true;
}

if (!$password_valid) {
    echo "<script>
            alert('Password salah!');
            window.location.href = '../index.php';
          </script>";
    exit;
}

if ($user['password'] === $password || password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $update_stmt = $koneksi->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
    $update_stmt->bind_param("si", $new_hash, $user['id_pengguna']);
    $update_stmt->execute();
    $update_stmt->close();
}

$_SESSION['id_pengguna'] = $user['id_pengguna'];
$_SESSION['nik']         = $user['nik'];
$_SESSION['nama']        = $user['nama'] ?? $user['nik'];
$_SESSION['role']        = $user['role'];

$redirect_map = [
    'warga'     => ['table' => 'warga',     'id_col' => 'id_warga',     'session_key' => 'id_warga',     'location' => '../dashboard.php'],
    'bendahara' => ['table' => 'bendahara', 'id_col' => 'id_bendahara', 'session_key' => 'id_bendahara', 'location' => '../dashboardBendahara.php'],
    'ketua'     => ['table' => 'ketua',     'id_col' => 'id_ketua',     'session_key' => 'id_ketua',     'location' => '../dashboardRT.php']
];

if (isset($redirect_map[$user['role']])) {
    $info = $redirect_map[$user['role']];
    
    $stmt = $koneksi->prepare("SELECT {$info['id_col']} FROM {$info['table']} WHERE id_pengguna = ?");
    $stmt->bind_param("i", $user['id_pengguna']);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($row = $res->fetch_assoc()) {
        $_SESSION[$info['session_key']] = $row[$info['id_col']];
    }
    $stmt->close();
    
    header("Location: " . $info['location']);
    exit;
}

header("Location: ../dashboard.php");
exit;
?>