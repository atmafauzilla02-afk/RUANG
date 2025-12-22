<?php
session_start();
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'ketua') {
    header("Location: index.php");
    exit;
}
include 'koneksi/koneksi.php';

$tahun_filter = date('Y');

$pemasukan_total = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran),0) FROM pembayaran WHERE status_pembayaran='lunas'"
))[0] ?? 0;

$pengeluaran_total = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran),0) FROM pengeluaran WHERE status_persetujuan='Disetujui'"
))[0] ?? 0;

$saldo = $pemasukan_total - $pengeluaran_total;

$pemasukan_tahun = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran),0) FROM pembayaran WHERE status_pembayaran='lunas' AND tahun_pembayaran='$tahun_filter'"
))[0] ?? 0;

$pengeluaran_tahun = mysqli_fetch_array(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran),0) FROM pengeluaran WHERE status_persetujuan='Disetujui' AND YEAR(tanggal_pengeluaran)='$tahun_filter'"
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

<body class="dashboard-bg">

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
      <li><a href="dashboardRT.php" class="nav-link active"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
      <li><a href="persetujuan.php" class="nav-link"><i class="fa-solid fa-file-signature  me-2"></i> Persetujuan</a></li>
      <li><a href="pengeluaranRT.php" class="nav-link"><i class="fa-solid fa-coins me-2"></i> Pengeluaran</a></li>
      <li><a href="laporanRT.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i> Laporan</a></li>
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
          <h3 class="fw-bold mb-0">Selamat datang, Ketua RT!</h3>
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
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span>Iuran Tertunggak Tahun <?= $tahun_filter ?></span>
            <i class="fa-solid fa-wallet icon text-warning"></i>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <select id="filterBulan" class="form-select form-select-sm">
                <option value="">Semua Bulan</option>
                <?php
                $bulanIndo = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
                foreach($bulanIndo as $b){
                  echo "<option value='$b'>".ucfirst($b)."</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-6">
              <select id="filterJenis" class="form-select form-select-sm">
                <option value="">Semua Jenis</option>
                <option value="kas">Kas</option>
                <option value="keamanan">Keamanan</option>
                <option value="kebersihan">Kebersihan</option>
              </select>
            </div>
          </div>

          <div class="text-center mt-3">
            <h3 id="jumlahTunggakan" class="fw-bold mb-0 text-warning fs-4">Memuat...</h3>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pemasukan Tahun <?= $tahun_filter ?></span>
            <i class="fa-solid fa-arrow-trend-up icon text-success"></i>
          </div>
          <h4 class="text-success">Rp<?= number_format($pemasukan_tahun, 0, ',', '.') ?></h4>
          <small class="text-muted">Total pemasukan tahun ini</small>
        </div>
      </div>

      <div class="col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pengeluaran Tahun <?= $tahun_filter ?></span>
            <i class="fa-solid fa-arrow-trend-down icon text-danger"></i>
          </div>
          <h4 class="text-danger">Rp<?= number_format($pengeluaran_tahun, 0, ',', '.') ?></h4>
          <small class="text-muted">Total pengeluaran tahun ini</small>
        </div>
      </div>

        <!-- GRAFIK TAHUN INI SAJA -->
    <div class="chart-card p-4 bg-white rounded shadow-sm">
      <h5 class="fw-semibold mb-4 text-center">
        Grafik Pemasukan & Pengeluaran Tahun <span class="text-primary"><?= date('Y') ?></span>
      </h5>

      <div class="position-relative" style="height:380px;">
        <canvas id="chartArea"></canvas>
      </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(async function() {
    const tahun = <?= date('Y') ?>;
    let chart = null;

    try {
        const res = await fetch(`chart_data.php?tahun=${tahun}`);
        const data = await res.json();

        const totalMasuk = data.pemasukan.reduce((a,b) => a + b, 0);
        const totalKeluar = data.pengeluaran.reduce((a,b) => a + b, 0);

        if (totalMasuk === 0 && totalKeluar === 0) {
            document.querySelector('.chart-card .position-relative').innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-chart-bar fa-3x mb-3 opacity-50"></i>
                    <p class="mb-0 fw-medium fs-5">Belum ada data pemasukan atau pengeluaran di tahun ${tahun}</p>
                </div>`;
            return;
        }

        const ctx = document.getElementById('chartArea').getContext('2d');

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                datasets: [
                    {
                        label: 'Pemasukan (Rp)',
                        data: data.pemasukan,
                        backgroundColor: '#27ae60',
                        borderColor: '#1e8449',
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Pengeluaran (Rp)',
                        data: data.pengeluaran,
                        backgroundColor: '#e74c3c',
                        borderColor: '#c0392b',
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': Rp' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => 'Rp' + v.toLocaleString('id-ID') },
                        title: { display: true, text: '' }
                    },
                    x: { title: { display: true, text: '' } }
                }
            }
        });

    } catch (err) {
        console.error('Gagal load grafik:', err);
        document.querySelector('.chart-card .position-relative').innerHTML = `
            <div class="text-center py-5 text-danger">
                <i class="fa-solid fa-triangle-exclamation fa-3x mb-3"></i>
                <p>Gagal memuat grafik</p>
            </div>`;
    }
})();
</script>

<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

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
        notifList.appendChild(el); //
      });
    }

    // Tampilkan modal
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

<script>
async function loadTunggakan() {
    const bulan = document.getElementById('filterBulan').value;
    const jenis = document.getElementById('filterJenis').value;
    const tahun = <?= $tahun_filter ?>;

    try {
        const response = await fetch(`aksi/get_tunggakan.php?tahun=${tahun}&bulan=${bulan}&jenis=${jenis}`);
        
        if (!response.ok) throw new Error("HTTP " + response.status);
        
        const data = await response.json();
        const el = document.getElementById('jumlahTunggakan');

        if (data.jumlah == 0) {
            el.textContent = "Semua Lunas!";
            el.className = "fw-bold mb-0 text-success fs-4";
        } else {
            el.textContent = data.jumlah + " iuran";
            el.className = "fw-bold mb-0 text-warning fs-4";
        }
    } catch (err) {
        console.error("Error load tunggakan:", err);
        document.getElementById('jumlahTunggakan').textContent = "Gagal";
    }
}

document.addEventListener('DOMContentLoaded', loadTunggakan);
document.getElementById('filterBulan').addEventListener('change', loadTunggakan);
document.getElementById('filterJenis').addEventListener('change', loadTunggakan);
</script>

</body>
</html>
