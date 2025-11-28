<?php
session_start();
require_once "koneksi/koneksi.php";   // <-- Koneksi kamu yang sudah benar

// Cek login (wajib login dulu)
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: index.php");
    exit;
}

$id_warga = $_SESSION['id_pengguna'];

// Ambil data pembayaran milik warga yang login
$query = "SELECT 
            id_pembayaran,
            jenis_pembayaran,
            bulan_pembayaran,
            tahun_pembayaran,
            nominal_pembayaran,
            status_pembayaran
          FROM pembayaran 
          WHERE id_warga = '$id_warga'
          ORDER BY tahun_pembayaran DESC, 
                   FIELD(bulan_pembayaran,'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember') DESC";

$result = mysqli_query($koneksi, $query);

// Ambil daftar tahun untuk filter
$tahun_query = "SELECT DISTINCT tahun_pembayaran FROM pembayaran WHERE id_warga = '$id_warga' ORDER BY tahun_pembayaran DESC";
$tahun_result = mysqli_query($koneksi, $tahun_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Pembayaran | Ruang</title>
  <link href="./assets/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./assets/css/status.css">
  <style>
    .badge-lunas { background:#28a745; color:white; padding:5px 12px; border-radius:50px; font-size:0.85rem; }
    .badge-belum { background:#dc3545; color:white; padding:5px 12px; border-radius:50px; font-size:0.85rem; }
    table tr:hover td { background-color: #fff8dc !important; }
    .table th { background-color: #f5c83b !important; color:black; }
    .sidebar img { width:110px; display:block; margin:20px auto 50px auto; }
    .logoMobile { width:80px; display:block; }
  </style>
</head>
<body>

<!-- MOBILE HEADER -->
<header class="mobile-header d-lg-none">
  <button id="menuToggle" class="btn btn-warning me-2"><i class="fa-solid fa-bars"></i></button>
  <img src="assets/img/logo final.png" class="logoMobile" alt="logo">
</header>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <img src="./assets/img/logo final.png" alt="logo">
  <hr>
  <ul class="nav flex-column mt-4">
    <li><a href="dashboard.php" class="nav-link"><i class="fa-solid fa-house me-2"></i>Dashboard</a></li>
    <li><a href="status.php" class="nav-link active"><i class="fa-solid fa-wallet me-2"></i>Status</a></li>
    <li><a href="pengeluaran.php" class="nav-link"><i class="fa-solid fa-coins me-2"></i>Pengeluaran</a></li>
    <li><a href="laporan.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i>Laporan</a></li>
  </ul>
  <a href="logout.php" class="btn btn-dark w-75 mx-auto mt-auto mb-4">
    <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
  </a>
</aside>

<div id="overlay" class="overlay"></div>

<!-- MODAL PEMBAYARAN -->
<div class="modal fade" id="modalPembayaran" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-3 rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Pembayaran Iuran RT/RW</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-center fw-bold text-primary mb-4 modal-summary"></p>

        <div class="mb-3">
          <label class="form-label fw-semibold">Jenis Iuran</label>
          <select class="form-select" id="jenisIuran" required>
            <option value="" disabled selected>Pilih iuran</option>
            <option value="Iuran Kas">Iuran Kas - Rp50.000</option>
            <option value="Iuran Keamanan">Iuran Keamanan - Rp30.000</option>
            <option value="Iuran Kebersihan">Iuran Kebersihan - Rp20.000</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">Metode Pembayaran</label>
          <div class="d-grid gap-2">
            <button class="btn btn-outline-secondary metode-btn active" data-metode="Transfer Bank">
              Transfer Bank
            </button>
            <button class="btn btn-outline-secondary metode-btn" data-metode="Uang Tunai">
              Uang Tunai
            </button>
          </div>
        </div>

        <div id="infoArea"></div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-warning" id="btnKirimPembayaran">Kirim</button>
      </div>
    </div>
  </div>
</div>

<main class="main-content p-4">
  <div class="text-center mb-4">
    <h2 class="fw-bold">Status Pembayaran Anda</h2>
    <p class="text-muted">Lihat rincian pembayaran iuran dan tagihan Anda.</p>
  </div>

  <!-- FILTER -->
  <div class="filter-box mb-4">
    <div class="container-fluid">
      <div class="row g-3 justify-content-center">
        <div class="col-lg-3 col-md-6">
          <label class="form-label fw-semibold">Tahun:</label>
          <select class="form-select" id="filterTahun">
            <option value="">Semua</option>
            <?php while($t = mysqli_fetch_assoc($tahun_result)): ?>
              <option value="<?= $t['tahun_pembayaran'] ?>" <?= $t['tahun_pembayaran'] == date('Y') ? 'selected' : '' ?>>
                <?= $t['tahun_pembayaran'] ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-lg-3 col-md-6">
          <label class="form-label fw-semibold">Bulan:</label>
          <select class="form-select" id="filterBulan">
            <option value="">Semua</option>
            <option>Januari</option><option>Februari</option><option>Maret</option><option>April</option>
            <option>Mei</option><option>Juni</option><option>Juli</option><option>Agustus</option>
            <option>September</option><option>Oktober</option><option>November</option><option>Desember</option>
          </select>
        </div>
        <div class="col-lg-3 col-md-6">
          <label class="form-label fw-semibold">Status:</label>
          <select class="form-select" id="filterStatus">
            <option value="">Semua</option>
            <option value="Lunas">Lunas</option>
            <option value="Belum Lunas">Belum Lunas</option>
          </select>
        </div>
        <div class="col-lg-3 col-md-6">
          <label class="form-label fw-semibold">Jenis Iuran:</label>
          <select class="form-select" id="filterJenis">
            <option value="">Semua</option>
            <option>Iuran Kas</option>
            <option>Iuran Keamanan</option>
            <option>Iuran Kebersihan</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- TABEL -->
  <div class="table-responsive">
    <table class="table table-bordered align-middle text-center shadow-sm rounded">
      <thead>
        <tr>
          <th>No.</th>
          <th>Jenis Pembayaran</th>
          <th>Bulan</th>
          <th>Tahun</th>
          <th>Nominal</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="statusTableBody">
        <?php 
        $no = 1;
        while($row = mysqli_fetch_assoc($result)): 
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['jenis_pembayaran']) ?></td>
          <td><?= htmlspecialchars($row['bulan_pembayaran']) ?></td>
          <td><?= $row['tahun_pembayaran'] ?></td>
          <td>Rp<?= number_format($row['nominal_pembayaran'], 0, ',', '.') ?></td>
          <td>
            <?php if(strtolower($row['status_pembayaran']) == 'lunas'): ?>
              <span class="badge-lunas">Lunas</span>
            <?php else: ?>
              <span class="badge-belum">Belum Lunas</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if(strtolower($row['status_pembayaran']) != 'lunas'): ?>
              <button class="btn btn-warning btn-sm btn-bayar"
                      data-jenis="<?= htmlspecialchars($row['jenis_pembayaran']) ?>"
                      data-bulan="<?= htmlspecialchars($row['bulan_pembayaran']) ?>"
                      data-tahun="<?= $row['tahun_pembayaran'] ?>"
                      data-nominal="Rp<?= number_format($row['nominal_pembayaran'], 0, ',', '.') ?>">
                Bayar
              </button>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Filter
document.querySelectorAll('#filterTahun, #filterBulan, #filterStatus, #filterJenis').forEach(el => {
  el.addEventListener('change', () => {
    const tahun = document.getElementById('filterTahun').value;
    const bulan = document.getElementById('filterBulan').value;
    const status = document.getElementById('filterStatus').value;
    const jenis = document.getElementById('filterJenis').value;

    document.querySelectorAll('#statusTableBody tr').forEach(row => {
      const rTahun = row.cells[3].textContent;
      const rBulan = row.cells[2].textContent.trim();
      const rStatus = row.cells[5].querySelector('span').textContent.trim();
      const rJenis = row.cells[1].textContent.trim();

      const show = (!tahun || rTahun == tahun) &&
                   (!bulan || rBulan == bulan) &&
                   (!status || rStatus == status) &&
                   (!jenis || rJenis == jenis);

      row.style.display = show ? '' : 'none';
    });
  });
});

// Tombol Bayar
document.querySelectorAll('.btn-bayar').forEach(btn => {
  btn.addEventListener('click', function() {
    const jenis = this.dataset.jenis;
    const bulan = this.dataset.bulan;
    const tahun = this.dataset.tahun;
    const nominal = this.dataset.nominal;

    document.querySelector('.modal-summary').innerHTML = `<strong>${jenis}</strong> - ${bulan} ${tahun} (${nominal})`;
    document.getElementById('jenisIuran').value = jenis;

    new bootstrap.Modal(document.getElementById('modalPembayaran')).show();
  });
});

// Metode Pembayaran
document.addEventListener('DOMContentLoaded', () => {
  const metodeBtns = document.querySelectorAll('.metode-btn');
  const infoArea = document.getElementById('infoArea');
  const btnKirim = document.getElementById('btnKirimPembayaran');

  metodeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      metodeBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      if (btn.dataset.metode === 'Transfer Bank') {
        infoArea.innerHTML = `
          <div class="border rounded-3 p-3 mb-3 bg-light">
            <p class="fw-bold mb-2">Informasi Transfer:</p>
            <p><strong>Bank:</strong> BCA</p>
            <p><strong>No. Rek:</strong> 1234567890</p>
            <p><strong>a.n.:</strong> Bendahara RT</p>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Upload Bukti</label>
            <input type="file" class="form-control" id="buktiPembayaran" accept=".jpg,.jpeg,.png">
            <small class="text-muted">Max 5MB</small>
          </div>`;
        btnKirim.textContent = 'Kirim';
      } else {
        infoArea.innerHTML = `
          <div class="border rounded-3 p-3 mb-3 bg-light">
            <p class="fw-bold mb-2">Pembayaran Tunai</p>
            <p>Jl. Melati No. 12 RT 01/RW 03</p>
            <p>Jam: 08.00–21.00</p>
            <p>HP: 0812-3456-7891</p>
          </div>`;
        btnKirim.textContent = 'Selesai';
      }
    });
  });
  metodeBtns[0].click();
});

// Kirim / Selesai
document.getElementById('btnKirimPembayaran').addEventListener('click', () => {
  const jenis = document.getElementById('jenisIuran').value;
  const metode = document.querySelector('.metode-btn.active').dataset.metode;

  if (!jenis) return alert('Pilih jenis iuran dulu!');

  if (metode === 'Transfer Bank') {
    const file = document.getElementById('buktiPembayaran');
    if (!file || !file.files.length) return alert('Upload bukti transfer dulu!');
    alert(`Bukti pembayaran ${jenis} berhasil dikirim!`);
  } else {
    alert(`Silakan bayar tunai ${jenis} ke bendahara.`);
  }

  bootstrap.Modal.getInstance(document.getElementById('modalPembayaran')).hide();
});

// Sidebar Mobile
document.getElementById('menuToggle').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('show');
  document.getElementById('overlay').classList.toggle('active');
});
document.getElementById('overlay').addEventListener('click', () => {
  document.getElementById('sidebar').classList.remove('show');
  document.getElementById('overlay').classList.remove('active');
});
</script>
</body>
</html>