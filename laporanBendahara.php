<?php
session_start();
if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>alert('Login dulu!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Bendahara | Ruang</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(253, 226, 119, 0.7)),
        url('./assets/img/batik awan kuning bg.jpg');
      background-size: cover;
      background-position: center;
      min-height: 100vh;
      overflow-x: hidden;
    }

    .sidebar {
      width: 240px;
      height: 100vh;
      background: linear-gradient(180deg, #f5c83b, #caa43b);
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.08);
      z-index: 1000;
      padding: 20px 0;
      display: flex;
      flex-direction: column;
      transition: left 0.3s ease;
    }

    .sidebar .logo {
      font-weight: 700;
      font-size: 1.8rem;
      color: #fff;
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

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.25);
    }

    .logout-btn {
      background-color: #333;
      color: #fff;
      font-weight: 500;
      border: none;
      width: 80%;
      margin: auto auto 20px auto;
      border-radius: 10px;
      padding: 10px 0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: 0.2s;
    }

    .logout-btn:hover {
      background-color: #222;
    }

    .content {
      margin-left: 260px;
      padding: 40px;
      transition: 0.3s;
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
      z-index: 998;
    }

    .mobile-header h5 {
      margin: 0;
      font-weight: 600;
    }

    #menuToggle {
      border: none;
      background: none;
      font-size: 1.4rem;
      color: #000;
      padding: 4px 8px;
    }

    .filter-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }

    .filter-controls {
      display: flex;
      gap: 10px;
    }

    .btn-upload {
      white-space: nowrap;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0;
        padding: 80px 20px 20px;
      }

      .sidebar {
        left: -240px;
      }

      .sidebar.show {
        left: 0;
      }

      .filter-row {
        flex-direction: row;
        justify-content: space-between;
      }

      .text-center-title {
        text-align: center;
        width: 100%;
      }

      .filter-controls {
        justify-content: flex-start;
      }

      .btn-upload {
        margin-left: auto;
      }
    }

    .card {
      border-radius: 20px;
    }

    .list-group-item {
      background-color: #fffbea;
      border: 1px solid #f6e58d;
      border-radius: 10px !important;
    }

    .list-group-item:hover {
      background-color: #fff5cc;
    }

    iframe {
      width: 100%;
      height: 500px;
      border: none;
      border-radius: 10px;
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
      border: 1px solid #000;
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
    <button id="menuToggle"><i class="fa-solid fa-bars"></i></button>
    <img src="assets/img/logo final.png" class="logoMobile" alt="logo">
  </header>

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <img src="./assets/img/logo final.png" alt="logo">
    <hr>
    <ul class="nav flex-column mt-4">
      <li><a href="dashboardBendahara.php" class="nav-link"><i class="fa-solid fa-house me-2"></i>Dashboard</a></li>
      <li><a href="iuran.php" class="nav-link"><i class="fa-solid fa-wallet me-2"></i>Iuran</a></li>
      <li><a href="kelola_warga.php" class="nav-link"><i class="fa-solid fa-users me-2"></i>Kelola Warga</a></li>
      <li><a href="Pengajuan.php" class="nav-link"><i class="fa-solid fa-file-import me-2"></i>Pengajuan</a></li>
      <li><a href="laporanBendahara.php" class="nav-link active"><i class="fa-solid fa-file-lines me-2"></i>Laporan</a></li>
    </ul>
    <a href="logout.php" class="btn btn-dark w-75 mx-auto mt-auto mb-4">
      <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
    </a>
  </aside>

  <!-- CONTENT -->
  <div class="content" id="content">
    <!-- HEADER -->
    <div class="d-flex justify-content-center align-items-center flex-wrap mb-3 text-center-title">
      <h4 class="fw-bold mb-0 text-dark">Laporan Keuangan Bulanan</h4>
    </div>

    <!-- FILTER BAR -->

    <div class="filter-row mb-3">
      <div class="filter-controls">
        <select id="filterTahun" class="form-select">
          <option value="">Semua Tahun</option>
          <option value="2024">2024</option>
          <option value="2025">2025</option>
        </select>
        <select id="filterBulan" class="form-select">
          <option value="">Semua Bulan</option>
        </select>
      </div>
      <button class="btn btn-success fw-semibold btn-upload" data-bs-toggle="modal" data-bs-target="#generateModal">
        <i class="fa-solid fa-cogs me-1"></i> Generate Laporan Otomatis
      </button>
    </div>

    <!-- DAFTAR LAPORAN -->
    <div class="card shadow-sm p-4 border-0">
      <div id="daftarLaporan" class="list-group"></div>
    </div>
  </div>

  <!-- MODALS -->
  <div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 rounded-4 p-3">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-semibold">Unggah / Edit Laporan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <div class="border border-2 border-dashed rounded-4 p-4 mb-3">
            <i class="fa-solid fa-file-arrow-up fa-2x mb-2 text-warning"></i>
            <p class="text-muted small m-0">Upload dokumen laporan keuangan (PDF)</p>
            <input type="file" id="fileInput" class="form-control mt-2" accept=".pdf" />
          </div>
          <input type="text" id="bulanInput" class="form-control" placeholder="Masukkan nama bulan (contoh: Oktober 2025)" />
        </div>
        <div class="modal-footer border-0 d-flex justify-content-end">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-warning fw-semibold" id="unggahBtn">Simpan</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="lihatModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content border-0 rounded-4">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-semibold" id="pdfTitle">Lihat Laporan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <iframe id="pdfViewer" src=""></iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL GENERATE LAPORAN -->
  <div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 rounded-4">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold" id="generateModalLabel">
            <i class="fa-solid fa-cogs text-success"></i> Generate Laporan Otomatis
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="genTahun" class="form-label fw-semibold">Pilih Tahun</label>
            <select class="form-select" id="genTahun" required>
              <option value="">-- Pilih Tahun --</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="genBulan" class="form-label fw-semibold">Pilih Bulan</label>
            <select class="form-select" id="genBulan" required>
              <option value="">-- Pilih Bulan --</option>
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
          <div class="alert alert-info small p-2" role="alert">
            <i class="fa-solid fa-info-circle"></i>
            Laporan akan digenerate otomatis untuk periode yang dipilih.
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-success fw-bold" id="btnGenerateLaporan">
            <i class="fa-solid fa-spinner fa-spin d-none"></i> Generate Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- SCRIPT -->
  <script>
const bulanNama = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
document.addEventListener('DOMContentLoaded', () => {
  const filterBulan = document.getElementById('filterBulan');
  filterBulan.innerHTML = '<option value="">Semua Bulan</option>';
  bulanNama.forEach(b => filterBulan.add(new Option(b, b)));
});

document.addEventListener('DOMContentLoaded', function() {
  const tahunSelect = document.getElementById('genTahun');
  const tahunSekarang = new Date().getFullYear();
  
  for (let y = tahunSekarang + 1; y >= tahunSekarang - 10; y--) {
    const opt = document.createElement('option');
    opt.value = y;
    opt.textContent = y;
    if (y === tahunSekarang) opt.selected = true;
    tahunSelect.appendChild(opt);
  }
});

document.getElementById('btnGenerateLaporan').addEventListener('click', function() {
  const tahun = document.getElementById('genTahun').value;
  const bulan = document.getElementById('genBulan').value;
  
  if (!tahun || !bulan) {
    alert('Pilih tahun dan bulan terlebih dahulu!');
    return;
  }

  const periode = `${bulan} ${tahun}`;

  if (!confirm(`Generate laporan untuk "${periode}"?`)) {
    return;
  }

  const btn = this;
  const spinner = btn.querySelector('.fa-spinner');
  btn.disabled = true;
  spinner.classList.remove('d-none');

  const formData = new FormData();
  formData.append('bulan', periode);

  fetch('./aksi/generate_laporan.php', {
    method: 'POST',
    body: formData
  })
  .then(r => {
    if (!r.ok) throw new Error('Server error: ' + r.status);
    return r.json();
  })
  .then(res => {
    if (res.success) {
      alert('Berhasil! ' + res.message);
      renderLaporan();
      bootstrap.Modal.getInstance(document.getElementById('generateModal')).hide();
    } else {
      alert('Gagal: ' + res.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert('Error: Terjadi kesalahan saat generate laporan.');
  })
  .finally(() => {
    btn.disabled = false;
    spinner.classList.add('d-none');
  });
});
</script>

<script>
async function renderLaporan() {
    const tahun = document.getElementById('filterTahun').value;
    const bulan = document.getElementById('filterBulan').value;

    const params = new URLSearchParams();
    if (tahun) params.append('tahun', tahun);
    if (bulan) params.append('bulan', bulan);

    try {
        const res = await fetch(`./aksi/get_laporan.php?${params}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        const container = document.getElementById('daftarLaporan');
        container.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">Belum ada laporan.</p>';
            return;
        }

        data.forEach(item => {
            const fileUrl = item.path.replace('../', './');
            const safePath = fileUrl.replace(/ /g, '%20');

            container.innerHTML += `
                <div class="list-group-item d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>${item.bulan_tahun}</strong><br>
                        <small class="text-muted">Otomatis digenerate • ${item.file}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <!-- Tombol LIHAT -->
                        <button class="btn btn-warning btn-sm" 
                                onclick="lihatLaporan('${safePath}', '${item.bulan_tahun}')">
                            <i class="fa-solid fa-eye me-1"></i> Lihat
                        </button>
                        
                        <!-- Tombol DOWNLOAD -->
                        <a href="${safePath}" 
                           download="${item.file}" 
                           class="btn btn-success btn-sm" 
                           title="Download ${item.file}">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    </div>
                </div>`;
        });
    } catch (err) {
        console.error(err);
        container.innerHTML = '<p class="text-danger">Gagal memuat data laporan.</p>';
    }
}

function lihatLaporan(path, judul) {
    const iframe = document.getElementById('pdfViewer');
    iframe.src = path + "?v=" + Date.now();
    document.getElementById('pdfTitle').innerText = `Laporan ${judul}`;
    
    const modal = new bootstrap.Modal(document.getElementById('lihatModal'));
    modal.show();
}

document.getElementById('filterTahun').addEventListener('change', renderLaporan);
document.getElementById('filterBulan').addEventListener('change', renderLaporan);

renderLaporan();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>