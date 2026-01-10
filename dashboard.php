<?php
session_start();

include 'koneksi/koneksi.php';

$bulanIndo = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];
$bulan_ini = $bulanIndo[date('n')-1];
$tahun_ini = date('Y');

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

$profile_query = mysqli_query($koneksi, "
    SELECT 
        pg.nama, 
        pg.nik, 
        pg.no_telp, 
        pg.alamat 
    FROM warga w 
    JOIN pengguna pg ON w.id_pengguna = pg.id_pengguna 
    WHERE w.id_warga = '{$_SESSION['id_warga']}'
");

if (!$profile_query) {
    die("Error query profile: " . mysqli_error($koneksi));
}

$profile_data = mysqli_fetch_assoc($profile_query);

if (!$profile_data) {
    $profile_data = [
        'nama' => $_SESSION['nama'] ?? 'Tidak Diketahui',
        'nik' => 'Tidak Tersedia',
        'no_telp' => 'Tidak Tersedia',
        'alamat' => 'Tidak Tersedia'
    ];
}
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

.notif-item {
  padding: 16px 20px;
  border-bottom: 1px solid #e9ecef;
  transition: background 0.2s;
}

.notif-item:hover {
  background-color: #f8f9fa;
}

.notif-item.unread {
  background-color: #fff8e1;
  border-left: 4px solid #f5c83b;
}

.notif-item:last-child {
  border-bottom: none;
}

.notif-title {
  font-weight: 600;
  color: #333;
  margin-bottom: 6px;
  font-size: 15px;
}

.notif-content {
  color: #666;
  font-size: 14px;
  line-height: 1.5;
  margin-bottom: 8px;
}

.notif-date {
  color: #999;
  font-size: 12px;
}

.notif-badge-unread {
  display: inline-block;
  background: #f5c83b;
  color: #333;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 10px;
  margin-left: 8px;
}

.notif-empty {
  text-align: center;
  padding: 40px 20px;
  color: #999;
}

.notif-empty i {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.3;
}

.user-profile {
  transition: transform 0.2s;
}

.user-profile:hover {
  transform: scale(1.1);
}

.input-group .btn-outline-secondary:hover {
  background-color: #f5c83b;
  border-color: #f5c83b;
  color: #fff;
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

      <div class="d-flex gap-3 align-items-center">
        <!-- Tombol Notifikasi -->
        <div class="notif position-relative" id="notifButton" style="cursor: pointer;">
          <i class="fa-regular fa-bell fa-lg text-dark"></i>
          <span class="notif-dot position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" 
                id="notifBadge" style="display: none;"></span>
        </div>

        <!-- Tombol User Profile -->
        <div class="user-profile position-relative" id="userButton" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#profileModal">
          <i class="fa-regular fa-user fa-lg text-dark"></i>
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
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
          <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #f5c83b, #caa43b);">
            <h5 class="modal-title fw-bold">
              <i class="fa-solid fa-bell me-2"></i>Notifikasi Iuran
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-0" style="max-height: 500px;">
            <div id="notifList"></div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-warning text-dark fw-semibold px-4 rounded-3" data-bs-dismiss="modal">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL PROFILE USER -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
          <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #f5c83b, #caa43b);">
            <h5 class="modal-title fw-bold">
              <i class="fa-solid fa-user-circle me-2"></i>Profil Pengguna
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            
            <!-- Info Pengguna -->
            <div class="mb-4">
              <div class="text-center mb-3">
                <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center" 
                    style="width: 80px; height: 80px;">
                  <i class="fa-solid fa-user fa-3x text-white"></i>
                </div>
              </div>

              <table class="table table-borderless">
                <tr>
                  <td width="40%" class="text-muted"><i class="fa-solid fa-user me-2"></i>Nama</td>
                  <td class="fw-semibold">: <?= htmlspecialchars($profile_data['nama'] ?? '-') ?></td>
                </tr>
                <tr>
                  <td class="text-muted"><i class="fa-solid fa-id-card me-2"></i>NIK</td>
                  <td class="fw-semibold">: <?= htmlspecialchars($profile_data['nik'] ?? '-') ?></td>
                </tr>
                <tr>
                  <td class="text-muted"><i class="fa-solid fa-phone me-2"></i>No. Telepon</td>
                  <td class="fw-semibold">: <?= htmlspecialchars($profile_data['no_telp'] ?? '-') ?></td>
                </tr>
                <tr>
                  <td class="text-muted"><i class="fa-solid fa-home me-2"></i>Alamat</td>
                  <td class="fw-semibold">: <?= htmlspecialchars($profile_data['alamat'] ?? '-') ?></td>
                </tr>
              </table>
            </div>

            <hr>

            <!-- Form Ganti Password -->
            <div>
              <h6 class="fw-bold mb-3"><i class="fa-solid fa-key me-2"></i>Ganti Password</h6>
              
              <form id="formGantiPassword" action="aksi/ganti_password.php" method="POST">
                
                <div class="mb-3">
                  <label class="form-label">Password Lama</label>
                  <div class="input-group">
                    <input type="password" name="password_lama" id="passwordLama" class="form-control" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordLama', this)">
                      <i class="fa fa-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Password Baru</label>
                  <div class="input-group">
                    <input type="password" name="password_baru" id="passwordBaru" class="form-control" 
                          minlength="6" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordBaru', this)">
                      <i class="fa fa-eye"></i>
                    </button>
                  </div>
                  <small class="text-muted">Minimal 6 karakter</small>
                </div>

                <div class="mb-3">
                  <label class="form-label">Konfirmasi Password Baru</label>
                  <div class="input-group">
                    <input type="password" name="password_konfirmasi" id="passwordKonfirmasi" class="form-control" 
                          minlength="6" required>
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordKonfirmasi', this)">
                      <i class="fa fa-eye"></i>
                    </button>
                  </div>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-semibold">
                  <i class="fa fa-save me-2"></i>Simpan Password Baru
                </button>

              </form>
            </div>

          </div>
        </div>
      </div>
    </div>

    <div class="chart-card p-4 bg-white rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <button id="btnPrevYear" class="btn btn-outline-primary btn-sm" disabled>
        <i class="fa-solid fa-chevron-left"></i> Tahun Sebelumnya
      </button>
      
      <h5 class="fw-semibold mb-0 text-center">
        Grafik Pemasukan & Pengeluaran Tahun <span class="text-primary" id="currentYear"><?= date('Y') ?></span>
      </h5>
      
      <button id="btnNextYear" class="btn btn-outline-primary btn-sm" disabled>
        Tahun Berikutnya <i class="fa-solid fa-chevron-right"></i>
      </button>
    </div>

    <div class="position-relative" style="height:380px;">
      <canvas id="chartArea"></canvas>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const tahunSekarang = <?= date('Y') ?>;
    const batasTahunMin = tahunSekarang - 2;
    let tahunTerpilih = tahunSekarang;
    let chart = null;

    const btnPrev = document.getElementById('btnPrevYear');
    const btnNext = document.getElementById('btnNextYear');
    const spanTahun = document.getElementById('currentYear');

    function updateNavigationButtons() {
        btnPrev.disabled = (tahunTerpilih <= batasTahunMin);
        btnNext.disabled = (tahunTerpilih >= tahunSekarang);
    }

    async function loadChart(tahun) {
        const chartContainer = document.querySelector('.chart-card .position-relative');
      
        chartContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat data tahun ${tahun}...</p>
            </div>`;

        try {
            const res = await fetch(`chart_data.php?tahun=${tahun}`);
            const data = await res.json();

            if (data.error) {
                throw new Error(data.error);
            }

            const totalMasuk = data.pemasukan.reduce((a,b) => a + b, 0);
            const totalKeluar = data.pengeluaran.reduce((a,b) => a + b, 0);

            chartContainer.innerHTML = '<canvas id="chartArea"></canvas>';

            if (totalMasuk === 0 && totalKeluar === 0) {
                chartContainer.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fa-solid fa-chart-bar fa-3x mb-3 opacity-50"></i>
                        <p class="mb-0 fw-medium fs-5">Belum ada data pemasukan atau pengeluaran di tahun ${tahun}</p>
                    </div>`;
                return;
            }

            const ctx = document.getElementById('chartArea').getContext('2d');

            if (chart) {
                chart.destroy();
            }

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
            chartContainer.innerHTML = `
                <div class="text-center py-5 text-danger">
                    <i class="fa-solid fa-triangle-exclamation fa-3x mb-3"></i>
                    <p class="mb-2 fw-semibold">Gagal memuat grafik</p>
                    <small class="text-muted">${err.message}</small>
                </div>`;
        }
    }

    btnPrev.addEventListener('click', async () => {
        if (tahunTerpilih > batasTahunMin) {
            tahunTerpilih--;
            spanTahun.textContent = tahunTerpilih;
            updateNavigationButtons();
            await loadChart(tahunTerpilih);
        }
    });

    btnNext.addEventListener('click', async () => {
        if (tahunTerpilih < tahunSekarang) {
            tahunTerpilih++;
            spanTahun.textContent = tahunTerpilih;
            updateNavigationButtons();
            await loadChart(tahunTerpilih);
        }
    });

    loadChart(tahunTerpilih).then(() => {
        updateNavigationButtons();
    });
})();
</script>

<script>
const REALTIME_URL = 'dashboard_realtime.php';

function updateKas() {
    fetch(REALTIME_URL)
        .then(res => res.json())
        .then(data => {
            document.getElementById('total-saldo')?.innerText = data.saldo;
            document.getElementById('pemasukan-bulan')?.innerText = data.pemasukan_bulan;
            document.getElementById('pengeluaran-bulan')?.innerText = data.pengeluaran_bulan;
            document.getElementById('waktu-update')?.innerText = 'Update: ' + data.updated_at;

            if (typeof updateChart === 'function') {
                updateChart(data);
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

<script>
let notifModal;

document.addEventListener('DOMContentLoaded', function() {
  console.log('🔔 Initializing notification system...');
  
  notifModal = new bootstrap.Modal(document.getElementById('notifModal'));
  
  loadNotifikasi();
  
  setInterval(loadNotifikasi, 30000);
  
  document.getElementById('notifButton').addEventListener('click', function() {
    console.log('🔔 Notification button clicked');
    notifModal.show();
    markAllAsRead();
  });
});

// Fungsi untuk load notifikasi
function loadNotifikasi() {
  console.log('📡 Fetching notifications...');
  
  fetch('aksi/get_notifikasi.php')
    .then(res => {
      console.log('📡 Response status:', res.status);
      if (!res.ok) {
        throw new Error('HTTP error! status: ' + res.status);
      }
      return res.json();
    })
    .then(data => {
      console.log('✅ Data received:', data);
      
      const notifList = document.getElementById('notifList');
      const notifBadge = document.getElementById('notifBadge');
      
      if (!notifList) {
        console.error('❌ Element notifList not found!');
        return;
      }
      
      if (data.belum_dibaca > 0) {
        notifBadge.style.display = 'block';
        console.log('🔴 Badge shown:', data.belum_dibaca, 'unread');
      } else {
        notifBadge.style.display = 'none';
        console.log('⚪ Badge hidden');
      }
      
      if (!data.notifikasi || data.notifikasi.length === 0) {
        notifList.innerHTML = `
          <div class="notif-empty">
            <i class="fa-regular fa-bell-slash"></i>
            <p class="mb-0">Belum ada notifikasi</p>
          </div>
        `;
        console.log('📭 No notifications');
      } else {
        console.log('📬', data.notifikasi.length, 'notifications found');
        
        notifList.innerHTML = data.notifikasi.map(notif => {
          const unreadClass = notif.dibaca == 0 ? 'unread' : '';
          const unreadBadge = notif.dibaca == 0 ? '<span class="notif-badge-unread">BARU</span>' : '';
          const tanggal = new Date(notif.tanggal).toLocaleString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
          
          return `
            <div class="notif-item ${unreadClass}">
              <div class="notif-title">
                ${escapeHtml(notif.judul)}
                ${unreadBadge}
              </div>
              <div class="notif-content">
                ${escapeHtml(notif.isi).replace(/\n/g, '<br>')}
              </div>
              <div class="notif-date">
                <i class="fa-regular fa-clock me-1"></i>${tanggal}
              </div>
            </div>
          `;
        }).join('');
      }
    })
    .catch(err => {
      console.error('❌ Error loading notifications:', err);
      const notifList = document.getElementById('notifList');
      if (notifList) {
        notifList.innerHTML = `
          <div class="notif-empty">
            <i class="fa-solid fa-exclamation-triangle text-danger"></i>
            <p class="mb-0">Gagal memuat notifikasi</p>
            <small class="text-danger">${err.message}</small>
          </div>
        `;
      }
    });
}

// Fungsi untuk tandai semua notifikasi sebagai dibaca
function markAllAsRead() {
  console.log('✔️ Marking all as read...');
  
  fetch('aksi/notifikasi_dibaca.php')
    .then(res => res.json())
    .then(data => {
      console.log('✔️ Mark as read response:', data);
      if (data.success) {
        console.log('✔️ Successfully marked as read');
        setTimeout(() => loadNotifikasi(), 500);
      }
    })
    .catch(err => console.error('❌ Failed to mark as read:', err));
}

// Fungsi helper untuk escape HTML (mencegah XSS)
function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}
</script>

<script>
function togglePassword(inputId, button) {
  const input = document.getElementById(inputId);
  const icon = button.querySelector('i');
  
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

// Validasi form ganti password
document.getElementById('formGantiPassword')?.addEventListener('submit', function(e) {
  e.preventDefault();
  
  const passwordBaru = document.getElementById('passwordBaru').value;
  const passwordKonfirmasi = document.getElementById('passwordKonfirmasi').value;
  
  if (passwordBaru !== passwordKonfirmasi) {
    alert('❌ Password baru dan konfirmasi tidak sama!');
    return false;
  }
  
  if (passwordBaru.length < 6) {
    alert('❌ Password minimal 6 karakter!');
    return false;
  }
  
  if (confirm('Yakin ingin mengganti password?')) {
    this.submit();
  }
});
</script>

<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
<!-- <script src="./assets/js/main.js"></script> -->

</body>
</html>
