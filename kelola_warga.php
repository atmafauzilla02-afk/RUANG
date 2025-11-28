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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola Data Warga</title>

  <link href="./assets/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(rgba(255, 255, 255, 0.85), rgba(253, 226, 119, 0.55)),
                  url(./assets/img/batik\ awan\ kuning\ bg.jpg);
      background-size: cover;
      background-position: center;
      min-height: 100vh;
      margin: 0;
      overflow-x: hidden;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      width: 240px;
      height: 100vh;
      background: linear-gradient(180deg, #f5c83b, #caa43b);
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      padding: 20px 0;
      display: flex;
      flex-direction: column;
      transition: left 0.3s ease;
    }

    .sidebar.hidden {
      left: -240px;
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

    .logout-btn:hover { background-color: #222; }

    /* ===== MAIN CONTENT ===== */
    .main-content {
       margin-left: 240px;
      padding: 90px 30px 30px;
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }

    /* ===== TABLE ===== */
    .table {
      min-width: 720px;
      border-radius: 12px;
      overflow: hidden;
    }

    .table th {
      background-color: #f4c430 !important;
      color: #000;
      font-weight: 600;
      text-align: center;
    }

    /* ===== TOGGLE BUTTON ===== */
    #toggleSidebar {
      background: none;
      border: none;
      font-size: 1.5rem;
      color: #333;
      cursor: pointer;
      display: none;
    }

    /* ===== FILTER BAR ===== */
    .filter-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 20px;
    }

    .filter-bar .search-group {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .filter-bar input {
      max-width: 220px;
    }

    .btn-tambah {
      background-color: #ffc107;
      color: #000;
      font-weight: 600;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
      #toggleSidebar {
        display: inline-block;
      }

      .sidebar {
        left: -240px;
      }

      .sidebar.active {
        left: 0;
      }

      .main-content {
        margin-left: 0;
      }

      .header-top {
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        gap: 10px;
      }

      .filter-bar {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-bar .search-group {
        width: 100%;
        justify-content: space-between;
      }

      .filter-bar .btn-tambah {
        width: 100%;
      }
    }

    @media (max-width: 576px) {
      h4 {
        font-size: 1.2rem;
      }

      button.btn {
        font-size: 0.85rem;
      }

      .modal-dialog {
        max-width: 95%;
        margin: auto;
      }

    .header-top {
      text-align: center;
      margin-bottom: 30px;
    }

    .header-top h4 {
      font-weight: 700;
      color: #000;
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

     @media (max-width: 992px) {
      .sidebar {
        left: -240px;
      }

      .main-content {
        margin-left: 0;
        padding-top: 100px;
      }
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


   <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
     <img src="./assets/img/logo final.png" alt="logo" >
    <hr>
    <ul class="nav flex-column mt-4">
      <li><a href="dashboardBendahara.php" class="nav-link"><i class="fa-solid fa-house me-2"></i>Dashboard</a></li>
      <li><a href="iuran.php" class="nav-link"><i class="fa-solid fa-wallet me-2"></i>Iuran</a></li>
      <li><a href="kelola_warga.php" class="nav-link active"><i class="fa-solid fa-users me-2"></i>Kelola Warga</a></li>
      <li><a href="Pengajuan.php" class="nav-link"><i class="fa-solid fa-file-import me-2"></i>Pengajuan</a></li>
      <li><a href="laporanBendahara.php" class="nav-link"><i class="fa-solid fa-file-lines me-2"></i>Laporan</a></li>
    </ul>
    <button class="logout-btn" onclick="logout()">
      <i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out
    </button>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div class="container py-4">
      <!-- HEADER -->
      <div class="header-top">
        <h4 class="fw-bold text-dark m-0"><i class="fa-solid fa-users me-2"></i>Kelola Data Warga</h4>
      </div>

      <!-- FILTER BAR -->
      <div class="filter-bar">
        <div class="search-group">
          <input type="text" id="searchNama" class="form-control" placeholder="🔍 Cari nama warga..." />
          <input type="text" id="searchNIK" class="form-control" placeholder="🔍 Cari NIK..." />
        </div>
        <button type="button" class="btn btn-tambah" id="btnTambahWarga">
            <i class="fa-solid fa-plus me-1"></i> Tambah Warga
        </button>
      </div>

      <!-- TABLE -->
      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center shadow-sm rounded">
          <thead>
            <tr>
              <th>Nama</th>
              <th>NIK</th>
              <th>Alamat</th>
              <th>No Telepon</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="wargaList">
             <?php 
                   include 'koneksi/koneksi.php';
                    $query = mysqli_query($koneksi, "SELECT w.id_warga, p.nama, p.nik, p.alamat, p.no_telp FROM warga w JOIN pengguna p ON w.id_pengguna = p.id_pengguna"); 
                    while ($data = mysqli_fetch_assoc($query)) { 
                    ?> 
                        <tr> 
                            <td><?php echo $data['nama']; ?></td> 
                            <td><?php echo $data['nik']; ?></td> 
                            <td><?php echo $data['alamat']; ?></td> 
                            <td><?php echo $data['no_telp']; ?></td> 
                            <td>
                                <a href="kelola_warga.php?edit=<?= $data['id_warga']; ?>" class="btn btn-warning btn-sm">
                                <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="aksi/hapus_kelola_warga.php" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini?');">
                                  <input type="hidden" name="id" value="<?= $data['id_warga']; ?>">
                                  <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                                </form>

                            </td>
                        </tr> 
              <?php } ?>
                      
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <?php
$editId = 0;
$editNama = $editNIK = $editAlamat = $editTelp = "";

if(isset($_GET['edit'])){
    $editId = $_GET['edit'];
    $queryEdit = mysqli_query($koneksi, "SELECT p.nama, p.nik, p.alamat, p.no_telp FROM warga w JOIN pengguna p ON w.id_pengguna = p.id_pengguna WHERE w.id_warga='$editId'");
    $row = mysqli_fetch_assoc($queryEdit);
    $editNama = $row['nama'];
    $editNIK = $row['nik'];
    $editAlamat = $row['alamat'];
    $editTelp = $row['no_telp'];
}
?>

  <!-- MODAL EDIT WARGA -->
  <div class="modal fade <?= $editId ? 'show' : '' ?>" id="modalWarga" tabindex="-1" <?= $editId ? 'style="display:block;"' : '' ?> aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content rounded-4 shadow-lg">
        <div class="modal-header bg-warning border-0">
          <h5 class="modal-title fw-bold" id="modalTitle"><?= $editId ? 'Edit Warga' : 'Tambah Warga' ?></h5>
          <a href="kelola_warga.php" class="btn-close"></a>
        </div>
        <div class="modal-body">
          <form action="<?= $editId ? 'aksi/edit_kelola_warga.php' : 'aksi/tambah_kelola_warga.php' ?>" method="POST">
            <input type="hidden" name="id_warga" value="<?= $editId ?>">

            <label for="nama_warga" class="form-label">Nama</label>
            <input type="text" name="nama_warga" class="form-control mb-2" required value="<?= $editNama ?>">

            <label for="nik" class="form-label">NIK</label>
            <input type="text" name="nik" class="form-control mb-2" required value="<?= $editNIK ?>">

            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" name="alamat" class="form-control mb-2" required value="<?= $editAlamat ?>">

            <label for="no_telp" class="form-label">No Telepon</label>
            <input type="text" name="no_telp" class="form-control mb-3" required value="<?= $editTelp ?>">

            <button type="submit" class="btn btn-success w-100 fw-semibold">
                <?= $editId ? "Update" : "Simpan" ?>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- MODAL TAMBAH WARGA-->
  <div class="modal fade" id="modalWarga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content rounded-4 shadow-lg">
        <div class="modal-header bg-warning border-0">
          <h5 class="modal-title fw-bold" id="modalTitle">Tambah Warga</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="aksi/tambah_kelola_warga.php" method="POST" id="wargaForm">
            <input type="hidden" id="wargaIndex" />

            <label for="nama_warga" class="form-label">Nama</label>
            <input type="text" id="nama_warga" name="nama_warga" class="form-control mb-2" required />

            <label for="nik" class="form-label">NIK</label>
            <input type="text" id="nik" name="nik" class="form-control mb-2" required />

            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" id="alamat" name="alamat" class="form-control mb-2" required />

            <label for="no_telp" class="form-label">No Telepon</label>
            <input type="text" id="no_telp" name="no_telp" class="form-control mb-3" required />

            <button type="submit" class="btn btn-success w-100 fw-semibold">Simpan</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="./assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script>

// === FILTER CARI NAMA ===
document.getElementById("searchNama").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#wargaList tr");

    rows.forEach(row => {
        let nama = row.cells[0].textContent.toLowerCase();
        row.style.display = nama.includes(filter) ? "" : "none";
    });
});

// === FILTER CARI NIK ===
document.getElementById("searchNIK").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#wargaList tr");

    rows.forEach(row => {
        let nik = row.cells[1].textContent.toLowerCase();
        row.style.display = nik.includes(filter) ? "" : "none";
    });
});

// === TOGGLE SIDEBAR MOBILE ===
document.getElementById("menuToggle").addEventListener("click", function () {
    document.getElementById("sidebar").classList.toggle("active");
});
</script>

<script>
// KLIK TOMBOL TAMBAH → BUKA MODAL DENGAN FORM KOSONG
document.getElementById('btnTambahWarga')?.addEventListener('click', function() {
    // Bersihkan URL dari ?edit tanpa reload
    history.replaceState({}, '', 'kelola_warga.php');
    
    // Ubah judul modal
    document.getElementById('modalTitle').textContent = 'Tambah Warga';
    
    // Kosongkan semua field
    document.querySelectorAll('#formWarga input[name]').forEach(input => {
        if (input.name !== 'id_pengguna') input.value = '';
    });
    
    // Pastikan action form ke tambah
    document.getElementById('formWarga').action = 'aksi/tambah_kelola_warga.php';
    
    // Hapus input hidden id_pengguna jika ada
    const hiddenId = document.querySelector('#formWarga input[name="id_pengguna"]');
    if (hiddenId) hiddenId.remove();
    
    // Buka modal
    const modalElement = document.getElementById('modalWarga');
    const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
    modal.show();
});
</script>

</body>
</html>


