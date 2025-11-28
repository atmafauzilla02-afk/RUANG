<?php
session_start();
if (!isset($_SESSION['id_pengguna'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu!');
        window.location.href = './login.php';

    </script>";
    exit;
}
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
      background: linear-gradient(rgba(255,255,255,0.9), rgba(253,226,119,0.5)), url(./assets/img/batik\ awan\ kuning\ bg.jpg);
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

    .logout-btn {
      background-color: #333; color: #fff; font-weight: 500; border: none;
      width: 80%; margin: auto auto 20px auto; border-radius: 10px;
      padding: 10px 0; display: flex; align-items: center;
      justify-content: center; gap: 6px; transition: 0.2s;
    }
    .logout-btn:hover { background-color: #222; }


    /* RESPONSIVE */
    @media (max-width: 992px) {
      .sidebar { left: -240px; }
      .sidebar.show { left: 0; }
      .main-content { margin-left: 0; padding: 1rem; }
    }

    .overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      z-index: 999;
    }
    .overlay.active { display: block; }

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

    /* MAIN CONTENT */
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
  background:transparent;
}

    th, td { text-align: center; vertical-align: middle !important; }

    .status-lunas { color: #28a745; font-weight: 600; }
    .status-menunggu { color: #ffc107; font-weight: 600; }
    .status-belum { color: #dc3545; font-weight: 600; }

    .btn-action {
      font-size: 0.85rem;
      padding: 4px 10px;
      border-radius: 6px;
      margin: 2px;
      color: #fff;
    }

    .floating-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: linear-gradient(135deg, #f5c83b, #caa43b);
      color: white;
      border: none;
      width: 55px;
      height: 55px;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      font-size: 1.4rem;
      z-index: 1001;
    }

    .form-select, .form-control {
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
    padding-top: 70px; /* biar ga ketiban header */
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
  border: 1px solid #000 ;  
  width: 80%;             
  margin: -40px auto 20px auto;
        
}

.logoMobile {
  width: 80px;         
  display: block;
}
/* FIX POSISI BEL SUPAYA SELALU DI KANAN BAWAH */
.floating-btn {
    position: fixed !important;
    bottom: 20px !important; /* Jarak dari bawah (bisa ubah) */
    right: 20px !important;  /* Jarak dari kanan (bisa ubah) */
    left: auto !important;   /* Pastikan tidak tertarik ke kiri */
    background: linear-gradient(135deg, #f5c83b, #caa43b);
    color: white;
    border: none;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    font-size: 1.6rem;
    z-index: 5000 !important; /* selalu di atas elemen lain */
}



  </style>
</head>


<body>

  <!-- HEADER MOBILE -->
  <header class="mobile-header d-lg-none">
    <button id="menuToggle" class="btn btn-warning me-2"><i class="fa-solid fa-bars"></i></button>
    <img src="assets/img/logo final.png" class="logoMobile"  alt="logo">
  </header>


  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar" >
    <img src="./assets/img/logo final.png" alt="logo" >
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

  

    <!-- OVERLAY UNTUK MENUTUP SIDEBAR SAAT MOBILE -->
  <div id="overlay" class="overlay"></div>

  <!-- MAIN -->
  <main class="main-content">
    <h3 class="fw-bold mb-4 text-center">Data Iuran Warga</h3>

   <!-- Filter Bar -->
<div class="row g-2 align-items-center mb-4">
  <div class="col-lg-2 col-md-3 col-6">
    <select id="filterKategori" class="form-select form-select-sm">
      <option value="">Semua Kategori</option>
      <option>Iuran Kas</option>
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
include 'koneksi/koneksi.php';
$cek = mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM pembayaran WHERE status_pembayaran='belum'");
$dataCek = mysqli_fetch_assoc($cek);
$jumlah_belum = $dataCek['jml'];

$query = mysqli_query($koneksi, "
  SELECT 
        pembayaran.*,warga.id_pengguna, pengguna.nama FROM pembayaran JOIN warga ON warga.id_warga = pembayaran.id_warga JOIN pengguna ON warga.id_pengguna = pengguna.id_pengguna ");

$no = 1;
while ($data = mysqli_fetch_assoc($query)) { 
?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $data['nama']; ?></td>
   <td>
  <?= 
    $data['jenis_pembayaran'] == 'kas' ? 'Iuran Kas' : 
    ucfirst($data['jenis_pembayaran']); 
  ?>
</td>

    <td><?= ucfirst($data['bulan_pembayaran']); ?></td>
    <td><?= $data['tahun_pembayaran']; ?></td>
    <td>Rp<?= number_format($data['nominal_pembayaran'], 0, ',', '.'); ?></td>

    <td>
        <?php if ($data['status_pembayaran'] == 'lunas') { ?>
            <span style="color:green;font-weight:600;">Lunas</span>
        <?php } elseif ($data['status_pembayaran'] == 'belum') { ?>
            <span style="color:red;font-weight:600;">Belum</span>
        <?php } else { ?>
            <span style="color:orange;font-weight:600;">Menunggu</span>
        <?php } ?>
    </td>

    <td>
    <?php if ($data['status_pembayaran'] == 'menunggu') { ?>

        <!-- Detail -->
        <button 
            class="btn btn-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#detailModal"
            data-id="<?= $data['id_pembayaran']; ?>"
            data-nama="<?= $data['nama']; ?>"
            data-kategori="<?= $data['jenis_pembayaran']; ?>"
            data-bulan="<?= $data['bulan_pembayaran']; ?>"
            data-tahun="<?= $data['tahun_pembayaran']; ?>"
            data-nominal="<?= $data['nominal_pembayaran']; ?>"
            data-status="<?= $data['status_pembayaran']; ?>"
            data-bukti="<?= $data['bukti_pembayaran']; ?>">
            <i class="fa fa-eye"></i> Detail
        </button>

        <!-- Konfirmasi -->
        <a href="aksi/konfirmasi_iuran.php?id=<?= $data['id_pembayaran']; ?>"
            class="btn btn-success btn-sm"
            onclick="return confirm('Yakin ingin mengkonfirmasi pembayaran ini?')">
            <i class="fa fa-check"></i> Konfirmasi
        </a>

        <!-- Tolak -->
        <a href="aksi/tolak_iuran.php?id=<?= $data['id_pembayaran']; ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Yakin ingin menolak pembayaran ini?')">
            <i class="fa fa-times"></i> Tolak
        </a>


    <?php } elseif ($data['status_pembayaran'] == 'belum') { ?>

        <!-- TOMBOL TAMBAH -->
        <button 
            class="btn btn-warning btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#tambahModal"
            data-id="<?= $data['id_pembayaran']; ?>"
            data-nama="<?= $data['nama']; ?>"
            data-kategori="<?= $data['jenis_pembayaran']; ?>"
            data-bulan="<?= $data['bulan_pembayaran']; ?>"
            data-tahun="<?= $data['tahun_pembayaran']; ?>">
            <i class="fa fa-plus"></i> Tambah
        </button>

    <?php } else { ?>

        <!-- Jika lunas -->
        -
        
    <?php } ?>
</td>
</tr>
</tr>
<?php } ?>
</tbody>
      </table>
    </div>
  </main>
  <button class="floating-btn position-relative" id="notifyBtn">
    <i class="fa-solid fa-bell"></i>
    <span id="notifBadge"
        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        <?= $jumlah_belum; ?>
    </span>
</button>

<!-- TOAST NOTIF -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="notifToast" class="toast text-bg-warning border-0">
    <div class="d-flex">
      <div class="toast-body">
        Notifikasi dikirim ke semua warga yang belum membayar!
      </div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

  <!-- Modal Konfirmasi/Tolak -->
  <div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">Konfirmasi Aksi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center">
            <p id="confirmText">Apakah Anda yakin  ingin mengkonfirmasi pembayaran ini??</p>
            <p id="tolakText">Apakah Anda yakin  ingin menolak pembayaran ini??</p>
            <div class="text-end">
              <button class="btn btn-success" id="confirmYes" data-bs-dismiss="modal">Ya</button>
              <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Pembayaran -->
  <div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="aksi/tambah_iuran.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">Tambah Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id_pembayaran" id="tambahId">

          <label class="form-label">Jenis Iuran</label>
          <select name="jenis" id="jenisIuran" class="form-select mb-2">
            <option value="kas">Iuran Kas - Rp50.000</option>
            <option value="keamanan">Keamanan - Rp30.000</option>
            <option value="kebersihan">Kebersihan - Rp20.000</option>
          </select>

          <label class="form-label">Upload Bukti Pembayaran</label>
          <input type="file" name="bukti" class="form-control mb-3" required>
        </div>

        <div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title fw-bold">Detail Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <p><strong>Nama:</strong> <span id="detailNama"></span></p>
          <p><strong>Kategori:</strong> <span id="detailKategori"></span></p>
          <p><strong>Bulan:</strong> <span id="detailBulan"></span></p>
          <p><strong>Tahun:</strong> <span id="detailTahun"></span></p>
          <p><strong>Nominal:</strong> Rp<span id="detailNominal"></span></p>
          <p><strong>Status:</strong> <span id="detailStatus"></span></p>
        </div>
        <div class="text-center">
          <p class="fw-semibold mb-2">Bukti Pembayaran:</p>
          <img id="detailBukti" src="./assets/img/bukti1.jpg" alt="Bukti Pembayaran"
            class="img-fluid rounded shadow-sm" style="max-height: 250px;">
          <p id="noBuktiText" class="text-muted fst-italic">Belum ada bukti pembayaran.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script> 
 <script>
  // Toast untuk bel notifikasi
document.getElementById("notifyBtn").addEventListener("click", function () {
    alert("Notifikasi dikirim ke semua warga yang belum membayar!");
});


  // FILTER FUNCTION
    const filterKategori = document.getElementById("filterKategori");
    const filterBulan = document.getElementById("filterBulan");
    const filterTahun = document.getElementById("filterTahun");
    const filterStatus = document.getElementById("filterStatus");
    const searchInput = document.getElementById("searchInput");

function applyFilters() {
    const kategoriVal = filterKategori.value.toLowerCase();
    const bulanVal = filterBulan.value.toLowerCase();
    const tahunVal = filterTahun.value;
    const statusVal = filterStatus.value.toLowerCase();
    const searchVal = searchInput.value.toLowerCase();

    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        const nama = row.cells[1].innerText.toLowerCase();
        const kategori = row.cells[2].innerText.toLowerCase();
        const bulan = row.cells[3].innerText.toLowerCase();
        const tahun = row.cells[4].innerText;
        const status = row.cells[6].innerText.toLowerCase();

        // Cek apakah row sesuai filter
        const show = 
            (kategoriVal === "" || kategori === kategoriVal) &&
            (bulanVal === "" || bulan === bulanVal) &&
            (tahunVal === "" || tahun === tahunVal) &&
            (statusVal === "" || status === statusVal) &&
            (searchVal === "" || nama.includes(searchVal));

        row.style.display = show ? "" : "none"; // tampilkan atau sembunyikan
    });
}

// Pasang event listener untuk semua filter
[filterKategori, filterBulan, filterTahun, filterStatus, searchInput].forEach(el =>
    el.addEventListener("input", applyFilters)
);

 
  var tambahModal = document.getElementById('tambahModal')
tambahModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget

  var id = button.getAttribute('data-id')
  // var nama = button.getAttribute('data-nama') // opsional

  var inputId = tambahModal.querySelector('#tambahId')
  inputId.value = id
})

var detailModal = document.getElementById('detailModal')
detailModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget

    var nama = button.getAttribute('data-nama')
    var kategori = button.getAttribute('data-kategori')
    var bulan = button.getAttribute('data-bulan')
    var tahun = button.getAttribute('data-tahun')
    var nominal = button.getAttribute('data-nominal')
    var status = button.getAttribute('data-status')
    var bukti = button.getAttribute('data-bukti')

    detailModal.querySelector('#detailNama').textContent = nama
    detailModal.querySelector('#detailKategori').textContent = kategori
    detailModal.querySelector('#detailBulan').textContent = bulan
    detailModal.querySelector('#detailTahun').textContent = tahun
    detailModal.querySelector('#detailNominal').textContent = nominal
    detailModal.querySelector('#detailStatus').textContent = status

    var imgBukti = detailModal.querySelector('#detailBukti')
    var noBuktiText = detailModal.querySelector('#noBuktiText')

    if (bukti && bukti !== "") {
        imgBukti.src = "./uploads/" + bukti
        imgBukti.style.display = "block"
        noBuktiText.style.display = "none"
    } else {
        imgBukti.style.display = "none"
        noBuktiText.style.display = "block"
    }
})

</script>

</body>
</html>
