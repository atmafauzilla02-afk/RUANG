// === Data Dummy User ===
const users = [
  { nik: "1234567890123456", password: "warga123", role: "WARGA", nama: "Asep Surecep", phone: "0895629499558" },
  { nik: "2345678901234567", password: "bendahara123", role: "BENDAHARA", nama: "Siti Rahayu", phone: "081234567890" },
  { nik: "3456789012345678", password: "rt123", role: "RT", nama: "Pak Darto", phone: "087812345678" }
];

let currentUser = null;
let generatedOTP = null;

// === Step 1: Kirim OTP ===
document.getElementById("formStep1").addEventListener("submit", function(e) {
  e.preventDefault();
  const phone = document.getElementById("phoneNumber").value.trim();
  const found = users.find(u => u.phone === phone);

  if (!found) {
    alert("Nomor HP tidak ditemukan dalam sistem!");
    return;
  }

  currentUser = found;
  generatedOTP = Math.floor(100000 + Math.random() * 900000); 
  alert(`Kode OTP Anda (simulasi): ${generatedOTP}`);

  // Tampilkan form verifikasi OTP
  document.getElementById("formStep1").style.display = "none";
  document.getElementById("formStep2").style.display = "block";
});

// === Step 2: Verifikasi OTP ===
document.getElementById("formStep2").addEventListener("submit", function(e) {
  e.preventDefault();
  const otpInput = document.getElementById("otpInput").value.trim();

  if (otpInput === generatedOTP.toString()) {
    alert("OTP benar! Silakan buat password baru.");
    document.getElementById("formStep2").style.display = "none";
    document.getElementById("formStep3").style.display = "block";
  } else {
    alert("Kode OTP salah!");
  }
});

// === Step 3: Simpan Password Baru ===
document.getElementById("formStep3").addEventListener("submit", function(e) {
  e.preventDefault();
  const newPass = document.getElementById("newPassword").value.trim();
  const confirmPass = document.getElementById("confirmPassword").value.trim();

  if (newPass !== confirmPass) {
    alert("Konfirmasi password tidak sama!");
    return;
  }

  // Update password dummy
  currentUser.password = newPass;
  alert("Password berhasil diubah! Silakan login kembali.");
  window.location.href = "index.html";
});
