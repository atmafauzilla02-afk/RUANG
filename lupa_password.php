<?php
session_start();
include 'koneksi/koneksi.php';

$admin_phone = '6285806231986';

function generateOTP() {
    return rand(100000, 999999);
}

$step = $_POST['step'] ?? '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === '1') {
        $nik = trim($_POST['nik'] ?? '');

        if (empty($nik)) {
            $error = 'NIK harus diisi!';
        } else {
            $stmt = $koneksi->prepare("SELECT id_pengguna, no_telp FROM pengguna WHERE nik = ?");
            $stmt->bind_param("s", $nik);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $error = 'NIK tidak terdaftar!';
            } else {
                $user = $result->fetch_assoc();
                $id_pengguna = $user['id_pengguna'];
                $no_telp = $user['no_telp'];

                $otp = generateOTP();

                $_SESSION['reset_otp'] = $otp;
                $_SESSION['reset_time'] = time();
                $_SESSION['reset_id_pengguna'] = $id_pengguna;
                $_SESSION['reset_nik'] = $nik;

                $message = "Kode OTP untuk reset password: $otp\nNIK: $nik\nNomor HP: $no_telp\nJangan bagikan kode ini!";
                $encoded_message = urlencode($message);
                $wa_link = "https://wa.me/$admin_phone?text=$encoded_message";

                $success_step1 = true;
                $wa_link_generated = $wa_link;
            }
            $stmt->close();
        }

    } elseif ($step === '2') {
        $user_otp = trim($_POST['otp'] ?? '');

        if (empty($user_otp)) {
            $error = 'OTP harus diisi!';
        } elseif (!isset($_SESSION['reset_otp']) || $_SESSION['reset_otp'] != $user_otp) {
            $error = 'OTP salah!';
        } elseif (time() - $_SESSION['reset_time'] > 300) {
            $error = 'OTP telah kadaluarsa!';
            unset($_SESSION['reset_otp']);
        } else {
            $success_step2 = true;
        }

    } elseif ($step === '3') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($new_password) || strlen($new_password) < 6) {
            $error = 'Password baru minimal 6 karakter!';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Konfirmasi password tidak sama!';
        } elseif (!isset($_SESSION['reset_id_pengguna'])) {
            $error = 'Sesi reset tidak valid!';
        } else {
            $hash_baru = password_hash($new_password, PASSWORD_DEFAULT);

            $id_pengguna = $_SESSION['reset_id_pengguna'];
            $stmt = $koneksi->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
            $stmt->bind_param("si", $hash_baru, $id_pengguna);

            if ($stmt->execute()) {
                unset($_SESSION['reset_otp'], $_SESSION['reset_time'], $_SESSION['reset_id_pengguna'], $_SESSION['reset_nik']);

                echo "<script>alert('Password berhasil diubah! Silakan login kembali.'); window.location.href = 'index.php';</script>";
                exit;
            } else {
                $error = 'Gagal mengubah password!';
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Kata Sandi | Ruang</title>
  <link rel="stylesheet" href="./assets/css/figma-style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="login-page">

  <div class="logo-top">
    <img src="./assets/img/black white grow logo 1(1).png" alt="Ruang Logo">
  </div>

  <div class="wrapper" id="forgotContainer">
    <?php if (isset($error)): ?>
      <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (!isset($success_step1) && !isset($success_step2)): ?>
      <!-- Step 1: Masukkan NIK (asumsi pakai NIK seperti login) -->
      <form method="POST">
        <input type="hidden" name="step" value="1">
        <h3>Lupa Kata Sandi</h3>
        <p>Masukkan NIK yang terdaftar untuk menerima kode OTP.</p>
        <div class="input-box">
          <input type="text" name="nik" placeholder="Masukkan NIK" required>
        </div>
        <button type="submit" class="btn">Kirim OTP</button>
        <a href="index.php" class="back-link">Kembali ke Login</a>
      </form>
    <?php elseif (isset($success_step1) && !isset($success_step2)): ?>
      <!-- Step 2: Instruksi Kirim OTP via wa.me dan Verifikasi -->
      <p>Klik link di bawah untuk kirim OTP ke admin via WhatsApp. Setelah dikirim, masukkan OTP di sini.</p>
      <a href="<?php echo $wa_link_generated; ?>" target="_blank">
        <button style="background:#25D366; color:white; padding:10px; border:none; border-radius:5px;">
          Kirim OTP via WhatsApp
        </button>
      </a>
      <form method="POST" style="margin-top:20px;">
        <input type="hidden" name="step" value="2">
        <h3>Verifikasi OTP</h3>
        <div class="input-box">
          <input type="text" name="otp" placeholder="Masukkan kode OTP" required>
        </div>
        <button type="submit" class="btn">Verifikasi</button>
      </form>
    <?php elseif (isset($success_step2)): ?>
      <!-- Step 3: Ubah Password -->
      <form method="POST">
        <input type="hidden" name="step" value="3">
        <h3>Atur Ulang Kata Sandi</h3>
        <p>Masukkan kata sandi baru Anda.</p>
        <div class="input-box">
          <input type="password" name="new_password" placeholder="Kata sandi baru" required>
        </div>
        <div class="input-box">
          <input type="password" name="confirm_password" placeholder="Konfirmasi kata sandi" required>
        </div>
        <button type="submit" class="btn">Simpan Password</button>
      </form>
    <?php endif; ?>
  </div>

</body>
</html>