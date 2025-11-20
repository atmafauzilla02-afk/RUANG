<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Ruang</title>

  <!-- Bootstrap & Font -->
  <link href="./assets/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="dashboard-bg">

<style>
  .info-card {
    
    box-shadow: 0 8px 10px rgba(0, 0, 0, 0.1);
  }

  .mobile-header {
    background-color: #f5c83b !important;
  }

.sidebar img {
  width: 110px;         
  display: block;
  margin: 20px auto 50px auto;  
}

.sidebar .nav {
  margin-top: 0;        
  padding-top: 0;
}
.sidebar hr {
  border: 1px solid #000 ;  
  width: 80%;             
  margin: -40px auto 20px auto;
        
}

.logoMobile {
  width: 80px;         
  display: block;
}
  
  
  
</style>

  <!-- HEADER UNTUK MOBILE -->
<header class="mobile-header d-lg-none">
  <button class="btn toggle-btn"><i class="fa-solid fa-bars"></i></button>
  <img src="assets/img/logo final.png" class="logoMobile"  alt="logo">
</header>

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar" >
    <img src="./assets/img/logo final.png" alt="logo" >
    <hr>
    <ul class="nav flex-column mt">
      <li><a href="dashboard.php" class="nav-link active"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
      <li><a href="status.php" class="nav-link"><i class="fa-solid fa-wallet me-2"></i> Status</a></li>
      <li><a href="pengeluaran.php" class="nav-link"><i class="fa-solid fa-coins me-2"></i> Pengeluaran</a></li>
      <li><a href="laporan.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i> Laporan</a></li>
    </ul>
    <a href="logout.php" class="btn btn-dark w-75 mx-auto mt-auto mb-4">
      <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
    </a>
  </aside>

<div class="overlay"></div>

  <!-- MAIN CONTENT -->
   
  <main id="mainContent">
    <header class="d-flex justify-content-between align-items-center mb-4">
      <div>
          <h3 class="fw-bold mb-0 align-items-center">Selamat datang, Asep Gunawan!</h3>
          <p class="text-muted mb-0">Ringkasan keuangan dan iuran</p>
      </div>

      <div class="notif position-relative" id="notifButton" style="cursor: pointer;">
        <i class="fa-regular fa-bell fa-lg text-dark"></i>
        <span class="notif-dot position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
      </div>
      </div>
    </header>

    <!-- KARTU INFO -->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Total Saldo Kas</span>
            <i class="fa-solid fa-sack-dollar icon"></i>
          </div>
          <h4>Rp32.850.000</h4>
          <small class="text-muted">Saldo Realtime</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Iuran Terbayar</span>
            <i class="fa-solid fa-wallet icon text-warning"></i>
          </div>
          <h4>2/3 Iuran</h4>
          <small class="text-muted">pada bulan ini</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pemasukan Bulan Ini</span>
            <i class="fa-solid fa-arrow-trend-up icon text-success"></i>
          </div>
          <h4 class="text-success">Rp2.957.000</h4>
          <small class="text-muted">Pemasukan bulan September</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pengeluaran Bulan Ini</span>
            <i class="fa-solid fa-arrow-trend-down icon text-danger"></i>
          </div>
          <h4 class="text-danger">Rp780.000</h4>
          <small class="text-muted">Pengeluaran bulan September</small>
        </div>
      </div>
    </div>
      
   


<!-- MODAL NOTIFIKASI -->
<div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #f5c83b, #caa43b);">
        <h5 class="modal-title fw-bold">
          <i class="fa-solid fa-bell me-2"></i>Notifikasi Iuran
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="notifList" class="list-group border-0"></div>
       
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-warning text-dark fw-semibold px-4 rounded-3" data-bs-dismiss="modal">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>


    <!-- GRAFIK -->
     <div class="chart-card p-4 position-relative">
  <h5 class="fw-semibold mb-3 text-center">
    Grafik Pemasukan & Pengeluaran Tahun <span id="chartYear">2025</span>
  </h5>

  <!-- Tombol Navigasi Tahun -->
  <button id="prevYear" class="btn btn-light position-absolute top-50 start-0 translate-middle-y ms-3">
    <i class="fa-solid fa-chevron-left"></i>
  </button>
  <button id="nextYear" class="btn btn-light position-absolute top-50 end-0 translate-middle-y me-3">
    <i class="fa-solid fa-chevron-right"></i>
  </button>

  <canvas id="chartArea" height="100"></canvas>
</div>





<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
  // === Data Dummy Grafik ===
  const chartData = {
    2024: { pemasukan: [6,7,8,9,10,11,12,10,9], pengeluaran: [4,5,5,6,5,6,7,6,5] },
    2025: { pemasukan: [8,10,9,11,14,8,10,9,15], pengeluaran: [5,6,7,8,6,5,7,6,8] },
    2026: { pemasukan: [10,11,12,10,13,14,13,12,15], pengeluaran: [6,6,7,7,8,8,9,8,7] }
  };

  let currentYear = 2025;
  const chartYearEl = document.getElementById('chartYear');
  const ctx = document.getElementById('chartArea').getContext('2d');

  // === Inisialisasi Chart ===
  let chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep'],
      datasets: [
        { label:'Pemasukan', data: chartData[currentYear].pemasukan, backgroundColor:'#9dd6a4' },
        { label:'Pengeluaran', data: chartData[currentYear].pengeluaran, backgroundColor:'#f78b89' }
      ]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom' } } }
  });

  // === Fungsi update chart ===
  function updateChart(year) {
    chart.data.datasets[0].data = chartData[year].pemasukan;
    chart.data.datasets[1].data = chartData[year].pengeluaran;
    chartYearEl.textContent = year;
    chart.update();
  }

  // Tombol navigasi tahun
  document.getElementById('prevYear').addEventListener('click', () => {
    if (chartData[currentYear - 1]) {
      currentYear--;
      updateChart(currentYear);
    } else {
      alert('📅 Data tahun sebelumnya belum tersedia!');
    }
  });

  document.getElementById('nextYear').addEventListener('click', () => {
    if (chartData[currentYear + 1]) {
      currentYear++;
      updateChart(currentYear);
    } else {
      alert('📅 Data tahun berikutnya belum tersedia!');
    }
  });
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  // === Data Dummy Notifikasi ===
  const iuranBelumLunas = [
    { nama: "Iuran Kas - Februari 2025", nominal: "Rp50.000" },
    { nama: "Iuran Keamanan - Mei 2025", nominal: "Rp30.000" },
    { nama: "Iuran Kebersihan - Maret 2025", nominal: "Rp25.000" }
  ];

  const notifBtn = document.getElementById("notifButton");
  const notifList = document.getElementById("notifList");

  // Cek elemen ada
  if (!notifBtn || !notifList) {
    console.error("❌ Elemen notifikasi tidak ditemukan!");
    return;
  }

  // === Event Klik Ikon Notifikasi ===
  notifBtn.addEventListener("click", () => {
    notifList.innerHTML = "";

    if (iuranBelumLunas.length === 0) {
      notifList.innerHTML = `
        <div class="text-center py-3">
          <i class="fa-solid fa-circle-check fa-2x text-success mb-2"></i>
          <p class="fw-semibold mb-0">Semua iuran sudah lunas 🎉</p>
        </div>
      `;
    } else {
      iuranBelumLunas.forEach(item => {
        const el = document.createElement("div");
        el.className = "list-group-item d-flex justify-content-between align-items-center border-0 border-bottom py-3";
        el.innerHTML = `
          <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-warning fs-5"></i>
            <div>
              <p class="mb-0 fw-semibold">${item.nama}</p>
              <small class="text-muted">Nominal: ${item.nominal}</small>
            </div>
        `;
        notifList.appendChild(el); 
      });
    }

    
    const modal = new bootstrap.Modal(document.getElementById("notifModal"));
    modal.show();
  });

});
</script>

<script>
  // === Sidebar Toggle Mobile ===
  const sidebar = document.getElementById('sidebar');
  const overlay = document.querySelector('.overlay');
  const toggleBtn = document.querySelector('.toggle-btn');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('show');
    overlay.classList.toggle('active');
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('show');
    overlay.classList.remove('active');
  });
</script>
<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<!-- <script src="./assets/js/main.js"></script> -->

</body>
</html>
