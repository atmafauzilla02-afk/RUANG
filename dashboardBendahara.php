<?php
session_start();

// Cek login & role bendahara
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'bendahara') {
    header("Location: index.php");
    exit;
}

// Koneksi Database (mysqli)
$koneksi = mysqli_connect("localhost", "root", "", "ruang"); // sesuaikan nama DB
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Total Saldo Kas
$masuk = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran), 0) AS total 
     FROM pembayaran 
     WHERE status_pembayaran = 'lunas'"))[0];

$keluar = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran), 0) AS total 
     FROM pengeluaran 
     WHERE status_persetujuan = 'Disetujui'"))[0];

$saldo = $masuk - $keluar;

// Iuran tertunggak bulan ini
$bulan_eng = ['january','february','march','april','may','june','july','august','september','october','november','december'];
$bulan_ini = $bulan_eng[date('n') - 1];  // ubah angka bulan jadi nama enum
$tahun_ini = date('Y');

$tunggakan = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COUNT(*) AS jumlah 
     FROM pembayaran 
     WHERE status_pembayaran IN ('belum','menunggu')
       AND bulan_pembayaran = '$bulan_ini'
       AND tahun_pembayaran = $tahun_ini"
))[0];

// Pemasukan bulan ini
$pemasukan_bulan_ini = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran), 0) AS total 
     FROM pembayaran 
     WHERE status_pembayaran = 'lunas'
       AND bulan_pembayaran = '$bulan_ini'
       AND tahun_pembayaran = $tahun_ini"
))[0];

// Pengeluaran bulan ini (disetujui)
$pengeluaran_bulan_ini = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran), 0) AS total 
     FROM pengeluaran 
     WHERE status_persetujuan = 'Disetujui'
       AND MONTH(tanggal_pengeluaran) = MONTH(CURDATE())
       AND YEAR(tanggal_pengeluaran) = YEAR(CURDATE())"
))[0];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Ketua RT | Ruang</title>

  <!-- Bootstrap & Font -->
  <link href="./assets/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="dashboard-bg"> 

  <style>
    
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
  <aside class="sidebar" id="sidebar">
    <img src="./assets/img/logo final.png" alt="logo" >
    <hr>
    <ul class="nav flex-column mt-4">
      <li><a href="dashboardBendahara.php" class="nav-link active"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
      <li><a href="iuran.php" class="nav-link"><i class="fa-solid fa-wallet me-2"></i> Iuran</a></li>
      <li><a href="kelola_warga.php" class="nav-link"><i class="fa-solid fa-users me-2"></i>Kelola Warga</a></li>
      <li><a href="Pengajuan.php" class="nav-link"><i class="fa-solid fa-file-import me-2"></i> Pengajuan</a></li>
      <li><a href="laporanBendahara.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i> Laporan</a></li>
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
          <h3 class="fw-bold mb-0">Selamat datang, Bendahara!</h3>
          <p class="text-muted mb-0">Ringkasan keuangan dan iuran</p>
      </div>

      
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
          <h4>Rp<?= number_format($saldo, 0, ',', '.') ?></h4>
          <small class="text-muted">Saldo Realtime</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Iuran Tertunggak</span>
            <i class="fa-solid fa-wallet icon text-warning"></i>
          </div>
          <h4><?= $tunggakan ?> iuran</h4>
          <small class="text-muted">Pada bulan ini</small>
          

        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pemasukan Bulan Ini</span>
            <i class="fa-solid fa-arrow-trend-up icon text-success"></i>
          </div>
          <h4 class="text-success">Rp<?= number_format($pemasukan_bulan_ini, 0, ',', '.') ?></h4>
          <small class="text-muted">Pemasukan bulan September</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pengeluaran Bulan Ini</span>
            <i class="fa-solid fa-arrow-trend-down icon text-danger"></i>
          </div>
          <h4 class="text-danger">Rp<?= number_format($pengeluaran_bulan_ini, 0, ',', '.') ?></h4>
          <small class="text-muted">Pengeluaran bulan September</small>
        </div>
      </div>
    </div>


        <!-- GRAFIK -->
    <div class="chart-card p-4 position-relative bg-white rounded shadow-sm">
      <h5 class="fw-semibold mb-4 text-center">
        Grafik Pemasukan & Pengeluaran Tahun <span id="chartYear" class="text-primary">2025</span>
      </h5>

      <!-- Tombol Navigasi Tahun -->
      <button id="prevYear" class="btn btn-sm btn-outline-secondary position-absolute top-50 start-0 translate-middle-y ms-3 z-3">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
      <button id="nextYear" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-3 z-3">
        <i class="fa-solid fa-chevron-right"></i>
      </button>

      <!-- Wrapper biar tinggi tetap terjaga -->
      <div class="position-relative" style="height: 380px;">
        <canvas id="chartArea"></canvas>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// === Variabel Global ===
let currentYear = new Date().getFullYear();
const ctx = document.getElementById('chartArea').getContext('2d');
let chart;

// === Fungsi Load Data dari API ===
async function loadChart(tahun) {
    try {
        const response = await fetch(`chart_data.php?tahun=${tahun}`);
        const data = await response.json();

        // Update teks tahun
        document.getElementById('chartYear').textContent = tahun;

        // Kalau chart belum dibuat → buat baru, kalau sudah ada → update data
        if (!chart) {
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                    datasets: [
                        {
                            label: 'Pemasukan (Juta)',
                            data: data.pemasukan,
                            backgroundColor: '#9dd6a4',
                            borderColor: '#6fbf7a',
                            borderWidth: 1
                        },
                        {
                            label: 'Pengeluaran (Juta)',
                            data: data.pengeluaran,
                            backgroundColor: '#f78b89',
                            borderColor: '#e74c3c',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        } else {
            // Update data saja
            chart.data.datasets[0].data = data.pemasukan;
            chart.data.datasets[1].data = data.pengeluaran;
            chart.update();
        }
    } catch (err) {
        alert('Gagal memuat grafik: ' + err);
    }
}

// === Tombol Prev & Next Tahun ===
document.getElementById('prevYear').addEventListener('click', () => {
    currentYear--;
    loadChart(currentYear);
});

document.getElementById('nextYear').addEventListener('click', () => {
    currentYear++;
    loadChart(currentYear);
});

// === Load pertama kali saat halaman dibuka ===
loadChart(currentYear);
</script>

<!-- === NOTIFIKASI === -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const iuranBelumLunas = [
    <?php while($n = mysqli_fetch_assoc($notifQuery)) { ?>
    , {
      nama: "<?= $n['jenis_pembayaran'] ?> - <?= $n['bulan_pembayaran'].' '.$n['tahun_pembayaran'] ?>",
      nominal: "Rp<?= number_format($n['nominal_pembayaran'],0,',','.') ?>"
    },
    <?php } ?>
  ];

  const notifBtn = document.getElementById("notifButton");
  const notifList = document.getElementById("notifList");

  if (notifBtn && notifList) {
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
            </div>
          `;
          notifList.appendChild(el);
        });
      }

      const modal = new bootstrap.Modal(document.getElementById("notifModal"));
      modal.show();
    });
  }
});
</script>

<!-- === SIDEBAR TOGGLE === -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.querySelector('.overlay');
  const toggleBtn = document.querySelector('.toggle-btn');

  if (toggleBtn && sidebar && overlay) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('show');
      overlay.classList.remove('active');
    });

    // Tutup sidebar kalau klik item menu
    sidebar.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('active');
      });
    });
  }
});
</script>


<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<!-- <script src="./assets/js/main.js"></script> -->
</main>
</body>
</html>