<?php
session_start();

include '../koneksi/koneksi.php';

// Cek user sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = '../login.php';
    </script>";
    exit;
}

// Ambil id_user dari session
$id_user = $_SESSION['id_user'];

// form dikirim POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil input form
    $nama_warga = $_POST['nama_warga'] ?? '';
    $nik        = $_POST['nik'] ?? '';
    $alamat     = $_POST['alamat'] ?? '';
    $no_telp    = $_POST['no_telp'] ?? '';

    // NILAI DEFAULT 
    $rt     = 0;            
    $rw     = 0;            
    $status = "Aktif";      

    // Query insert
    $input = mysqli_query($koneksi, 
        "INSERT INTO warga (id_user, nama_warga, nik, alamat, no_telp, rt, rw, status) 
         VALUES ('$id_user', '$nama_warga', '$nik', '$alamat', '$no_telp', '$rt', '$rw', '$status')"
    );

    if ($input) {
        echo "<script>
            alert('Data Berhasil Disimpan');
            window.location.href = '../kelola_warga.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal Menyimpan Data: ".mysqli_error($koneksi)."');
            window.history.back();
        </script>";
    }

} else {
    echo "Form tidak dikirim menggunakan POST.";
}
?>
