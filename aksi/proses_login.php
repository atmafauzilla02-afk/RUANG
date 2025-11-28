<?php
include '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nik      = trim($_POST['nik']);
    $password = trim($_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE nik = '$nik' AND password = '$password' LIMIT 1");

    if ($query === false) {
        die("Error query: " . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        session_start();
        $_SESSION['id_pengguna'] = $data['id_pengguna'];
        $_SESSION['nik']         = $data['nik'];
        $_SESSION['role']        = $data['role'];

        if ($data['role'] === 'warga') {
            $q = mysqli_query($koneksi, "SELECT id_warga FROM warga WHERE id_pengguna = '{$data['id_pengguna']}'");
            if ($w = mysqli_fetch_assoc($q)) {
                $_SESSION['id_warga'] = $w['id_warga'];
            }
            header("Location: ../dashboard.php");
            exit();

        } elseif ($data['role'] === 'bendahara') {
            $q = mysqli_query($koneksi, "SELECT id_bendahara FROM bendahara WHERE id_pengguna = '{$data['id_pengguna']}'");
            if ($b = mysqli_fetch_assoc($q)) {
                $_SESSION['id_bendahara'] = $b['id_bendahara'];
            }
            header("Location: ../dashboardBendahara.php");
            exit();

        } elseif ($data['role'] === 'ketua') {
            $q = mysqli_query($koneksi, "SELECT id_ketua FROM ketua WHERE id_pengguna = '{$data['id_pengguna']}'");
            if ($k = mysqli_fetch_assoc($q)) {
                $_SESSION['id_ketua'] = $k['id_ketua'];
            }
            header("Location: ../dashboardRT.php");
            exit();
        }

    } else {
        echo "<script>
                alert('NIK atau Password salah!');
                window.location='../index.php';
              </script>";
    }
}
?>