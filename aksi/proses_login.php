<?php
include '../koneksi/koneksi.php';

if (isset($_POST['submit'])) {

    $nik = $_POST['nik'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE nik='$nik' AND password='$password'");;
    $cek = mysqli_num_rows($query);
    $data = mysqli_fetch_array($query);

    if ($cek > 0) {
        session_start();
        $_SESSION['id_pengguna'] = $data['id_pengguna'];
        $_SESSION['nik'] = $data['nik'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] == 'warga') {
            header("Location: ../dashboard.php");
        } elseif ($data['role'] == 'bendahara') {
            header("Location: ../dashboardBendahara.php");
        } elseif ($data['role'] == 'rt') {
            header("Location: ../dashboardRT.php");
        } else {
            echo "<script>alert('Role tidak dikenali.'); window.location='../index.php';</script>";
        }
    } else {
        echo "<script>alert('NIK atau kata sandi salah.'); window.location='../index.php';</script>";
    }
}
?>
