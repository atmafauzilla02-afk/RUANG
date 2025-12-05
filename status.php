<?php
session_start();
require_once "koneksi/koneksi.php";   // <-- Koneksi kamu yang sudah benar

// Cek login (wajib login dulu)
if (!isset($_SESSION['id_pengguna'])) {
  header("Location: index.php");
  exit;
}

$id_warga = $_SESSION['id_warga'];
$query = "SELECT ... FROM pembayaran WHERE id_warga = '$id_warga'";


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
    .badge-lunas {
      background: #28a745;
      color: white;
      padding: 5px 12px;
      border-radius: 50px;
      font-size: 0.85rem;
    }

    .badge-belum {
      background: #dc3545;
      color: white;
      padding: 5px 12px;
      border-radius: 50px;
      font-size: 0.85rem;
    }

    table tr:hover td {
      background-color: #fff8dc !important;
    }

    .table th {
      background-color: #f5c83b !important;
      color: black;
    }

    .sidebar img {
      width: 110px;
      display: block;
      margin: 20px auto 50px auto;
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

  <!-- MAIN -->
  <main class="main-content p-4">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Status Pembayaran Anda</h2>
      <p class="text-muted">Lihat rincian pembayaran iuran dan tagihan Anda berdasarkan bulan dan tahun.</p>
    </div>

    <!-- FILTER -->
    <div class="filter-box mb-4">
      <div class="container-fluid">
        <div class="row g-3 justify-content-center">
          <div class="col-lg-3 col-md-6">
            <label class="form-label fw-semibold">Tahun:</label>
            <select class="form-select" id="filterTahun">
              <option value="">Semua</option>
              <?php while ($t = mysqli_fetch_assoc($tahun_result)): ?>
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
              <option>Kas</option>
              <option>Keamanan</option>
              <option>Kebersihan</option>
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
          while ($row = mysqli_fetch_assoc($result)):
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['jenis_pembayaran']) ?></td>
              <td><?= htmlspecialchars($row['bulan_pembayaran']) ?></td>
              <td><?= $row['tahun_pembayaran'] ?></td>
              <td>Rp<?= number_format($row['nominal_pembayaran'], 0, ',', '.') ?></td>
              <td>
                <?php
                $status = strtolower($row['status_pembayaran']);
                if ($status == 'lunas'):
                ?>
                  <span class="badge-lunas">Lunas</span>
                <?php elseif ($status == 'menunggu'): ?>
                  <span class="badge-belum">Menunggu</span>
                <?php else: ?>
                  <span class="badge-belum">Belum Lunas</span>
                <?php endif; ?>

              </td>
              <td>
                <?php if (strtolower($row['status_pembayaran']) != 'lunas'): ?>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalPembayaran<?= $row['id_pembayaran'] ?>">Bayar</button>

                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
            </tr>

            <?php if (strtolower($row['status_pembayaran']) != 'lunas'): ?>
              <div class="modal fade" id="modalPembayaran<?= $row['id_pembayaran'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content p-3 rounded-4 border-0 shadow-lg">

                    <div class="modal-header border-0">
                      <h5 class="modal-title fw-bold">
                        Pembayaran Iuran RT/RW
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                      <!-- Judul iuran -->
                      <h5 class="text-center fw-bold mb-4">
                        <?= htmlspecialchars($row['jenis_pembayaran']) ?> –
                        <?= htmlspecialchars($row['bulan_pembayaran']) ?>
                        <?= htmlspecialchars($row['tahun_pembayaran']) ?>
                        (Rp<?= number_format($row['nominal_pembayaran'], 0, ',', '.') ?>)
                      </h5>

                      <!-- Jenis Iuran -->
                      <div class="mb-3">
                        <label class="form-label fw-semibold">1. Jenis Iuran</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($row['jenis_pembayaran']) ?>" readonly>
                      </div>

                      <!-- Metode Pembayaran -->
                      <div class="mb-3">
                        <label class="form-label fw-semibold">2. Metode Pembayaran</label>
                        <div class="d-grid gap-2">
                          <button class="btn btn-outline-secondary metode-btn" data-metode="bank">
                            <i class="fa-solid fa-building-columns me-2"></i>Transfer Bank
                          </button>

                          <button class="btn btn-outline-secondary metode-btn" data-metode="tunai">
                            <i class="fa-solid fa-money-bill-wave me-2"></i>Uang Tunai
                          </button>

                        </div>
                      </div>

                      <!-- Transfer Bank -->
                      <div class="info-metode-bank" id="bank<?= $row['id_pembayaran'] ?>">
                        <div class="card border-0 shadow-sm rounded-4 mb-3 p-3">
                          <p class="fw-semibold mb-2">
                            <i class="fa-solid fa-building-columns me-2 text-primary"></i>
                            Informasi Transfer Bank:
                          </p>
                          <p class="mb-1">Bank: <strong>BCA</strong></p>
                          <p class="mb-1">No. Rekening: <strong>1234567890</strong></p>
                          <p class="mb-0">Atas Nama: <strong>Bendahara RT</strong></p>
                        </div>
                      </div>


                      <!-- Info Tunai (card kotak) -->
                      <div class="info-metode-tunai d-none" id="tunai<?= $row['id_pembayaran'] ?>">
                        <div class="card border-0 shadow-sm rounded-4 mb-3 p-3">
                          <p class="fw-semibold mb-2">
                            <i class="fa-solid fa-hand-holding-dollar me-2 text-success"></i>
                            Informasi Pembayaran Tunai:
                          </p>
                          <p class="mb-1">Alamat Bendahara: <strong>Jl. Melati No. 12</strong></p>
                          <p class="mb-1">Jam: <strong>08.00–21.00</strong></p>
                          <p class="mb-0">HP: <strong>0812-3456-7891</strong></p>
                        </div>
                      </div>


                      <!-- Form Upload -->
                      <form action="aksi/upload_bukti.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_pembayaran" value="<?= $row['id_pembayaran'] ?>">

                        <div class="mb-3">
                          <label class="form-label fw-semibold">Upload Bukti Pembayaran</label>
                          <input type="file" name="bukti" class="form-control" accept=".jpg,.png,.jpeg" required>
                        </div>

                        <div class="modal-footer border-0">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-warning">Kirim</button>
                        </div>
                      </form>

                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

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
        const tahunFilter = document.getElementById('filterTahun').value.trim();
        const bulanFilter = document.getElementById('filterBulan').value.trim().toLowerCase();
        const statusFilter = document.getElementById('filterStatus').value.trim().toLowerCase();
        const jenisFilter = document.getElementById('filterJenis').value.trim().toLowerCase();

        document.querySelectorAll('#statusTableBody tr').forEach(row => {
          const rTahun = row.cells[3].textContent.trim();
          const rBulan = row.cells[2].textContent.trim().toLowerCase();
          const rStatus = row.cells[5].querySelector('span').textContent.trim().toLowerCase();
          const rJenis = row.cells[1].textContent.trim().toLowerCase();

          const show = (!tahunFilter || rTahun === tahunFilter) &&
            (!bulanFilter || rBulan === bulanFilter) &&
            (!statusFilter || rStatus === statusFilter) &&
            (!jenisFilter || rJenis === jenisFilter);

          row.style.display = show ? '' : 'none';
        });
      });
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


    document.querySelectorAll('.metode-btn').forEach(btn => {
      btn.addEventListener('click', function() {

        const modal = this.closest('.modal');
        const id = modal.id.replace('modalPembayaran', '');

        // tombol
        modal.querySelectorAll('.metode-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // info
        const bankBox = modal.querySelector('#bank' + id);
        const tunaiBox = modal.querySelector('#tunai' + id);

        if (this.dataset.metode === "bank") {
          bankBox.classList.remove('d-none');
          tunaiBox.classList.add('d-none');
        } else {
          tunaiBox.classList.remove('d-none');
          bankBox.classList.add('d-none');
        }
      });
    });
  </script>
</body>

</html>