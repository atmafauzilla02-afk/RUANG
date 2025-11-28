<?php
include '../koneksi/koneksi.php';

if (isset($_POST['submit'])) {
    $nik = $_POST['nik'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE nik='$nik' AND password='$password'");
    $cek = mysqli_num_rows($query);
    $data = mysqli_fetch_assoc($query);

    if ($cek > 0) {
        session_start();
        $_SESSION['id_pengguna'] = $data['id_pengguna'];
        $_SESSION['nik'] = $data['nik'];    
        $_SESSION['role'] = $data['role'];
        if ($data['role'] === 'warga') {
            $query_warga = mysqli_query($koneksi, "SELECT * FROM warga WHERE id_pengguna='{$data['id_pengguna']}'");
            $data_warga = mysqli_fetch_assoc($query_warga);
            $_SESSION['id_warga'] = $data_warga['id_warga'];

            header("Location: ../dashboard.php");
        } else if ($data['role'] === 'bendahara') {
            $qB = mysqli_query($koneksi, "SELECT * FROM bendahara WHERE id_pengguna='{$data['id_pengguna']}'");
            $bendahara = mysqli_fetch_assoc($qB);
            $_SESSION['id_bendahara'] = $bendahara['id_bendahara'];

            header("Location: ../dashboardBendahara.php");
        } else if ($data['role'] === 'ketua') {
            $query_ketua = mysqli_query($koneksi, "SELECT * FROM ketua_rt WHERE id_pengguna='{$data['id_pengguna']}'");
            $data_ketua = mysqli_fetch_assoc($query_ketua);
            $_SESSION['id_ketua_rt'] = $data_ketua['id_ketua_rt'];

            header("Location: ../dashboardRT.php");
        }


        if ($data['role'] === 'warga') {

            header("Location: ../dashboard.php");
        } else if ($data['role'] === 'bendahara') {

            $qB = mysqli_query($koneksi, "SELECT * FROM bendahara WHERE id_pengguna='$data[id_pengguna]'");
            $bendahara = mysqli_fetch_assoc($qB);

            $_SESSION['id_bendahara'] = $bendahara['id_bendahara'];

            header("Location: ../dashboardBendahara.php");
        } else if ($data['role'] === 'ketua') {

            header("Location: ../dashboardRT.php");
        }
    } else {
        echo "<script>alert('NIK atau kata sandi salah.'); window.location='../index.php';</script>";
    }
}
