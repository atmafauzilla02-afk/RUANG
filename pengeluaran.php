<?php
session_start();

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'warga') {
    header("Location: index.php");
    exit;
}

$koneksi = mysqli_connect("localhost", "root", "", "ruang");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$tahun_ini = (int)date('Y');

$stmt = mysqli_prepare($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran), 0) AS total 
     FROM pembayaran 
     WHERE status_pembayaran = 'lunas' 
       AND tahun_pembayaran = ?");
if (!$stmt) die("Error prepare pemasukan: " . mysqli_error($koneksi));

mysqli_stmt_bind_param($stmt, "i", $tahun_ini);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$masuk = mysqli_fetch_array($result)['total'] ?? 0;
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran), 0) AS total 
     FROM pengeluaran 
     WHERE status_persetujuan = 'Disetujui'
       AND YEAR(tanggal_pengeluaran) = ?");
if (!$stmt) die("Error prepare pengeluaran: " . mysqli_error($koneksi));

mysqli_stmt_bind_param($stmt, "i", $tahun_ini);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$keluar = mysqli_fetch_array($result)['total'] ?? 0;
mysqli_stmt_close($stmt);

$stmt_masuk_all = mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pembayaran), 0) AS total 
     FROM pembayaran 
     WHERE status_pembayaran = 'lunas'");
$masuk_alltime = mysqli_fetch_array($stmt_masuk_all)['total'] ?? 0;

$stmt_keluar_all = mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(nominal_pengeluaran), 0) AS total 
     FROM pengeluaran 
     WHERE status_persetujuan = 'Disetujui'");
$keluar_alltime = mysqli_fetch_array($stmt_keluar_all)['total'] ?? 0;

$saldo = $masuk_alltime - $keluar_alltime;

$stmt = mysqli_prepare($koneksi, "
    SELECT nama_pengeluaran, keterangan_pengeluaran, nominal_pengeluaran, 
           tanggal_pengeluaran, jenis_pengeluaran 
    FROM pengeluaran 
    WHERE status_persetujuan = 'Disetujui' 
      AND YEAR(tanggal_pengeluaran) = ?
    ORDER BY tanggal_pengeluaran DESC");
if (!$stmt) die("Error prepare detail: " . mysqli_error($koneksi));

mysqli_stmt_bind_param($stmt, "i", $tahun_ini);
mysqli_stmt_execute($stmt);
$result_pengeluaran = mysqli_stmt_get_result($stmt);
$pengeluaranData = [];

$bulanIndo = [
    'January'=>'Januari', 'February'=>'Februari', 'March'=>'Maret', 
    'April'=>'April', 'May'=>'Mei', 'June'=>'Juni',
    'July'=>'Juli', 'August'=>'Agustus', 'September'=>'September', 
    'October'=>'Oktober', 'November'=>'November', 'December'=>'Desember'
];

while ($row = mysqli_fetch_assoc($result_pengeluaran)) {
    $tanggal = date('d F Y', strtotime($row['tanggal_pengeluaran']));
    $tanggal = strtr($tanggal, $bulanIndo);

    $pengeluaranData[] = [
        'judul'     => htmlspecialchars($row['nama_pengeluaran'], ENT_QUOTES, 'UTF-8'),
        'deskripsi' => htmlspecialchars($row['keterangan_pengeluaran'] ?: '-', ENT_QUOTES, 'UTF-8'),
        'nominal'   => (int)$row['nominal_pengeluaran'],
        'tanggal'   => $tanggal,
        'kategori'  => strtolower($row['jenis_pengeluaran'])
    ];
}
mysqli_stmt_close($stmt);

$tanggal_sekarang = date('d F Y');
$tanggal_sekarang = strtr($tanggal_sekarang, $bulanIndo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengeluaran | Ruang</title>
  <link href="./assets/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(rgba(255, 255, 255, 0.827), rgba(253, 226, 119, 0.559)), url(./assets/img/batik\ awan\ kuning\ bg.jpg);
      background-size: cover;
      background-position: center;
      min-height: 100vh;
    }

.sidebar {
  width: 240px;
  height: 100vh;
  background: linear-gradient(180deg, #f5c83b, #caa43b);
  color: #fff;
  position: fixed;
  top: 0;
  left: 0;
  box-shadow: 2px 0 10px rgba(0,0,0,0.08);
  z-index: 1000;
  padding: 20px 0;
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
}

.sidebar .logo {
  font-weight: 700;
  color: #fff;
  font-size: 1.8rem;
}

.sidebar .subtitle {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.85);
  margin-top: -6px;
}

.sidebar .nav-link {
  color: #fff !important;
  font-weight: 500;
  margin: 8px 20px;
  border-radius: 8px;
  transition: 0.3s;
  display: flex;
  align-items: center;
  gap: 10px;
}

.sidebar .nav-link i {
  font-size: 1rem;
}

.sidebar .nav-link.active,
.sidebar .nav-link:hover {
  background-color: rgba(255, 255, 255, 0.25);
}

@media (max-width: 992px) {
  .sidebar {
    left: -240px;
  }
  .sidebar.show {
    left: 0;
  }
}

.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: none;
  z-index: 9;
}

.overlay.active {
  display: block;
}

.mobile-header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  background: #f5c83b;
  color: #000;
  padding: 10px 16px;
  display: flex;
  align-items: center;
  z-index: 8;
}

.main-content {
  margin-left: 240px; 
  padding: 2rem;
  transition: all 0.3s ease;
}

@media (max-width: 992px) {
  .main-content {
    margin-left: 0 !important; 
    margin-top: 80px; 
    padding: 1rem;
  }
}

    .info-box {
      background: #fff;
      border-radius: 20px;
      padding: 20px;
      box-shadow: 0 5px 30px rgba(0,0,0,0.05);
    }

    .filter-container {
      padding: 15px;
      margin-bottom: 20px;  
    }
    
    .filter-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: space-between;
  margin-bottom: 20px;
}

.filter-container .form-control,
.filter-container .form-select {
  flex: 1;
  min-width: 220px;
  max-width: 360px;
}

    .card-pengeluaran {
      background: #fff;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.05);
      cursor: pointer;
      transition: transform .2s ease;
    }
    .card-pengeluaran:hover { transform: translateY(-3px); }

    .kategori-badge {
      font-size: 0.8rem;
      background: #f4c430;
      color: #000;
      padding: 5px 10px;
      border-radius: 20px;
    }
    .text-danger { font-weight: 600; }

    #pengeluaranList {
      max-height: 500px;
      overflow-y: auto;
      padding-right: 5px;
    }
    #pengeluaranList::-webkit-scrollbar {
      width: 6px;
    }
    #pengeluaranList::-webkit-scrollbar-thumb {
      background-color: #f5c83b;
      border-radius: 10px;
    }
    .info-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
  padding: 25px;
  transition: 0.2s;
}

.info-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 14px rgba(0, 0, 0, 0.1);
}

.info-card h4 {
  font-weight: 700;
  margin-top: 10px;
}

.info-card .icon {
  font-size: 1.4rem;
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
</head>
<body>

  <!-- MOBILE HEADER -->
  <header class="mobile-header d-lg-none">
    <button id="menuToggle" class="btn btn-warning me-2"><i class="fa-solid fa-bars"></i></button>
    <img src="assets/img/logo final.png" class="logoMobile"  alt="logo">
  </header>

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <img src="./assets/img/logo final.png" alt="logo" >
    <hr>
    <ul class="nav flex-column mt-4">
      <li><a href="dashboard.php" class="nav-link"><i class="fa-solid fa-house me-2"></i>Dashboard</a></li>
      <li><a href="status.php" class="nav-link"><i class="fa-solid fa-file-signature me-2"></i>Status</a></li>
      <li><a href="pengeluaran.php" class="nav-link active"><i class="fa-solid fa-coins me-2"></i>Pengeluaran</a></li>
      <li><a href="laporan.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i>Laporan</a></li>
    </ul>
    <a href="logout.php" class="btn btn-dark w-75 mx-auto mt-auto mb-4">
      <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
    </a>
  </aside>

  <!-- OVERLAY -->
  <div id="overlay" class="overlay"></div>

  <!-- MAIN -->
  <main class="main-content">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Rincian Keuangan Tahun <?= $tahun_ini ?></h2>
      <p class="text-muted">Lihat detail pemasukan dan pengeluaran tahun ini</p>
    </div>

   <!-- Info Box (3 Kolom) -->
  <div class="row g-3 mb-4">
    <div class="col-lg-4 col-md-6">
      <div class="info-card h-100">
        <div class="d-flex justify-content-between align-items-center">
          <span>Pemasukan Tahun Ini</span>
          <i class="fa-solid fa-arrow-trend-up icon text-success"></i>
        </div>
        <h4 class="text-success">Rp<?= number_format($masuk, 0, ',', '.') ?></h4>
        <small class="text-muted">Total iuran lunas tahun <?= $tahun_ini ?></small>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="info-card h-100">
        <div class="d-flex justify-content-between align-items-center">
          <span>Pengeluaran Tahun Ini</span>
          <i class="fa-solid fa-arrow-trend-down icon text-danger"></i>
        </div>
        <h4 class="text-danger">Rp<?= number_format($keluar, 0, ',', '.') ?></h4>
        <small class="text-muted">Total pengeluaran disetujui tahun <?= $tahun_ini ?></small>
      </div>
    </div>

    <div class="col-lg-4 col-md-12">
      <div class="info-card h-100">
        <div class="d-flex justify-content-between align-items-center">
          <span>Saldo Kas Saat Ini</span>
          <i class="fa-solid fa-wallet icon text-warning"></i>
        </div>
        <h4>Rp<?= number_format($saldo, 0, ',', '.') ?></h4>
        <small class="text-muted">Sisa kas per <?= $tanggal_sekarang ?></small>
      </div>
    </div>
  </div>

    <!-- Filter -->
    <div class="filter-container d-flex flex-wrap gap-2 align-items-center justify-content-between">
      <input type="text" class="form-control" placeholder="Cari pengeluaran..." id="searchInput" style="max-width:360px;">
      <select class="form-select" id="filterKategori" style="max-width:360px;">
        <option value="Semua">Semua Kategori</option>
        <option value="infrastruktur">Infrastruktur</option>
        <option value="keamanan">Keamanan</option>
        <option value="kegiatan">Kegiatan</option>
      </select>
      <select class="form-select" id="filterBulan" style="max-width:360px;">
        <option value="Semua">Semua Bulan</option>
        <option value="Januari">Januari</option>
        <option value="Februari">Februari</option>
        <option value="Maret">Maret</option>
        <option value="April">April</option>
        <option value="Mei">Mei</option>
        <option value="Juni">Juni</option>
        <option value="Juli">Juli</option>
        <option value="Agustus">Agustus</option>
        <option value="September">September</option>
        <option value="Oktober">Oktober</option>
        <option value="November">November</option>
        <option value="Desember">Desember</option>
      </select>
    </div>
    
    <div id="pengeluaranList"></div>
  </main>

  <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4">
        <div class="modal-header text-white" style="background: linear-gradient(135deg, #f5c83b, #caa43b);">
          <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-info me-2"></i>Detail Pengeluaran</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="detailBody"></div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-warning text-dark fw-semibold" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script>
    
    const pengeluaranData = <?= json_encode($pengeluaranData, JSON_UNESCAPED_UNICODE) ?>;

    const listContainer = document.getElementById('pengeluaranList');

    function getKategoriIcon(kategori) {
    const icons = {
        'infrastruktur': 'wrench text-primary',
        'keamanan':     'shield-halved text-success',
        'kegiatan':     'people-group text-warning'
    };
    const iconClass = icons[kategori] || 'tag text-secondary';
    return `<i class="fa-solid fa-${iconClass} me-2"></i>`;
    }

    function formatRupiah(num) {
      return 'Rp' + num.toLocaleString('id-ID');
    }

    function renderList(data) {
      listContainer.innerHTML = '';
      if (data.length === 0) {
        listContainer.innerHTML = `<p class="text-center text-muted mt-4">Tidak ada data pengeluaran</p>`;
        return;
      }
      data.forEach(item => {
        const card = document.createElement('div');
        card.className = 'card-pengeluaran';
        card.innerHTML = `
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="fw-semibold mb-1">${getKategoriIcon(item.kategori)}${item.judul}</h6>
              <p class="mb-0 text-muted small">${item.deskripsi}</p>
              <p class="mb-0 text-danger mt-1">${formatRupiah(item.nominal)}</p>
              <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i>${item.tanggal}</small>
            </div>
            <div>
              <span class="kategori-badge">${item.kategori.charAt(0).toUpperCase() + item.kategori.slice(1)}</span>
            </div>
          </div>
        `;
        card.addEventListener('click', () => showDetail(item));
        listContainer.appendChild(card);
      });
    }

    function showDetail(item) {
      const detailBody = document.getElementById('detailBody');
      detailBody.innerHTML = `
        <p><strong>Judul:</strong> ${item.judul}</p>
        <p><strong>Deskripsi:</strong> ${item.deskripsi}</p>
        <p><strong>Nominal:</strong> ${formatRupiah(item.nominal)}</p>
        <p><strong>Tanggal:</strong> ${item.tanggal}</p>
        <p><strong>Kategori:</strong> ${item.kategori}</p>
      `;
      const modal = new bootstrap.Modal(document.getElementById('detailModal'));
      modal.show();
    }

    function applyFilters() {
      const searchVal = document.getElementById('searchInput').value.toLowerCase();
      const kategoriVal = document.getElementById('filterKategori').value;
      const bulanVal = document.getElementById('filterBulan').value;

      const filtered = pengeluaranData.filter(item => {
        const matchSearch = item.judul.toLowerCase().includes(searchVal) || item.deskripsi.toLowerCase().includes(searchVal);
        const matchKategori = kategoriVal === 'Semua' || item.kategori === kategoriVal;
        const matchBulan = bulanVal === 'Semua' || item.tanggal.includes(bulanVal);
        return matchSearch && matchKategori && matchBulan;
      });

      renderList(filtered);
    }

    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('filterKategori').addEventListener('change', applyFilters);
    document.getElementById('filterBulan').addEventListener('change', applyFilters);

    renderList(pengeluaranData);

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menuToggle');

    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
      sidebar.classList.remove('show');
      overlay.classList.remove('active');
    });
  </script>
</body>
</html>