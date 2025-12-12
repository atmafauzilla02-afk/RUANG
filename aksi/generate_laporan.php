<?php
ob_start();
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

$bulan_tahun = trim($_POST['bulan']); // "Januari 2025"
list($namaBulan, $tahun) = explode(' ', $bulan_tahun);

$daftarBulan = [
    'Januari'   => '01', 'Februari' => '02', 'Maret'    => '03',
    'April'     => '04', 'Mei'      => '05', 'Juni'     => '06',
    'Juli'      => '07', 'Agustus'  => '08', 'September'=> '09',
    'Oktober'   => '10', 'November' => '11', 'Desember' => '12'
];

if (!isset($daftarBulan[$namaBulan])) {
    echo json_encode(['success' => false, 'message' => 'Nama bulan tidak valid']);
    exit;
}

$bulanAngka = $daftarBulan[$namaBulan];
$namaFile   = "Laporan_" . $namaBulan . "_" . $tahun . ".pdf";
$folder     = "../uploads/laporan/";
$path       = $folder . $namaFile;

if (!is_dir($folder)) mkdir($folder, 0755, true);

$tanggal_cetak_sekarang = date('d F Y');

// ===================================================================
// 1. HITUNG SALDO AWAL (Akumulasi dari SEMUA transaksi sebelum bulan ini)
// ===================================================================
$bulanAngkaInt = (int)$bulanAngka;
$tahunInt = (int)$tahun;

// Hitung TOTAL PEMASUKAN dari semua transaksi SEBELUM bulan laporan
$sql_pemasukan_sebelumnya = "
    SELECT COALESCE(SUM(nominal_pembayaran), 0) as total 
    FROM pembayaran 
    WHERE status_pembayaran = 'lunas'
    AND (
        (CAST(tahun_pembayaran AS UNSIGNED) < ?) 
        OR (
            CAST(tahun_pembayaran AS UNSIGNED) = ? 
            AND MONTH(STR_TO_DATE(CONCAT('01 ', bulan_pembayaran, ' ', tahun_pembayaran), '%d %M %Y')) < ?
        )
    )
";
$stmt_in_prev = $koneksi->prepare($sql_pemasukan_sebelumnya);
$stmt_in_prev->bind_param("iii", $tahunInt, $tahunInt, $bulanAngkaInt);
$stmt_in_prev->execute();
$total_pemasukan_sebelumnya = $stmt_in_prev->get_result()->fetch_assoc()['total'];

// Hitung TOTAL PENGELUARAN dari semua transaksi SEBELUM bulan laporan
$sql_pengeluaran_sebelumnya = "
    SELECT COALESCE(SUM(nominal_pengeluaran), 0) as total 
    FROM pengeluaran 
    WHERE status_persetujuan = 'Disetujui'
    AND (
        (YEAR(tanggal_pengeluaran) < ?) 
        OR (YEAR(tanggal_pengeluaran) = ? AND MONTH(tanggal_pengeluaran) < ?)
    )
";
$stmt_out_prev = $koneksi->prepare($sql_pengeluaran_sebelumnya);
$stmt_out_prev->bind_param("iii", $tahunInt, $tahunInt, $bulanAngkaInt);
$stmt_out_prev->execute();
$total_pengeluaran_sebelumnya = $stmt_out_prev->get_result()->fetch_assoc()['total'];

// SALDO AWAL = Total Pemasukan (semua bulan sebelumnya) - Total Pengeluaran (semua bulan sebelumnya)
$saldo_awal = $total_pemasukan_sebelumnya - $total_pengeluaran_sebelumnya;

// ===================================================================
// 2. TOTAL & DETAIL PEMASUKAN BULAN INI
// ===================================================================
$sql_pemasukan = "SELECT COALESCE(SUM(nominal_pembayaran), 0) as total 
                  FROM pembayaran 
                  WHERE bulan_pembayaran = ? 
                    AND tahun_pembayaran = ? 
                    AND status_pembayaran = 'lunas'";
$stmt = $koneksi->prepare($sql_pemasukan);
$stmt->bind_param("ss", $namaBulan, $tahun);
$stmt->execute();
$pemasukan = $stmt->get_result()->fetch_assoc()['total'];

$sql_detail_pemasukan = "
    SELECT p.tanggal_pembayaran, pg.nama, p.jenis_pembayaran, p.nominal_pembayaran
    FROM pembayaran p
    JOIN warga w ON p.id_warga = w.id_warga
    JOIN pengguna pg ON w.id_pengguna = pg.id_pengguna
    WHERE p.bulan_pembayaran = ? AND p.tahun_pembayaran = ? AND p.status_pembayaran = 'lunas'
    ORDER BY p.tanggal_pembayaran ASC";
$stmt_in = $koneksi->prepare($sql_detail_pemasukan);
$stmt_in->bind_param("ss", $namaBulan, $tahun);
$stmt_in->execute();
$result_detail_pemasukan = $stmt_in->get_result();

// ===================================================================
// 3. TOTAL & DETAIL PENGELUARAN BULAN INI
// ===================================================================
$sql_pengeluaran = "SELECT COALESCE(SUM(nominal_pengeluaran), 0) as total 
                    FROM pengeluaran 
                    WHERE MONTH(tanggal_pengeluaran) = ? 
                      AND YEAR(tanggal_pengeluaran) = ?
                      AND status_persetujuan = 'Disetujui'";
$stmt_out = $koneksi->prepare($sql_pengeluaran);
$stmt_out->bind_param("ii", $bulanAngka, $tahun);
$stmt_out->execute();
$pengeluaran = $stmt_out->get_result()->fetch_assoc()['total'];

$sql_detail_pengeluaran = "
    SELECT tanggal_pengeluaran, nama_pengeluaran, jenis_pengeluaran, nominal_pengeluaran
    FROM pengeluaran
    WHERE MONTH(tanggal_pengeluaran) = ? AND YEAR(tanggal_pengeluaran) = ? AND status_persetujuan = 'Disetujui'
    ORDER BY tanggal_pengeluaran ASC";
$stmt_out_detail = $koneksi->prepare($sql_detail_pengeluaran);
$stmt_out_detail->bind_param("ii", $bulanAngka, $tahun);
$stmt_out_detail->execute();
$result_detail_pengeluaran = $stmt_out_detail->get_result();

// ===================================================================
// 4. HITUNG SALDO AKHIR
// ===================================================================
// Rumus: Saldo Awal + Pemasukan - Pengeluaran
$saldo_akhir = $saldo_awal + $pemasukan - $pengeluaran;

// ===================================================================
// 5. NAMA PENANDATANGAN
// ===================================================================
$nama_ketua = 'Ketua RT';
$nama_bendahara = 'Bendahara';
$q = mysqli_query($koneksi, "SELECT nama, role FROM pengguna WHERE role IN ('ketua','bendahara') AND status='Aktif'");
while ($r = mysqli_fetch_assoc($q)) {
    if ($r['role'] === 'ketua')     $nama_ketua = $r['nama'];
    if ($r['role'] === 'bendahara') $nama_bendahara = $r['nama'];
}

// ===================================================================
// 6. PDF GENERATION
// ===================================================================
class PDF extends FPDF {
    private $periode;
    function __construct($periode) {
        parent::__construct();
        $this->periode = $periode;
    }
    function Header() {
        if (file_exists('../assets/img/logo final.png')) {
            $this->Image('../assets/img/logo final.png', 10, 6, 25);
        }
        $this->SetFont('Arial','B',18);
        $this->Cell(0,10,'LAPORAN KEUANGAN BULANAN',0,1,'C');
        $this->SetFont('Arial','B',14);
        $this->Cell(0,8,'RT 01 / RW 02',0,1,'C');
        $this->SetFont('Arial','',12);
        $this->Cell(0,8,'Periode: '.$this->periode,0,1,'C');
        $this->SetDrawColor(245,200,59);
        $this->SetLineWidth(0.8);
        $this->Line(10,40,200,40);
        $this->Ln(8);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo().' - Dicetak: '.date('d/m/Y H:i'),0,0,'C');
    }
    function FancyTable($header,$data,$w) {
        $this->SetFillColor(245,200,59);
        $this->SetTextColor(0);
        $this->SetFont('Arial','B',10);
        foreach($header as $i=>$h) $this->Cell($w[$i],7,$h,1,0,'C',true);
        $this->Ln();
        $this->SetFont('Arial','',9);
        $fill=false;
        if(empty($data)){
            $this->Cell(array_sum($w),7,'Tidak ada data',1,1,'C');
            return;
        }
        foreach($data as $row){
            foreach($row as $i=>$col)
                $this->Cell($w[$i],6,$col,'LR',0, $i==count($row)-1?'R':'L', $fill);
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w),0,'','T');
    }
    function SummaryBox($label,$value,$rgb){
        $this->SetFillColor($rgb[0],$rgb[1],$rgb[2]);
        $this->SetTextColor(255);
        $this->SetFont('Arial','B',11);
        $this->Cell(70,8,$label,1,0,'L',true);
        $this->SetFont('Arial','B',12);
        $this->Cell(0,8,$value,1,1,'R',true);
        $this->SetTextColor(0);
    }
}

$pdf = new PDF($bulan_tahun);
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
$pdf->SetTextColor(245,200,59);
$pdf->Cell(0,10,'RINGKASAN KEUANGAN',0,1,'L');
$pdf->SetTextColor(0);
$pdf->Ln(3);

// Tampilkan Saldo Awal
$pdf->SummaryBox('Saldo Awal','Rp '.number_format($saldo_awal,0,',','.'),[158,158,158]);
$pdf->Ln(1);
$pdf->SummaryBox('Total Pemasukan','Rp '.number_format($pemasukan,0,',','.'),[76,175,80]);
$pdf->Ln(1);
$pdf->SummaryBox('Total Pengeluaran','Rp '.number_format($pengeluaran,0,',','.'),[244,67,54]);
$pdf->Ln(1);
$pdf->SummaryBox('SALDO AKHIR','Rp '.number_format($saldo_akhir,0,',','.'),[33,150,243]);
$pdf->Ln(10);

$pdf->SetFont('Arial','B',14);
$pdf->SetTextColor(245,200,59);
$pdf->Cell(0,10,'DETAIL PEMASUKAN',0,1,'L');
$pdf->SetTextColor(0);
$pdf->Ln(3);

$header_in = ['No','Tanggal','Nama Warga','Jenis','Nominal'];
$w_in = [10,30,70,40,40];
$data_in = [];
$no=1;
while($r = $result_detail_pemasukan->fetch_assoc()){
    $data_in[] = [
        $no++,
        date('d/m/Y', strtotime($r['tanggal_pembayaran'])),
        $r['nama'],
        ucfirst($r['jenis_pembayaran']),
        'Rp '.number_format($r['nominal_pembayaran'],0,',','.')
    ];
}
$pdf->FancyTable($header_in, $data_in, $w_in);
$pdf->Ln(10);

$pdf->SetFont('Arial','B',14);
$pdf->SetTextColor(245,200,59);
$pdf->Cell(0,10,'DETAIL PENGELUARAN',0,1,'L');
$pdf->SetTextColor(0);
$pdf->Ln(3);

$header_out = ['No','Tanggal','Keterangan','Jenis','Nominal'];
$w_out = [10,30,70,40,40];
$data_out = [];
$no=1;
while($r = $result_detail_pengeluaran->fetch_assoc()){
    $data_out[] = [
        $no++,
        date('d/m/Y', strtotime($r['tanggal_pengeluaran'])),
        $r['nama_pengeluaran'],
        ucfirst($r['jenis_pengeluaran']),
        'Rp '.number_format($r['nominal_pengeluaran'],0,',','.')
    ];
}
$pdf->FancyTable($header_out, $data_out, $w_out);
$pdf->Ln(15);

$pdf->SetFont('Arial','',10);
$pdf->Cell(95,6,'Mengetahui,',0,0,'C');
$pdf->Cell(95,6,'Batam, '.$tanggal_cetak_sekarang,0,1,'C');

$pdf->Cell(95,6,'Ketua RT',0,0,'C');
$pdf->Cell(95,6,'Bendahara',0,1,'C');

$pdf->Ln(15);
$pdf->SetFont('Arial','BU',11);
$pdf->Cell(95,6,$nama_ketua,0,0,'C');
$pdf->Cell(95,6,$nama_bendahara,0,1,'C');

$pdf->Output('F', $path);

// Simpan ke database
$stmt_save = $koneksi->prepare("
    INSERT INTO laporan 
    (bulan_tahun, tahun, bulan_nama, bulan_angka, nama_file, path_file, generated_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
        nama_file=VALUES(nama_file),
        path_file=VALUES(path_file),
        generated_at=NOW()
");
$stmt_save->bind_param("ssssss", $bulan_tahun, $tahun, $namaBulan, $bulanAngka, $namaFile, $path);
$stmt_save->execute();
$stmt_save->close();

ob_end_clean();
echo json_encode([
    'success' => true,
    'message' => "Laporan $bulan_tahun berhasil digenerate!",
    'file' => $namaFile,
    'saldo_awal' => $saldo_awal,
    'saldo_akhir' => $saldo_akhir
]);
?>