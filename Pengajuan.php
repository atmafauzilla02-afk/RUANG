<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pengajuan | Ruang</title>
  <link href="./assets/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(rgba(255, 255, 255, 0.85), rgba(253, 226, 119, 0.55)), url(./assets/img/batik\ awan\ kuning\ bg.jpg);
      background-size: cover;
      background-position: center;
      min-height: 100vh;
    }

    /* Sidebar */
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
    }

    .sidebar .logo { font-weight: 700; font-size: 1.8rem; color: #fff; }
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
    .sidebar .nav-link.active,
    .sidebar .nav-link:hover { background-color: rgba(255, 255, 255, 0.25); }

    .main-content { margin-left: 250px; padding: 2rem; }
    @media (max-width: 992px) { .main-content { margin-left: 0; padding: 1rem; } }

    .info-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
      padding: 25px;
      transition: 0.2s;
    }
    .info-card:hover { transform: translateY(-3px); }
    .info-card h4 { font-weight: 700; margin-top: 10px; }

    /* Filter bar + button */
    .filter-header {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }
    .filter-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      flex: 1;
    }
    .filter-container .form-control,
    .filter-container .form-select {
      flex: 1;
      min-width: 160px;
    }

    .card-pengajuan {
      background: #fff;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.05);
      transition: transform .2s ease;
      cursor: pointer;
    }
    .card-pengajuan:hover { transform: translateY(-3px); }

    .status { font-weight: 600; }
    .status-menunggu { color: #c59a00; }
    .status-disetujui { color: #28a745; }
    .status-ditolak { color: #dc3545; }

    #pengajuanList {
      max-height: 520px;
      overflow-y: auto;
      padding-right: 5px;
    }
    #pengajuanList::-webkit-scrollbar { width: 6px; }
    #pengajuanList::-webkit-scrollbar-thumb { background-color: #f5c83b; border-radius: 10px; }

/* === Sidebar Responsive === */
@media (max-width: 992px) {
  .sidebar {
    position: fixed;
    left: -260px;
    transition: left 0.3s ease;
  }
  .sidebar.active {
    left: 0;
  }
  #overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.4);
    z-index: 999;
  }
  #overlay.show {
    display: block;
  }
  #toggleSidebar {
    background: linear-gradient(135deg,#f5c83b,#caa43b);
    color: white;
    border: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  }
}
 .mobile-header {
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      background: #f5c83b;
      color: #000;
      padding: 10px 16px;
      display: flex;
      align-items: center;
      z-index: 998;
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


<!-- HEADER MOBILE -->
  <header class="mobile-header d-lg-none">
    <button id="menuToggle" class="btn btn-warning me-2"><i class="fa-solid fa-bars"></i></button>
    <img src="assets/img/logo final.png" class="logoMobile"  alt="logo">
  </header>

  <!-- Sidebar -->
  <aside class="sidebar">
     <img src="./assets/img/logo final.png" alt="logo" >
    <hr>
    <ul class="nav flex-column mt-4">
      <li><a href="dashboardBendahara.php" class="nav-link"><i class="fa-solid fa-house me-2"></i> Dashboard</a></li>
      <li><a href="iuran.php" class="nav-link"><i class="fa-solid fa-wallet me-2"></i> Iuran</a></li>
      <li><a href="kelola_warga.php" class="nav-link"><i class="fa-solid fa-users me-2"></i>Kelola Warga</a></li>
      <li><a href="Pengajuan.php" class="nav-link active" ><i class="fa-solid fa-file-import me-2"></i> Pengajuan</a></li>
      <li><a href="laporanBendahara.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i> Laporan</a></li>
    </ul>
    <a href="logout.php" class="btn btn-dark w-75 mx-auto mt-auto mb-4">
      <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
    </a>
  </aside>

  <!-- Main -->
  <main class="main-content">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Data Pengajuan Tahun 2025</h2>
    </div>

    <!-- Info Cards -->
    <div class="row g-3 mb-4">
      <div class="col-lg-4 col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pemasukan Bulan Ini</span>
            <i class="fa-solid fa-arrow-trend-up text-success"></i>
          </div>
          <h4 class="text-success">Rp3.200.000</h4>
          <small class="text-muted">Bulan Oktober 2025</small>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Pengeluaran Bulan Ini</span>
            <i class="fa-solid fa-arrow-trend-down text-danger"></i>
          </div>
          <h4 class="text-danger">Rp2.450.000</h4>
          <small class="text-muted">Bulan Oktober 2025</small>
        </div>
      </div>
      <div class="col-lg-4 col-md-12">
        <div class="info-card">
          <div class="d-flex justify-content-between align-items-center">
            <span>Total Saldo</span>
            <i class="fa-solid fa-wallet text-warning"></i>
          </div>
          <h4>Rp40.550.000</h4>
          <small class="text-muted">Saldo akhir Oktober</small>
        </div>
      </div>
    </div>

    <!-- Filter + Button -->
    <div class="filter-header">
      <div class="filter-container">
        <input type="text" class="form-control" id="searchInput" placeholder="Cari pengajuan...">
        <select class="form-select" id="filterKategori">
          <option value="Semua">Semua Kategori</option>
          <option value="Keamanan">Keamanan</option>
          <option value="Infrastruktur">Infrastruktur</option>
          <option value="Kegiatan">Kegiatan</option>
        </select>
        <select class="form-select" id="filterBulan">
          <option value="Semua">Semua Bulan</option>
          <option>Januari</option><option>Februari</option><option>Maret</option><option>April</option>
          <option>Mei</option><option>Juni</option><option>Juli</option><option>Agustus</option>
          <option>September</option><option>Oktober</option><option>November</option><option>Desember</option>
        </select>
        <select class="form-select" id="filterTahun">
          <option value="Semua">Semua Tahun</option>
          <option>2025</option><option>2024</option><option>2023</option>
        </select>
        <select class="form-select" id="filterStatus">
          <option value="Semua">Semua Status</option>
          <option value="Disetujui">Disetujui</option>
          <option value="Menunggu">Menunggu</option>
          <option value="Ditolak">Ditolak</option>
        </select>
      </div>
      <button class="btn btn-dark">
        <i class="fa-solid fa-plus me-1"></i> Ajukan Pengeluaran
      </button>
    </div>

    <!-- Daftar Pengajuan -->
    <div id="pengajuanList"></div>
  </main>

  <!-- Modal Detail -->
  <div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4">
        <div class="modal-header text-white" style="background: linear-gradient(135deg, #f5c83b, #caa43b);">
          <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-info me-2"></i>Detail Pengajuan</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="detailBody"></div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-warning text-dark fw-semibold" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Pengajuan -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg,#f5c83b,#caa43b);">
        <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-plus me-2"></i>Pengajuan Pengeluaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Judul Pengeluaran</label>
          <input type="text" class="form-control" id="judulInput" placeholder="Misal: Pembelian cat tembok">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Keterangan</label>
          <textarea class="form-control" id="keteranganInput" placeholder="Deskripsi singkat..."></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Nominal</label>
          <input type="number" class="form-control" id="nominalInput" placeholder="Rp">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Kategori</label>
          <select id="kategoriInput" class="form-select">
            <option value="Keamanan">Keamanan</option>
            <option value="Infrastruktur">Infrastruktur</option>
            <option value="Kegiatan">Kegiatan</option>
          </select>
         </div>

      
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-warning text-dark fw-semibold" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-dark fw-semibold" id="submitPengajuan">Kirim</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="konfirmasiModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 text-center p-3">
      <h5 class="fw-bold mt-2">Apakah anda yakin?</h5>
      <div class="d-flex justify-content-center gap-3 mt-3 mb-2">
        <button class="btn btn-secondary px-4" data-bs-dismiss="modal">Tidak</button>
        <button class="btn btn-dark px-4" id="konfirmasiYa">Ya</button>
      </div>
    </div>
  </div>
</div>

<!-- Overlay untuk menutup sidebar -->
<div id="overlay"></div>

<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script>
  // === Logout ===
  function logout() {
    alert("Logout berhasil!");
    location.href = "index.html";
  }

  // === Tombol tambah pengeluaran (desktop) ===
  document.querySelector('.btn-dark i.fa-plus').parentElement.addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('tambahModal')).show();
  });

  // === Data sementara untuk tambah pengajuan ===
  let tempData = {};

  document.getElementById('submitPengajuan').addEventListener('click', () => {
    const judul = document.getElementById('judulInput').value.trim();
    const keterangan = document.getElementById('keteranganInput').value.trim();
    const nominal = parseInt(document.getElementById('nominalInput').value);
    const kategori = document.getElementById('kategoriInput').value;

    if (!judul || !keterangan || !nominal || isNaN(nominal)) {
      alert("Semua field wajib diisi dengan benar!");
      return;
    }

    tempData = {
      judul,
      deskripsi: keterangan,
      nominal,
      tanggal: new Date().toLocaleDateString('id-ID'),
      kategori,
      status: "Menunggu"
    };

    const tambahModal = bootstrap.Modal.getInstance(document.getElementById('tambahModal'));
    tambahModal.hide();
    new bootstrap.Modal(document.getElementById('konfirmasiModal')).show();
  });

  document.getElementById('konfirmasiYa').addEventListener('click', () => {
    if (Object.keys(tempData).length) {
      pengajuanData.unshift(tempData);
      renderList(pengajuanData);
      tempData = {};
      bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal')).hide();
    }
  });
</script>

<script>
  // === Data pengajuan ===
  const pengajuanData = [
    {judul:'Gaji Satpam Bulanan', deskripsi:'Gaji satpam bulan Oktober', nominal:2500000, tanggal:'01/10/2025', kategori:'Keamanan', status:'Disetujui'},
    {judul:'Pengecatan Pos Ronda', deskripsi:'Cat dan peralatan untuk pos ronda', nominal:400000, tanggal:'12/09/2025', kategori:'Infrastruktur', status:'Menunggu'},
    {judul:'Kegiatan 17 Agustus', deskripsi:'Pembelian hadiah lomba dan konsumsi', nominal:1500000, tanggal:'05/08/2025', kategori:'Kegiatan', status:'Ditolak'},
    {judul:'Perbaikan Saluran Air', deskripsi:'Pembuatan saluran air RT 01', nominal:1200000, tanggal:'10/07/2025', kategori:'Infrastruktur', status:'Disetujui'},
    {judul:'Lampu Jalan Baru', deskripsi:'Pemasangan 5 lampu jalan tambahan', nominal:750000, tanggal:'25/06/2025', kategori:'Keamanan', status:'Menunggu'}
  ];

  const listContainer = document.getElementById('pengajuanList');

  function formatRupiah(num) {
    return 'Rp' + num.toLocaleString('id-ID');
  }

  function renderList(data) {
    listContainer.innerHTML = '';
    if (!data.length) {
      listContainer.innerHTML = `<p class="text-center text-muted mt-4">Tidak ada data pengajuan</p>`;
      return;
    }

    data.forEach(item => {
      const card = document.createElement('div');
      card.className = 'card-pengajuan';
      card.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6 class="fw-semibold mb-1">${item.judul}</h6>
            <p class="mb-0 text-muted small">${item.deskripsi}</p>
            <p class="mb-1 text-danger fw-semibold">${formatRupiah(item.nominal)}</p>
            <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i>${item.tanggal} | ${item.kategori}</small>
          </div>
          <span class="status ${item.status === 'Disetujui' ? 'status-disetujui' : item.status === 'Ditolak' ? 'status-ditolak' : 'status-menunggu'}">${item.status}</span>
        </div>
      `;
      card.addEventListener('click', () => showDetail(item));
      listContainer.appendChild(card);
    });
  }

  function showDetail(item) {
    document.getElementById('detailBody').innerHTML = `
      <p><strong>Judul:</strong> ${item.judul}</p>
      <p><strong>Deskripsi:</strong> ${item.deskripsi}</p>
      <p><strong>Nominal:</strong> ${formatRupiah(item.nominal)}</p>
      <p><strong>Tanggal:</strong> ${item.tanggal}</p>
      <p><strong>Kategori:</strong> ${item.kategori}</p>
      <p><strong>Status:</strong> ${item.status}</p>
    `;
    new bootstrap.Modal('#detailModal').show();
  }

  function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const kategori = document.getElementById('filterKategori').value;
    const bulan = document.getElementById('filterBulan').value;
    const tahun = document.getElementById('filterTahun').value;
    const status = document.getElementById('filterStatus').value;

    const filtered = pengajuanData.filter(p => {
      const matchSearch = p.judul.toLowerCase().includes(search) || p.deskripsi.toLowerCase().includes(search);
      const matchKategori = kategori === 'Semua' || p.kategori === kategori;
      const matchStatus = status === 'Semua' || p.status === status;
      const matchBulan = bulan === 'Semua' || p.tanggal.includes(bulan);
      const matchTahun = tahun === 'Semua' || p.tanggal.includes(tahun);
      return matchSearch && matchKategori && matchStatus && matchBulan && matchTahun;
    });

    renderList(filtered);
  }

  document.querySelectorAll('#searchInput, #filterKategori, #filterBulan, #filterTahun, #filterStatus')
    .forEach(el => el.addEventListener('input', applyFilters));

  renderList(pengajuanData);
</script>

<script>
  // === Sidebar mobile fix ===
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('overlay');
  const menuToggle = document.getElementById('menuToggle');

  menuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar.classList.toggle('active');
    overlay.classList.toggle('show');
  });

  // Tutup sidebar jika klik di luar area sidebar
  document.addEventListener('click', (e) => {
    const isClickInsideSidebar = sidebar.contains(e.target) || menuToggle.contains(e.target);
    if (!isClickInsideSidebar) {
      sidebar.classList.remove('active');
      overlay.classList.remove('show');
    }
  });
</script>
