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
    bottom: 20px !important; 
    right: 20px !important;  
    left: auto !important;  
    background: linear-gradient(135deg, #f5c83b, #caa43b);
    color: white;
    border: none;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    font-size: 1.6rem;
    z-index: 5000 !important;
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

<!-- ambil detail -->
<?php
$detail = null;

if (isset($_GET['detail'])) {
    $id = $_GET['detail'];

    $detail = mysqli_query($koneksi, "
    SELECT p.*, pg.nama AS nama_warga, p.bukti_pembayaran
    FROM pembayaran p
    JOIN warga w ON p.id_warga = w.id_warga
    JOIN pengguna pg ON w.id_pengguna = pg.id_pengguna
    WHERE p.id_pembayaran = '$id'
")->fetch_assoc();

}
?>

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

<!-- Tombol BUAT IURAN -->
<button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambahIuran">
<i class="fa fa-plus"></i> Buat Iuran
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
$query = mysqli_query($koneksi,"
SELECT p.*, pg.nama 
FROM pembayaran p
JOIN warga w ON p.id_warga = w.id_warga
JOIN pengguna pg ON w.id_pengguna = pg.id_pengguna
ORDER BY p.tahun_pembayaran DESC, p.bulan_pembayaran ASC
");

$no=1;
while($data=mysqli_fetch_assoc($query)){
?>
<tr>
<td><?= $no++; ?></td>
<td><?= $data['nama']; ?></td>
<td><?= ucfirst($data['jenis_pembayaran']); ?></td>
<td><?= ucfirst($data['bulan_pembayaran']); ?></td>
<td><?= $data['tahun_pembayaran']; ?></td>
<td>Rp<?= number_format($data['nominal_pembayaran'],0,',','.'); ?></td>

<!--MODAL TAMBAH BUKTI-->
<div class="modal fade" id="tambahBukti<?= $data['id_pembayaran']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form action="aksi/upload_bukti.php" method="POST" enctype="multipart/form-data">

        <div class="modal-header bg-warning">
          <h5 class="modal-title">Upload Bukti Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="id_pembayaran" value="<?= $data['id_pembayaran']; ?>">

          <!-- Tampilkan kategori -->
          <div class="mb-2">
            <label class="fw-bold">Jenis Iuran</label>
            <input type="text" class="form-control" value="<?= ucfirst($data['jenis_pembayaran']); ?>" readonly>
          </div>

          <!-- Tampilkan nominal -->
          <div class="mb-2">
            <label class="fw-bold">Total Pembayaran</label>
            <input type="text" class="form-control" 
              value="Rp<?= number_format($data['nominal_pembayaran'], 0, ',', '.'); ?>" readonly>
          </div>

          <!-- Input foto -->
          <label class="fw-bold">Upload Bukti (Foto)</label>
          <input type="file" name="bukti" class="form-control" required>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Upload</button>
        </div>

      </form>

    </div>
  </div>
</div>


<td>
<?php
if($data['status_pembayaran']=='lunas') {
echo "<span class='text-success fw-bold'>Lunas</span>";
}elseif($data['status_pembayaran']=='menunggu'){
echo "<span class='text-warning fw-bold'>Menunggu</span>";
}else{
echo "<span class='text-danger fw-bold'>Belum</span>";
}
?>
</td>

<td>
<?php if ($data['status_pembayaran'] == 'belum') { ?>

    <!-- TOMBOL TAMBAH (UPLOAD BUKTI) -->
    <button class="btn btn-warning btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#tambahBukti<?= $data['id_pembayaran']; ?>">
        + Tambah
    </button>

<?php } elseif ($data['status_pembayaran'] == 'menunggu') { ?>

    <!-- TOMBOL DETAIL -->
   <a href="iuran.php?detail=<?= $data['id_pembayaran']; ?>" 
   class="btn btn-primary btn-sm">Detail</a> 

    <a href="aksi/konfirmasi_iuran.php?id=<?= $data['id_pembayaran']; ?>" class="btn btn-success btn-sm">Konfirmasi</a>

    <a href="aksi/tolak_iuran.php?id=<?= $data['id_pembayaran']; ?>" class="btn btn-danger btn-sm">Tolak</a>

<?php } else { ?>

    <!-- STATUS LUNAS -->
    <span class="text-success fw-bold"> - </span>

<?php } ?>
</td>


</tr>
<?php } ?>
</tbody>
</table>
</div>
</main>

<!-- MODAL BUAT IURAN -->
<div class="modal fade" id="modalTambahIuran" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<form action="aksi/tambah_iuran_awal.php" method="POST">

<div class="modal-header bg-warning">
<h5 class="modal-title">Buat Iuran</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<label>Warga</label>
<select name="id_warga" class="form-control mb-2" required>
    <option value="">-- Pilih Warga --</option>
    <?php
    $w = mysqli_query($koneksi, "
        SELECT warga.id_warga, pengguna.nama 
        FROM warga
        JOIN pengguna ON warga.id_pengguna = pengguna.id_pengguna
    ");
    while ($x = mysqli_fetch_assoc($w)) {
        echo "<option value='{$x['id_warga']}'>{$x['nama']}</option>";
    }
    ?>
  </select>




<label>Kategori</label>
<select name="jenis" id="jenisIuran" class="form-control mb-2" onchange="setNominal()">
<option value="kas">Kas</option>
<option value="keamanan">Keamanan</option>
<option value="kebersihan">Kebersihan</option>
</select>

<label>Bulan</label>
<input name="bulan" class="form-control mb-2">

<label>Tahun</label>
<input type="number" name="tahun" class="form-control mb-2">

<label>Nominal</label>
<label>Nominal</label>
<input type="number" id="nominalIuran" name="nominal" class="form-control mb-2" readonly>


</div>

<div class="modal-footer">
<button type="submit" class="btn btn-success">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<!-- MODAL DETAIL -->
<?php if ($detail): ?>
<div class="modal fade show" id="detailModal" style="display:block; background: rgba(0,0,0,0.4);">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header bg-warning">
<h5 class="modal-title">Detail Pembayaran</h5>
<a href="iuran.php" class="btn-close"></a>
</div>

<div class="modal-body">

<p><b>Nama:</b> <?= $detail['nama_warga']; ?></p>
<p><b>Kategori:</b> <?= ucfirst($detail['jenis_pembayaran']); ?></p>
<p><b>Bulan:</b> <?= ucfirst($detail['bulan_pembayaran']); ?></p>
<p><b>Tahun:</b> <?= $detail['tahun_pembayaran']; ?></p>
<p><b>Nominal:</b> Rp<?= number_format($detail['nominal_pembayaran'],0,',','.'); ?></p>
<p><b>Status:</b> <?= ucfirst($detail['status_pembayaran']); ?></p>

<?php if ($detail['bukti_pembayaran']): ?>
    <img src="assets/bukti_pembayaran/<?= $detail['bukti_pembayaran']; ?>" 
     class="img-fluid rounded mt-2">

<?php else: ?>
    <p class="text-danger">Belum ada bukti pembayaran.</p>
<?php endif; ?>

</div>

</div>
</div>
</div>
<?php endif; ?>


<script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script>
function setNominal() {
    let jenis = document.getElementById('jenisIuran').value;
    let nominal = document.getElementById('nominalIuran');

    if (jenis === 'kas') nominal.value = 50000;
    else if (jenis === 'keamanan') nominal.value = 30000;
    else if (jenis === 'kebersihan') nominal.value = 20000;
}
</script>

<script>
// FILTER TABEL IURAN

// Ambil elemen filter
const filterKategori = document.getElementById("filterKategori");
const filterBulan    = document.getElementById("filterBulan");
const filterTahun    = document.getElementById("filterTahun");
const filterStatus   = document.getElementById("filterStatus");
const searchInput    = document.getElementById("searchInput");

// Ambil semua baris tabel
const rows = document.querySelectorAll("table tbody tr");

// FUNGSI FILTER UTAMA
function applyFilter() {
    const kategori = filterKategori.value.toLowerCase();
    const bulan    = filterBulan.value.toLowerCase();
    const tahun    = filterTahun.value.toLowerCase();
    const status   = filterStatus.value.toLowerCase();
    const search   = searchInput.value.toLowerCase();

    rows.forEach(row => {
        const namaCell     = row.cells[1].innerText.toLowerCase();
        const kategoriCell = row.cells[2].innerText.toLowerCase();
        const bulanCell    = row.cells[3].innerText.toLowerCase();
        const tahunCell    = row.cells[4].innerText.toLowerCase();
        const statusCell   = row.cells[6].innerText.toLowerCase();

        let show = true;

        if (kategori && kategoriCell !== kategori) show = false;
        if (bulan && bulanCell !== bulan) show = false;
        if (tahun && tahunCell !== tahun) show = false;
        if (status && statusCell !== status) show = false;

        if (search && !namaCell.includes(search)) show = false;

        row.style.display = show ? "" : "none";
    });
}

// filter
filterKategori.addEventListener("change", applyFilter);
filterBulan.addEventListener("change", applyFilter);
filterTahun.addEventListener("change", applyFilter);
filterStatus.addEventListener("change", applyFilter);
searchInput.addEventListener("keyup", applyFilter);


// MOBILE SIDEBAR
const sidebar  = document.getElementById("sidebar");
const menuBtn  = document.getElementById("menuToggle");
const overlay  = document.getElementById("overlay");


menuBtn.addEventListener("click", function () {
    sidebar.classList.toggle("show");
    overlay.classList.toggle("active");
});


overlay.addEventListener("click", function () {
    sidebar.classList.remove("show");
    overlay.classList.remove("active");
});
</script>



</body>
</html>