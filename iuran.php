<?php
session_start();
if (!isset($_SESSION['id_pengguna'])) {
  echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = './login.php';

    </script>";
  exit;
}
include 'koneksi/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iuran | Ruang</title>

  <link href="./assets/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(253, 226, 119, 0.5)), url(./assets/img/batik\ awan\ kuning\ bg.jpg);
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
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.08);
      z-index: 1000;
      padding: 20px 0;
      display: flex;
      flex-direction: column;
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

    @media (max-width: 992px) {
      .sidebar {
        left: -240px;
      }

      .sidebar.show {
        left: 0;
      }

      .main-content {
        margin-left: 0;
        padding: 1rem;
      }
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      z-index: 999;
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
      z-index: 998;
    }

    .main-content {
      margin-left: 250px;
      padding: 2rem;
    }

    .table-container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
      padding: 20px;
      overflow-x: auto;
    }

    .table-container {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      min-width: 720px;
      border-radius: 12px;
      overflow: hidden;
      background: transparent;
    }

    th,
    td {
      text-align: center;
      vertical-align: middle !important;
    }

    .status-lunas {
      color: #28a745;
      font-weight: 600;
    }

    .status-menunggu {
      color: #ffc107;
      font-weight: 600;
    }

    .status-belum {
      color: #dc3545;
      font-weight: 600;
    }

    .btn-action {
      font-size: 0.85rem;
      padding: 4px 10px;
      border-radius: 6px;
      margin: 2px;
      color: #fff;
    }

    .form-select,
    .form-control {
      border-radius: 10px;
      font-size: 14px;
      height: 38px;
    }

    .sidebar img {
      transition: 0.3s;
    }

    .sidebar.show img {
      transform: scale(1.05);
    }

    @media (max-width: 992px) {
      .sidebar {
        left: -240px;
        transition: all 0.3s ease;
      }

      .sidebar.show {
        left: 0;
      }

      .main-content {
        margin-left: 0 !important;
        padding-top: 70px;
      }

      .overlay.active {
        display: block;
      }
    }

    .table th {
      background-color: #f4c430 !important;
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

    .floating-btn {
      position: fixed !important;
      bottom: 20px !important;
      right: 20px !important;
      left: auto !important;
      background: linear-gradient(135deg, #f5c83b, #caa43b);
      color: white;
      border: none;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      font-size: 1.6rem;
      z-index: 5000 !important;
    }
  </style>
</head>


<body>

  <!-- HEADER MOBILE -->
  <header class="mobile-header d-lg-none">
    <button id="menuToggle" class="btn btn-warning me-2"><i class="fa-solid fa-bars"></i></button>
    <img src="assets/img/logo final.png" class="logoMobile" alt="logo">
  </header>


  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <img src="./assets/img/logo final.png" alt="logo">
    <hr>
    <ul class="nav flex-column mt-4">
      <li><a href="dashboardBendahara.php" class="nav-link"><i class="fa-solid fa-house me-2"></i>Dashboard</a></li>
      <li><a href="iuran.php" class="nav-link active"><i class="fa-solid fa-wallet me-2"></i>Iuran</a></li>
      <li><a href="kelola_warga.php" class="nav-link"><i class="fa-solid fa-users me-2"></i>Kelola Warga</a></li>
      <li><a href="Pengajuan.php" class="nav-link"><i class="fa-solid fa-file-import me-2"></i>Pengajuan</a></li>
      <li><a href="laporanBendahara.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i>Laporan</a></li>
    </ul>
    <a href="logout.php" class="btn btn-dark w-75 mx-auto mt-auto mb-4">
      <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
    </a>
  </aside>
  <div id="overlay" class="overlay"></div>

  <!-- MAIN -->
  <main class="main-content">
    <h3 class="fw-bold mb-4 text-center">Data Iuran Warga</h3>

    <!-- Filter Bar -->
    <div class="row g-2 align-items-center mb-4">
      <div class="col-lg-2 col-md-3 col-6">
        <select id="filterKategori" class="form-select form-select-sm">
          <option value="">Semua Kategori</option>
          <option>Kas</option>
          <option>Keamanan</option>
          <option>Kebersihan</option>
        </select>
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <select id="filterBulan" class="form-select form-select-sm">
          <option value="">Semua Bulan</option>
          <option>Januari</option>
          <option>Februari</option>
          <option>Maret</option>
          <option>April</option>
          <option>Mei</option>
          <option>Juni</option>
          <option>Juli</option>
          <option>Agustus</option>
          <option>September</option>
          <option>Oktober</option>
          <option>November</option>
          <option>Desember</option>
        </select>
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <select id="filterTahun" class="form-select form-select-sm">
          <option value="">Semua Tahun</option>
          <option>2025</option>
          <option>2024</option>
        </select>
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <select id="filterStatus" class="form-select form-select-sm">
          <option value="">Semua Status</option>
          <option>Lunas</option>
          <option>Menunggu</option>
          <option>Belum</option>
        </select>
      </div>
      <div class="col-lg-4 col-md-12 mt-md-0 mt-2">
        <input id="searchInput" type="text" class="form-control form-control-sm" placeholder="Cari nama...">
      </div>
    </div>

    <!-- Tombol Iuran -->
    <button class="btn btn-primary mb-3 me-2" data-bs-toggle="modal" data-bs-target="#modalIuranWajib">
      <i class="fa fa-users"></i> Buat Iuran Wajib Bulanan
    </button>

    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center shadow-sm rounded">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Warga</th>
            <th>Kategori</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Nominal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $query = mysqli_query($koneksi, "
              SELECT p.*, pg.nama 
              FROM pembayaran p
              JOIN warga w ON p.id_warga = w.id_warga
              JOIN pengguna pg ON w.id_pengguna = pg.id_pengguna
              ORDER BY p.tahun_pembayaran DESC, FIELD(p.bulan_pembayaran, 'januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember')
          ");

          $no = 1;
          while ($data = mysqli_fetch_assoc($query)) {
          ?>
            <tr>
              <td><?= $no++; ?></td>
              <td><?= htmlspecialchars($data['nama']); ?></td>
              <td><?= ucfirst($data['jenis_pembayaran']); ?></td>
              <td><?= ucfirst($data['bulan_pembayaran']); ?></td>
              <td><?= $data['tahun_pembayaran']; ?></td>
              <td>Rp<?= number_format($data['nominal_pembayaran'], 0, ',', '.'); ?></td>

              <td>
                <?php if ($data['status_pembayaran'] == 'lunas'): ?>
                  <span class="badge bg-success">Lunas</span>
                <?php elseif ($data['status_pembayaran'] == 'menunggu'): ?>
                  <span class="badge bg-warning text-dark">Menunggu</span>
                <?php else: ?>
                  <span class="badge bg-danger">Belum Bayar</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($data['status_pembayaran'] == 'menunggu'): ?>
                  <button class="btn btn-primary btn-sm me-1"
                    data-bs-toggle="modal"
                    data-bs-target="#detailModal"
                    data-nama="<?= htmlspecialchars($data['nama']); ?>"
                    data-kategori="<?= ucfirst($data['jenis_pembayaran']); ?>"
                    data-bulan="<?= ucfirst($data['bulan_pembayaran']); ?>"
                    data-tahun="<?= $data['tahun_pembayaran']; ?>"
                    data-nominal="<?= $data['nominal_pembayaran']; ?>"
                    data-status="<?= ucfirst($data['status_pembayaran']); ?>"
                    data-bukti="<?= $data['bukti_pembayaran']; ?>">
                    <i class="fa fa-eye"></i>
                  </button>

                  <a href="aksi/konfirmasi_iuran.php?id=<?= $data['id_pembayaran']; ?>"
                    class="btn btn-success btn-sm me-1"
                    onclick="return confirm('Yakin pembayaran sudah masuk?')">
                    <i class="fa fa-check"></i>
                  </a>

                  <a href="aksi/tolak_iuran.php?id=<?= $data['id_pembayaran']; ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Tolak pembayaran ini?')">
                    <i class="fa fa-times"></i>
                  </a>

                <?php elseif ($data['status_pembayaran'] == 'belum'): ?>
                  <button class="btn btn-success btn-sm me-1"
                    data-bs-toggle="modal"
                    data-bs-target="#modalBayarTunai"
                    data-id="<?= $data['id_pembayaran']; ?>"
                    data-nama="<?= htmlspecialchars($data['nama']); ?>"
                    data-jenis="<?= ucfirst($data['jenis_pembayaran']); ?>"
                    data-bulan="<?= ucfirst($data['bulan_pembayaran']); ?>"
                    data-tahun="<?= $data['tahun_pembayaran']; ?>"
                    data-nominal="<?= $data['nominal_pembayaran']; ?>">
                    <i class="fa fa-money-bill-wave"></i> Bayar Tunai
                  </button>

                  <!-- TOMBOL BEL NOTIFIKASI -->
                  <?php
                  $cek_notif = mysqli_query($koneksi, "
                      SELECT id FROM notifikasi_warga 
                      WHERE id_warga = '{$data['id_warga']}' 
                        AND judul LIKE '%{$data['jenis_pembayaran']}%' 
                        AND judul LIKE '%{$data['bulan_pembayaran']}%' 
                        AND judul LIKE '%{$data['tahun_pembayaran']}%'
                        AND tanggal >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                  ");
                  $sudah_kirim = mysqli_num_rows($cek_notif) > 0;
                  ?>

                  <button class="btn btn-warning btn-sm <?= $sudah_kirim ? 'disabled' : '' ?>"
                    data-bs-toggle="modal"
                    data-bs-target="<?= $sudah_kirim ? '' : '#modalNotifikasiSingle' ?>"
                    data-id-warga="<?= $data['id_warga']; ?>"
                    data-nama="<?= htmlspecialchars($data['nama']); ?>"
                    data-jenis="<?= ucfirst($data['jenis_pembayaran']); ?>"
                    data-bulan="<?= ucfirst($data['bulan_pembayaran']); ?>"
                    data-tahun="<?= $data['tahun_pembayaran']; ?>"
                    data-nominal="<?= number_format($data['nominal_pembayaran'], 0, ',', '.'); ?>"
                    <?= $sudah_kirim ? 'disabled title="Notifikasi sudah dikirim dalam 24 jam terakhir"' : '' ?>>
                    <i class="fa fa-bell"></i>
                    <?= $sudah_kirim ? '<i class="fa fa-check ms-1"></i>' : '' ?>
                  </button>

                <?php else: ?>
                  <span class="text-success fw-bold">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- MODAL BUAT IURAN WAJIB -->
  <div class="modal fade" id="modalIuranWajib" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="aksi/iuran_wajib.php" method="POST">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><i class="fa fa-users"></i> Buat Iuran Wajib Bulanan</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted fw-bold">Akan otomatis membuat iuran untuk <u>SEMUA WARGA</u> pada bulan & tahun berikut:</p>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select" required>
                  <option value="">Pilih Bulan</option>
                  <?php
                  $bulan = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];
                  foreach ($bulan as $b) {
                    echo "<option value='$b'>" . ucfirst($b) . "</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>" required>
              </div>
            </div>

            <hr>

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label text-success fw-bold">Iuran Kas</label>
                <input type="number" name="nominal_kas" class="form-control" value="50000" required>
              </div>
              <div class="col-md-4">
                <label class="form-label text-primary fw-bold">Iuran Keamanan</label>
                <input type="number" name="nominal_keamanan" class="form-control" value="20000" required>
              </div>
              <div class="col-md-4">
                <label class="form-label text-info fw-bold">Iuran Kebersihan</label>
                <input type="number" name="nominal_kebersihan" class="form-control" value="15000" required>
              </div>
            </div>

            <div class="alert alert-warning mt-3">
              <strong>Peringatan:</strong> Jika salah satu dari 3 iuran ini sudah pernah dibuat di bulan & tahun yang sama, proses akan dihentikan.
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="fa fa-check"></i> Buat Iuran
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title"><b>Detail Pembayaran</b></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <p><b>Nama:</b> <span id="detail-nama"></span></p>
          <p><b>Kategori:</b> <span id="detail-jenis"></span></p>
          <p><b>Bulan:</b> <span id="detail-bulan"></span></p>
          <p><b>Tahun:</b> <span id="detail-tahun"></span></p>
          <p><b>Nominal:</b> <span id="detail-nominal"></span></p>
          <p><b>Status:</b> <span id="detail-status"></span></p>

          <div id="detail-bukti-wrapper"></div>

        </div>
      </div>
    </div>
  </div>

  <!-- MODAL BAYAR TUNAI -->
  <div class="modal fade" id="modalBayarTunai" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="aksi/proses_bayar_tunai.php" method="POST" enctype="multipart/form-data">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Konfirmasi Pembayaran Tunai</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id_pembayaran" id="tunaiId">

            <div class="row mb-3">
              <div class="col-md-6">
                <p><strong>Warga:</strong> <span id="tunaiNama" class="text-primary fw-bold"></span></p>
                <p><strong>Iuran:</strong> <span id="tunaiJenis"></span></p>
                <p><strong>Periode:</strong> <span id="tunaiBulan"></span> <span id="tunaiTahun"></span></p>
              </div>
              <div class="col-md-6">
                <p><strong>Nominal:</strong>
                  <input type="number" name="nominal_dibayar" id="tunaiNominal" class="form-control text-success fw-bold text-center" readonly>
                </p>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Tanggal Bayar</label>
              <input type="date" name="tanggal_bayar" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-danger">Upload Bukti (Kuitansi/Nota)</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*" required>
              <small class="text-muted">JPG/PNG, max 2MB</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success btn-lg">
              Konfirmasi & Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL NOTIFIKASI -->
  <div class="modal fade" id="modalNotifikasiSingle" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="aksi/kirim_notifikasi.php" method="POST">
          <div class="modal-header bg-warning">
            <h5 class="modal-title"><i class="fa-solid fa-bell"></i> Kirim Pengingat Pembayaran</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            
            <input type="hidden" name="id_warga" id="notifIdWarga">
            <input type="hidden" name="jenis" id="notifJenis">
            <input type="hidden" name="bulan" id="notifBulan">
            <input type="hidden" name="tahun" id="notifTahun">

            <div class="alert alert-info">
              <strong>Kepada:</strong> <span id="notifNama" class="text-primary"></span><br>
              <strong>Untuk:</strong> Iuran <span id="notifJenisText"></span> - <span id="notifBulanText"></span> <span id="notifTahunText"></span><br>
              <strong>Nominal:</strong> Rp<span id="notifNominal"></span>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Judul Notifikasi</label>
              <input type="text" name="judul" class="form-control" id="notifJudul"
                    value="Pengingat Pembayaran Iuran" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Isi Pesan</label>
              <textarea name="isi" class="form-control" rows="4" id="notifIsi" required>Mohon segera melunasi pembayaran iuran. Terima kasih atas kerjasamanya.</textarea>
            </div>

          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning">
              <i class="fa fa-paper-plane"></i> Kirim Notifikasi
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

      const searchInput = document.getElementById('searchInput');
      const filterKategori = document.getElementById('filterKategori');
      const filterBulan = document.getElementById('filterBulan');
      const filterTahun = document.getElementById('filterTahun');
      const filterStatus = document.getElementById('filterStatus');

      if (searchInput && filterKategori && filterBulan && filterTahun && filterStatus) {
        function filterTable() {
          const search = searchInput.value.toLowerCase();
          const kategori = filterKategori.value.toLowerCase();
          const bulan = filterBulan.value.toLowerCase();
          const tahun = filterTahun.value.toLowerCase();
          const status = filterStatus.value.toLowerCase();

          const rows = document.querySelectorAll('tbody tr');
          rows.forEach(row => {
            const nama = row.cells[1].textContent.toLowerCase();
            const rowKategori = row.cells[2].textContent.toLowerCase();
            const rowBulan = row.cells[3].textContent.toLowerCase();
            const rowTahun = row.cells[4].textContent.toLowerCase();
            const rowStatus = row.cells[6].textContent.toLowerCase();

            const matchSearch = nama.includes(search);
            const matchKategori = kategori === '' || rowKategori.includes(kategori);
            const matchBulan = bulan === '' || rowBulan.includes(bulan);
            const matchTahun = tahun === '' || rowTahun.includes(tahun);
            const matchStatus = status === '' || rowStatus.includes(status);

            if (matchSearch && matchKategori && matchBulan && matchTahun && matchStatus) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        }

        searchInput.addEventListener('keyup', filterTable);
        filterKategori.addEventListener('change', filterTable);
        filterBulan.addEventListener('change', filterTable);
        filterTahun.addEventListener('change', filterTable);
        filterStatus.addEventListener('change', filterTable);
      }

      const menuToggle = document.getElementById('menuToggle');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');

      if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function() {
          sidebar.classList.toggle('show');
          overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function() {
          sidebar.classList.remove('show');
          overlay.classList.remove('active');
        });
      }

      const detailModal = document.getElementById('detailModal');

      detailModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        const nama = button.getAttribute('data-nama');
        const kategori = button.getAttribute('data-kategori');
        const bulan = button.getAttribute('data-bulan');
        const tahun = button.getAttribute('data-tahun');
        const nominal = button.getAttribute('data-nominal');
        const status = button.getAttribute('data-status');
        const bukti = button.getAttribute('data-bukti');

        // Isi modal
        document.getElementById('detail-nama').textContent = nama;
        document.getElementById('detail-jenis').textContent = kategori;
        document.getElementById('detail-bulan').textContent = bulan;
        document.getElementById('detail-tahun').textContent = tahun;
        document.getElementById('detail-nominal').textContent = "Rp" + Number(nominal).toLocaleString('id-ID');
        document.getElementById('detail-status').textContent = status;

        const buktiWrapper = document.getElementById('detail-bukti-wrapper');
        if (bukti && bukti !== "") {
          buktiWrapper.innerHTML = `<img src="assets/bukti_pembayaran/${bukti}" class="img-fluid rounded mt-3" style="max-height:400px;">`;
        } else {
          buktiWrapper.innerHTML = `<p class="text-muted mt-3">Tidak ada bukti pembayaran.</p>`;
        }
      });
    });

    // Handle modal bayar tunai
    const modalBayarTunai = document.getElementById('modalBayarTunai');
    if (modalBayarTunai) {
      modalBayarTunai.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        // Ambil data dari button
        const id = button.getAttribute('data-id');
        const nama = button.getAttribute('data-nama');
        const jenis = button.getAttribute('data-jenis');
        const bulan = button.getAttribute('data-bulan');
        const tahun = button.getAttribute('data-tahun');
        const nominal = button.getAttribute('data-nominal');

        // Isi ke modal
        document.getElementById('tunaiId').value = id;
        document.getElementById('tunaiNama').textContent = nama;
        document.getElementById('tunaiJenis').textContent = jenis;
        document.getElementById('tunaiBulan').textContent = bulan;
        document.getElementById('tunaiTahun').textContent = tahun;
        document.getElementById('tunaiNominal').value = nominal;
      });
    }

    // Handle modal notifikasi single
    const modalNotifSingle = document.getElementById('modalNotifikasiSingle');
    if (modalNotifSingle) {
      modalNotifSingle.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        // Ambil data dari button
        const idWarga = button.getAttribute('data-id-warga');
        const nama = button.getAttribute('data-nama');
        const jenis = button.getAttribute('data-jenis');
        const bulan = button.getAttribute('data-bulan');
        const tahun = button.getAttribute('data-tahun');
        const nominal = button.getAttribute('data-nominal');

        // Isi ke modal
        document.getElementById('notifIdWarga').value = idWarga;
        document.getElementById('notifJenis').value = jenis.toLowerCase();
        document.getElementById('notifBulan').value = bulan.toLowerCase();
        document.getElementById('notifTahun').value = tahun;
        
        document.getElementById('notifNama').textContent = nama;
        document.getElementById('notifJenisText').textContent = jenis;
        document.getElementById('notifBulanText').textContent = bulan;
        document.getElementById('notifTahunText').textContent = tahun;
        document.getElementById('notifNominal').textContent = nominal;

        // Auto-fill pesan
        const pesanOtomatis = `Yth. Bapak/Ibu ${nama}, Kami mengingatkan bahwa pembayaran Iuran ${jenis} untuk periode ${bulan} ${tahun} sebesar Rp${nominal} belum kami terima. Mohon segera melakukan pembayaran. Terima kasih atas kerjasamanya.`;
        document.getElementById('notifIsi').value = pesanOtomatis;
      });
    }
  </script>
</body>

</html>