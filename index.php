<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ruang | Login</title>
  <link rel="stylesheet" href="./assets/css/figma-style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body class="login-page">

  <div class="logo-top">
    <img src="./assets/img/black white grow logo 1(1).png" alt="Ruang Logo">
  </div>

  <div class="wrapper">
    <form id="loginForm" method="post" action="aksi/proses_login.php">
      <div class="input-box">
        <input type="number" id="nik" placeholder="Masukkan NIK anda (16 digit angka)" name="nik" required>
      </div>

      <div class="input-box">
        <input type="password" id="password" placeholder="Masukkan kata sandi anda" name="password" required>
        <img src="./assets/img/hide-regular-24.png" class="pass-icon" id="pass-icon" onclick="pass()">
      </div>

      <div class="remember-forgot">
        <label><input type="checkbox" id="ingatSaya"> Ingat Saya</label>
        <a href="lupa_password.html">Lupa kata sandi?</a>
      </div>

      <input type="submit" value="Login" name="submit" class="btn">
    </form>
  </div>

</body>

</html>