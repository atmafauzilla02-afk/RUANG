<?php
session_start();
include '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_pembayaran = $_POST['id_pembayaran'];
    $role = $_SESSION['role'] ?? 'warga'; 
    $redirect = ($role === 'bendahara') ? '../iuran.php' : '../status.php';

    $bukti = null;

    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {

        $folder = "../assets/bukti_pembayaran/";
        
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('bukti_') . '.' . $ext;
        $fullpath = $folder . $filename;

        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $fullpath)) {
            $bukti = $filename; 
        }
    }

    if (!$bukti) {
        echo "<script>
            alert('Gagal mengupload bukti! Pastikan file valid dan folder memiliki izin tulis.');
            window.location='$redirect';
        </script>";
        exit;
    }

    $stmt = mysqli_prepare($koneksi, "
        UPDATE pembayaran SET
            bukti_pembayaran = ?,
            status_pembayaran = 'menunggu'
        WHERE id_pembayaran = ?
    ");
    mysqli_stmt_bind_param($stmt, "si", $bukti, $id_pembayaran);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            alert('Bukti berhasil diajukan! Menunggu konfirmasi.');
            window.location='$redirect';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menyimpan ke database!');
            window.location='$redirect';
        </script>";
    }
}
?>