<?php
session_start();
include '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_pembayaran = $_POST['id_pembayaran'];

    // Tentukan redirect berdasarkan role
    $role = $_SESSION['role'] ?? 'warga'; 
    $redirect = ($role === 'bendahara') ? '../iuran.php' : '../status.php';

    /* Upload Foto Bukti */
    $bukti = null;

    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {

        // folder simpan bukti
        $folder = "../assets/bukti_pembayaran/";  
        
        // ambil extension (jpg, png, dll)
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);

        // nama file baru (unique)
        $filename = uniqid('bukti_') . '.' . $ext;

        // path final
        $fullpath = $folder . $filename;

        // upload ke folder
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $fullpath)) {
            $bukti = $filename; 
        } else {
            $bukti = null;
        }
    } else {
        $bukti = null;
    }

    if (!$bukti) {
        echo "<script>
            alert('Gagal mengupload bukti!');
            window.location='$redirect';
        </script>";
        exit;
    }

    // Update database
    $query = mysqli_query($koneksi, "
        UPDATE pembayaran SET
            bukti_pembayaran = '$bukti',
            status_pembayaran = 'menunggu'
        WHERE id_pembayaran = '$id_pembayaran'
    ");

    if ($query) {
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
