<?php
ob_start();
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['success' => false, 'message' => 'Login diperlukan']);
    exit;
}

require_once '../koneksi/koneksi.php';
require_once '../assets/fpdf/fpdf.php';

if (!isset($_POST['bulan']) || empty(trim($_POST['bulan']))) {
    echo json_encode(['success' => false, 'message' => 'Bulan tidak valid']);
    exit;
}

$bulan_tahun = trim($_POST['bulan']);
list($namaBulan, $tahun) = explode(' ', $bulan_tahun);

$daftarBulan = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
$index = array_search($namaBulan, $daftarBulan);

if ($index === false) {
    echo json_encode(['success' => false, 'message' => 'Nama bulan tidak valid']);
    exit;
}

$bulanAngka = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
$namaFile   = "laporan_{$tahun}_{$bulanAngka}.pdf";
$folder     = "../uploads/laporan/";
$path       = $folder . $namaFile;

if (!is_dir($folder)) mkdir($folder, 0755, true);

$pemasukan = 0;
$pengeluaran = 0;

$sql = "SELECT COALESCE(SUM(nominal_pembayaran), 0) as total 
        FROM pembayaran 
        WHERE MONTH(tanggal_pembayaran) = ? 
          AND YEAR(tanggal_pembayaran) = ? 
          AND status_pembayaran = 'lunas'";

if ($stmt = $koneksi->prepare($sql)) {
    $stmt->bind_param("ii", $bulanAngka, $tahun);
    $stmt->execute();
    $result = $stmt->get_result();
    $pemasukan = $result->fetch_assoc()['total'];
    $stmt->close();
}

$pengeluaran = 0;
if ($koneksi->query("SHOW TABLES LIKE 'pengeluaran'")->num_rows > 0) {
    $sql2 = "SELECT COALESCE(SUM(nominal_pengeluaran), 0) as total 
             FROM pengeluaran 
             WHERE MONTH(tanggal_pengeluaran) = ? AND YEAR(tanggal_pengeluaran) = ?";
    if ($stmt2 = $koneksi->prepare($sql2)) {
        $stmt2->bind_param("ii", $bulanAngka, $tahun);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $pengeluaran = $res2->fetch_assoc()['total'];
        $stmt2->close();
    }
}

$saldo_akhir = $pemasukan - $pengeluaran;

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->Cell(0,10,'LAPORAN KEUANGAN BULANAN',0,1,'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

$pdf->Cell(0,10,"Periode: $bulan_tahun",0,1);
$pdf->Ln(8);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,"RINCIAN KEUANGAN",0,1);
$pdf->SetFont('Arial','',12);

$pdf->Cell(0,10,"Total Pemasukan  : Rp " . number_format($pemasukan, 0, ',', '.'),0,1);
$pdf->Cell(0,10,"Total Pengeluaran: Rp " . number_format($pengeluaran, 0, ',', '.'),0,1);
$pdf->SetFont('Arial','B',13);
$pdf->Cell(0,12,"SALDO AKHIR      : Rp " . number_format($saldo_akhir, 0, ',', '.'),0,1);
$pdf->SetFont('Arial','',12);

$pdf->Ln(10);
$pdf->Cell(0,10,"Laporan digenerate otomatis pada " . date('d F Y H:i'),0,1);

$pdf->Output('F', $path);

$stmt = $koneksi->prepare("
    INSERT INTO laporan (bulan_tahun, tahun, bulan_nama, bulan_angka, nama_file, path_file)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        nama_file = VALUES(nama_file),
        path_file = VALUES(path_file)
");

if ($stmt) {
    $stmt->bind_param("ssssss", $bulan_tahun, $tahun, $namaBulan, $bulanAngka, $namaFile, $path);
    $stmt->execute();
    $stmt->close();
}

ob_end_clean();
echo json_encode([
    'success' => true,
    'message' => "Laporan $bulan_tahun berhasil digenerate!",
    'file' => $namaFile
]);
?>