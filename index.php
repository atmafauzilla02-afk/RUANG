<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ruang | Login</title>
  <link rel="stylesheet" href="./assets/css/figma-style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  
  <style>
    .input-box {
      position: relative;
      margin-bottom: 26px;
      text-align: left;
    }
    
    .input-label {
      display: block;
      font-size: 15.5px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 9px;
      text-align: left;
    }
    
    .input-box input {
      width: 100%;
      text-align: left;
      font-size: 15px;
      padding: 12px 15px; /* biar lebih nyaman */
    }
    
    .pass-icon {
      position: absolute;
      right: 15px;
      top: 68%;                     /* DITURUNKAN SEDIKIT LAGI (dari 65% jadi 68%) */
      transform: translateY(-50%);
      cursor: pointer;
      width: 26px;
      height: 26px;
      opacity: 0.65;
      transition: opacity 0.2s;
    }
    .pass-icon:hover {
      opacity: 1;
    }
  </style>
</head>

<body class="login-page">

  <div class="logo-top">
    <img src="./assets/img/black white grow logo 1(1).png" alt="Ruang Logo">
  </div>

  <div class="wrapper">
    <form id="loginForm" method="post" action="aksi/proses_login.php">

      <div class="input-box">
        <label class="input-label">Nomor Induk Kependudukan (NIK)</label>
        <input type="number" id="nik" name="nik" placeholder="Masukkan NIK anda (16 digit angka)" required maxlength="16">
      </div>

      <div class="input-box">
        <label class="input-label">Kata Sandi</label>
        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi anda" required>
        <img src="./assets/img/hide-regular-24.png" 
             class="pass-icon" 
             id="pass-icon" 
             onclick="togglePassword()">
      </div>

      <input type="submit" value="Login" name="submit" class="btn">
    </form>
  </div>

  <script>
    function togglePassword() {
      const field = document.getElementById('password');
      const icon  = document.getElementById('pass-icon');

      if (field.type === 'password') {
        field.type = 'text';
        icon.src = './assets/img/show-regular-24.png';
      } else {
        field.type = 'password';
        icon.src = './assets/img/hide-regular-24.png';
      }
    }
  </script>

</body>
</html>