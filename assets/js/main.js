// === UTILITAS LOCALSTORAGE ===
function saveData(key, data) {
  localStorage.setItem(key, JSON.stringify(data));
}
function loadData(key) {
  const data = localStorage.getItem(key);
  return data ? JSON.parse(data) : null;
}

// === DATA DUMMY USER ===
const users = [
  { nik: "1234567890123456", password: "warga123", role: "WARGA", nama: "Asep Surecep", phone: "0895629499558" },
  { nik: "2345678901234567", password: "bendahara123", role: "BENDAHARA", nama: "Siti Rahayu", phone: "081234567890" },
  { nik: "3456789012345678", password: "rt123", role: "RT", nama: "Pak Darto", phone: "087812345678" }
];


// === EVENT SAAT HALAMAN DIMUAT ===
document.addEventListener("DOMContentLoaded", () => {
  const currentPath = location.pathname.split("/").pop();

  // Jika di halaman login
  if (currentPath === "index.html" || currentPath === "") {
    const form = document.getElementById("loginForm");
    if (form) {
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        const nik = document.getElementById("nik").value.trim();
        const pass = document.getElementById("password").value.trim();

        const found = users.find((u) => u.nik === nik && u.password === pass);
        if (!found) {
          alert("NIK atau password salah!");
          return;
        }

        // Simpan data login
        localStorage.setItem(
          "currentUser",
          JSON.stringify({ username: found.nama, role: found.role })
        );

        // Redirect ke dashboard sesuai role
        if (found.role === "WARGA") location.href = "dashboard.html";
        else if (found.role === "BENDAHARA") location.href = "dashboardBendahara.html";
        else if (found.role === "RT") location.href = "dashboardRT.html";
      });
    }

    return; // stop di halaman login
  }

  // ==== CEK LOGIN DI HALAMAN LAIN ====
  const currentUser = loadData("currentUser");
  if (!currentUser) {
    location.href = "index.html";
    return;
  }

  // ==== TAMPILKAN NAMA USER ====
  const welcome = document.getElementById("welcomeTitle");
  if (welcome) {
    welcome.textContent = `Selamat datang, ${currentUser.username}!`;
  }

  // ==== RENDER SIDEBAR SESUAI ROLE ====
  renderSidebar(currentUser.role);

  // ==== FUNGSI LOGOUT ====
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
      localStorage.removeItem("currentUser");
      location.href = "index.html";
    });
  }
});

// === TAMPIL/SEMBUNYI PASSWORD ===
function pass() {
  const passInput = document.getElementById("password");
  const passIcon = document.getElementById("pass-icon");
  if (!passInput || !passIcon) return;

  if (passInput.type === "password") {
    passInput.type = "text";
    passIcon.src = "./assets/img/show-regular-24.png";
  } else {
    passInput.type = "password";
    passIcon.src = "./assets/img/hide-regular-24.png";
  }
}

// === RENDER SIDEBAR SESUAI ROLE ===
function renderSidebar(role) {
  const sidebarContainer = document.getElementById("sidebarContainer");
  if (!sidebarContainer) return;

  const menus = {
    WARGA: [
      { href: "dashboard.html", icon: "fa-chart-line", label: "Dashboard" },
      { href: "status.html", icon: "fa-wallet", label: "Status" },
      { href: "pengeluaran.html", icon: "fa-coins", label: "Pengeluaran" },
      { href: "laporan.html", icon: "fa-file-lines", label: "Laporan" },
    ],
    RT: [
      { href: "dashboardRT.html", icon: "fa-chart-line", label: "Dashboard" },
      { href: "persetujuan.html", icon: "fa-check-double", label: "Persetujuan" },
      { href: "pengeluaran.html", icon: "fa-coins", label: "Pengeluaran" },
      { href: "laporan.html", icon: "fa-file-lines", label: "Laporan" },
    ],
    BENDAHARA: [
      { href: "dashboardBendahara.html", icon: "fa-chart-line", label: "Dashboard" },
      { href: "iuran.html", icon: "fa-wallet", label: "Iuran" },
      { href: "pengajuan.html", icon: "fa-coins", label: "Pengajuan" },
      { href: "laporanBendahara.html", icon: "fa-file-lines", label: "Laporan" },
      { href: "kelola_warga.html", icon: "fa-users", label: "Kelola Warga" },
    ],
  };

  const list = menus[role] || [];
  sidebarContainer.innerHTML = `
    <div class="brand">
      <h4>Ruang</h4>
      <p class="small">Rekapitulasi Uang</p>
      <div class="small mt-2 text-muted">Role: ${role}</div>
    </div>
    <nav class="nav flex-column">
      ${list
        .map(
          (m) => `
        <a class="nav-link ${location.pathname.endsWith(m.href) ? "active" : ""}" href="${m.href}">
          <i class="fa-solid ${m.icon} me-2"></i>${m.label}
        </a>`
        )
        .join("")}
    </nav>
    <div class="mt-auto">
      <button id="logoutBtn" class="btn btn-dark w-100">Sign Out</button>
    </div>
  `;
}

// ==== SIMULASI LUPA PASSWORD ====
const forgotLink = document.getElementById("forgotLink");
const forgotModal = document.getElementById("forgotModal");
const closeForgotModal = document.getElementById("closeForgotModal");

const sendOtpBtn = document.getElementById("sendOtpBtn");
const verifyOtpBtn = document.getElementById("verifyOtpBtn");
const resetPasswordBtn = document.getElementById("resetPasswordBtn");

let generatedOtp = null;
let otpUser = null;

if (forgotLink) {
  forgotLink.addEventListener("click", (e) => {
    e.preventDefault();
    forgotModal.style.display = "flex";
  });
}

if (closeForgotModal) {
  closeForgotModal.addEventListener("click", () => {
    forgotModal.style.display = "none";
    document.getElementById("forgotStep1").style.display = "block";
    document.getElementById("forgotStep2").style.display = "none";
    document.getElementById("forgotStep3").style.display = "none";
  });
}

// STEP 1: Kirim OTP
if (sendOtpBtn) {
  sendOtpBtn.addEventListener("click", () => {
    const nik = document.getElementById("forgotNik").value.trim();
    const phone = document.getElementById("forgotPhone").value.trim();

    const user = users.find(u => u.nik === nik);
    if (!user) return alert("NIK tidak ditemukan!");
    if (!phone) return alert("Masukkan nomor HP!");

    generatedOtp = Math.floor(100000 + Math.random() * 900000);
    otpUser = user;

    alert(`(SIMULASI) Kode OTP dikirim ke ${phone}: ${generatedOtp}`);

    document.getElementById("forgotStep1").style.display = "none";
    document.getElementById("forgotStep2").style.display = "block";
  });
}

// STEP 2: Verifikasi OTP
if (verifyOtpBtn) {
  verifyOtpBtn.addEventListener("click", () => {
    const inputOtp = document.getElementById("otpInput").value.trim();
    if (inputOtp == generatedOtp) {
      document.getElementById("forgotStep2").style.display = "none";
      document.getElementById("forgotStep3").style.display = "block";
    } else {
      alert("Kode OTP salah!");
    }
  });
}

// STEP 3: Reset Password
if (resetPasswordBtn) {
  resetPasswordBtn.addEventListener("click", () => {
    const newPass = document.getElementById("newPassword").value.trim();
    if (!newPass) return alert("Masukkan password baru!");

    otpUser.password = newPass;
    alert("Password berhasil direset! Silakan login kembali.");

    forgotModal.style.display = "none";
  });
}

// === LUPA PASSWORD PAGE ===
document.addEventListener("DOMContentLoaded", () => {
  if (location.pathname.endsWith("lupa_password.html")) {
    const form = document.getElementById("forgotForm");
    const phoneInput = document.getElementById("phone");
    const otpBox = document.getElementById("otpBox");
    const otpInput = document.getElementById("otpInput");
    const newPassBox = document.getElementById("newPassBox");
    const newPassInput = document.getElementById("newPassword");
    const submitBtn = document.getElementById("submitBtn");

    let generatedOTP = null;
    let verifiedUser = null;

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      // STEP 1: KIRIM OTP
      if (!otpBox.classList.contains("show") && !newPassBox.classList.contains("show")) {
        const phone = phoneInput.value.trim();
        const found = users.find((u) => u.telp === phone);
        if (!found) {
          alert("Nomor HP tidak ditemukan di sistem!");
          return;
        }

        verifiedUser = found;
        generatedOTP = Math.floor(100000 + Math.random() * 900000).toString();
        alert(`(Simulasi) Kode OTP Anda: ${generatedOTP}`);

        otpBox.classList.remove("d-none");
        otpBox.classList.add("show");
        submitBtn.textContent = "Verifikasi OTP";
        return;
      }

      // STEP 2: VERIFIKASI OTP
      if (otpBox.classList.contains("show") && !newPassBox.classList.contains("show")) {
        if (otpInput.value.trim() !== generatedOTP) {
          alert("Kode OTP salah!");
          return;
        }

        otpBox.classList.remove("show");
        newPassBox.classList.remove("d-none");
        newPassBox.classList.add("show");
        submitBtn.textContent = "Ubah Password";
        return;
      }

      // STEP 3: UBAH PASSWORD
      if (newPassBox.classList.contains("show")) {
        const newPass = newPassInput.value.trim();
        if (newPass.length < 4) {
          alert("Password minimal 4 karakter!");
          return;
        }

        // Update password di data dummy
        verifiedUser.password = newPass;
        alert("Password berhasil diubah! Silakan login kembali.");
        location.href = "index.html";
      }
    });
  }
});

