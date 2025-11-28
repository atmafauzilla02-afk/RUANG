<?php
session_start();
include '../koneksi/koneksi.php';
if (!isset($_SESSION['id_pengguna'])) exit(header("Location: ../login.php"));

if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../login.php';
    </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_warga = $_POST['nama'] ?? '';
    $nik        = $_POST['nik'] ?? '';
    $alamat     = $_POST['alamat'] ?? '';
    $no_telp    = $_POST['no_telp'] ?? '';

    // default password
    $default_password = password_hash('password123', PASSWORD_DEFAULT);

    // 1. INSERT KE PENGGUNA
    $sqlPengguna = mysqli_query($koneksi, 
        "INSERT INTO pengguna (nik, nama, alamat, no_telp, role, password) 
         VALUES ('$nik', '$nama_warga', '$alamat', '$no_telp', 'warga', '$default_password')"
    );
}

    if (!$sqlPengguna) {
        echo "<script>
                alert('Gagal insert pengguna: ".mysqli_error($koneksi)."');
                window.history.back();
              </script>";
        exit;
    }

    // 2. AMBIL id_pengguna TERAKHIR
    $id_pengguna_baru = mysqli_insert_id($koneksi);

    // 3. INSERT KE WARGA
    $sqlWarga = mysqli_query($koneksi,
        "INSERT INTO warga (id_pengguna) VALUES ('$id_pengguna_baru')"
    );

    if ($sqlWarga) {
        echo "<script>
            alert('Data Berhasil Disimpan!');
            window.location.href = '../kelola_warga.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal insert ke tabel warga: ".mysqli_error($koneksi)."');
            window.history.back();
        </script>";
    }

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Warga berhasil ditambahkan!'); window.location='../kelola_warga.php';</script>";
} else {
    echo "<script>alert('Gagal menambah warga!'); history.back();</script>";
}
?>  