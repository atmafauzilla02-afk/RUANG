<?php
session_start();

include 'koneksi/koneksi.php';

$bulanIndo = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
$bulan_ini = $bulanIndo[date('n')-1];
$tahun_ini = date('Y');

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'warga') {
    header("Location: index.php");
    exit;
}

$bulan_eng = ['january','february','march','april','may','june','july','august','september','october','november','december'];
$bulan_ini = $bulan_eng[date('n') - 1];
$tahun_ini = date('Y');

$id_warga = $_SESSION['id_warga'];
$tahun_ini = date('Y');
$total_stmt = mysqli_prepare($koneksi, 
    "SELECT COUNT(*) FROM pembayaran WHERE id_warga = ? AND tahun_pembayaran = ?");
mysqli_stmt_bind_param($total_stmt, "ii", $_SESSION['id_warga'], $tahun_ini);
mysqli_stmt_execute($total_stmt);
mysqli_stmt_bind_result($total_stmt, $total_harus_bayar);
mysqli_stmt_fetch($total_stmt);
mysqli_stmt_close($total_stmt);

$lunas_stmt = mysqli_prepare($koneksi, 
    "SELECT COUNT(*) FROM pembayaran WHERE id_warga = ? AND tahun_pembayaran = ? AND status_pembayaran = 'lunas'");
mysqli_stmt_bind_param($lunas_stmt, "ii", $_SESSION['id_warga'], $tahun_ini);
mysqli_stmt_execute($lunas_stmt);
mysqli_stmt_bind_result($lunas_stmt, $sudah_bayar);
mysqli_stmt_fetch($lunas_stmt);
mysqli_stmt_close($lunas_stmt);

$belum_bayar = $total_harus_bayar - $sudah_bayar;

$iuran_terbayar_text = "$sudah_bayar / $total_harus_bayar Iuran";
$iuran_belum_text = $belum_bayar > 0 
    ? "$belum_bayar iuran belum dibayar tahun ini" 
    : "Semua iuran sudah lunas tahun ini!";

$masuk = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran), 0) AS total 
     FROM pembayaran 
     WHERE status_pembayaran = 'lunas'"))[0] ?? 0;

$keluar = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran), 0) AS total 
     FROM pengeluaran 
     WHERE status_persetujuan = 'Disetujui'"))[0] ?? 0;

$saldo = $masuk - $keluar;

$tunggakan = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COUNT(*) AS jumlah 
     FROM pembayaran 
     WHERE id_warga = '{$_SESSION['id_warga']}'
       AND bulan_pembayaran = '$bulan_ini'
       AND tahun_pembayaran = '$tahun_ini'
       AND status_pembayaran IN ('belum','menunggu')"
))[0] ?? 0;

$pemasukan_bulan_ini = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran), 0) AS total 
     FROM pembayaran 
     WHERE status_pembayaran = 'lunas'
       AND bulan_pembayaran = '$bulan_ini'
       AND tahun_pembayaran = '$tahun_ini'"
))[0] ?? 0;

$pengeluaran_bulan_ini = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran), 0) AS total 
     FROM pengeluaran 
     WHERE status_persetujuan = 'Disetujui'
       AND MONTH(tanggal_pengeluaran) = MONTH(CURDATE())
       AND YEAR(tanggal_pengeluaran) = YEAR(CURDATE())"
))[0] ?? 0;
?>

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
          <h3 class="fw-bold mb-0 align-items-center">Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Warga') ?>!</h3>
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
          <h4>Rp<?= number_format($saldo, 0, ',', '.') ?></h4>
          <small class="text-muted">Saldo Realtime</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span>Iuran Tertunggak Tahun Ini</span>
            <i class="fa-solid fa-exclamation-triangle icon text-danger"></i>
          </div>
          
          <div class="mb-2">
            <select id="filterTunggakan" class="form-select form-select-sm">
              <option value="">Semua Bulan</option>
              <option value="1">Januari</option>
              <option value="2">Februari</option>
              <option value="3">Maret</option>
              <option value="4">April</option>
              <option value="5">Mei</option>
              <option value="6">Juni</option>
              <option value="7">Juli</option>
              <option value="8">Agustus</option>
              <option value="9">September</option>
              <option value="10">Oktober</option>
              <option value="11">November</option>
              <option value="12">Desember</option>
            </select>
          </div>

          <h4 class="text-danger fw-bold"><span id="jumlahTunggakan"><?= $belum_bayar ?></span> iuran</h4>
          <small class="text-muted" id="infoTunggakan">
            <?= $belum_bayar > 0 ? "$belum_bayar iuran belum dibayar tahun $tahun_ini" : "Semua iuran sudah lunas tahun ini!" ?>
          </small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pemasukan Tahun Ini</span>
            <i class="fa-solid fa-arrow-trend-up icon text-success"></i>
          </div>
          <h4 class="text-success">Rp<?= number_format($masuk, 0, ',', '.') ?></h4>
          <small class="text-muted">Total iuran lunas tahun <?= $tahun_ini ?></small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pengeluaran Tahun Ini</span>
            <i class="fa-solid fa-arrow-trend-down icon text-danger"></i>
          </div>
          <h4 class="text-danger">Rp<?= number_format($keluar, 0, ',', '.') ?></h4>
          <small class="text-muted">Total pengeluaran disetujui tahun <?= $tahun_ini ?></small>
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

<script>
const REALTIME_URL = 'dashboard_realtime.php';

function updateKas() {
    fetch(REALTIME_URL)
        .then(res => res.json())
        .then(data => {
            // Sesuaikan ID ini dengan elemen HTML kamu
            document.getElementById('total-saldo')?.innerText = data.saldo;
            document.getElementById('pemasukan-bulan')?.innerText = data.pemasukan_bulan;
            document.getElementById('pengeluaran-bulan')?.innerText = data.pengeluaran_bulan;
            document.getElementById('waktu-update')?.innerText = 'Update: ' + data.updated_at;

            // Jika kamu pakai Chart.js untuk grafik
            if (typeof updateChart === 'function') {
                updateChart(data); // kamu buat fungsi ini sendiri kalau perlu
            }
        })
        .catch(err => console.log('Gagal update kas:', err));
}

updateKas();

setInterval(updateKas, 8000);
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

<script>
document.getElementById('filterTunggakan').addEventListener('change', function() {
    const bulan = this.value;
    const id_warga = <?= $_SESSION['id_warga'] ?>;
    const tahun = <?= date('Y') ?>;

    if (!bulan) {
        document.getElementById('jumlahTunggakan').textContent = '<?= $belum_bayar ?>';
        document.getElementById('infoTunggakan').textContent = '<?= $belum_bayar > 0 ? "$belum_bayar iuran belum dibayar tahun ini" : "Semua iuran sudah lunas tahun ini!" ?>';
        return;
    }

    fetch(`get_tunggakan.php?id_warga=${id_warga}&bulan=${bulan}&tahun=${tahun}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('jumlahTunggakan').textContent = data.jumlah;
            document.getElementById('infoTunggakan').textContent = 
                data.jumlah > 0 
                    ? `${data.jumlah} iuran belum dibayar bulan ini` 
                    : `Semua iuran bulan ini sudah lunas!`;
        });
});
</script>

<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<!-- <script src="./assets/js/main.js"></script> -->

</body>
</html>
