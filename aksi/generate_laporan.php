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

$bulan_tahun = trim($_POST['bulan']);
$parts = explode(' ', $bulan_tahun);

if (count($parts) !== 2) {
    echo json_encode(['success' => false, 'message' => 'Format bulan tidak valid']);
    exit;
}

list($namaBulan, $tahun) = $parts;
$tahunInt = (int)$tahun;

$tahun_sekarang = (int)date('Y');
$tahun_minimal = $tahun_sekarang - 2;

if ($tahunInt < $tahun_minimal || $tahunInt > $tahun_sekarang) {
    ob_end_clean();
    echo json_encode([
        'success' => false, 
        'message' => "Laporan hanya dapat digenerate untuk tahun $tahun_minimal - $tahun_sekarang"
    ]);
    exit;
}

$daftarBulan = [
    'Januari'   => 1,  'Februari' => 2,  'Maret'    => 3,
    'April'     => 4,  'Mei'      => 5,  'Juni'     => 6,
    'Juli'      => 7,  'Agustus'  => 8,  'September'=> 9,
    'Oktober'   => 10, 'November' => 11, 'Desember' => 12
];

$bulanIndo = [
    'January'=>'Januari', 'February'=>'Februari', 'March'=>'Maret', 
    'April'=>'April', 'May'=>'Mei', 'June'=>'Juni',
    'July'=>'Juli', 'August'=>'Agustus', 'September'=>'September', 
    'October'=>'Oktober', 'November'=>'November', 'December'=>'Desember'
];

if (!isset($daftarBulan[$namaBulan])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Nama bulan tidak valid']);
    exit;
}

$bulanAngka = $daftarBulan[$namaBulan];
$bulanAngkaStr = str_pad($bulanAngka, 2, '0', STR_PAD_LEFT);

$namaFile   = "Laporan_" . $namaBulan . "_" . $tahunInt . ".pdf";
$folder     = "../uploads/laporan/";
$path       = $folder . $namaFile;

if (!is_dir($folder)) mkdir($folder, 0755, true);

$tanggal_cetak_sekarang = date('d F Y');
$tanggal_cetak_sekarang = strtr($tanggal_cetak_sekarang, $bulanIndo);

$sql_pemasukan_tahun_lalu = "
    SELECT COALESCE(SUM(nominal_pembayaran), 0) as total 
    FROM pembayaran 
    WHERE status_pembayaran = 'lunas'
      AND CAST(tahun_pembayaran AS UNSIGNED) < ?
";
$stmt1 = $koneksi->prepare($sql_pemasukan_tahun_lalu);
if (!$stmt1) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error prepare: ' . $koneksi->error]);
    exit;
}
$stmt1->bind_param("i", $tahunInt);
$stmt1->execute();
$pemasukan_tahun_lalu = $stmt1->get_result()->fetch_assoc()['total'];
$stmt1->close();

$sql_pemasukan_bulan_lalu = "
    SELECT COALESCE(SUM(nominal_pembayaran), 0) as total 
    FROM pembayaran 
    WHERE status_pembayaran = 'lunas'
      AND CAST(tahun_pembayaran AS UNSIGNED) = ?
      AND bulan_pembayaran IN (?)
";

$bulan_sebelumnya = [];
foreach ($daftarBulan as $nama => $angka) {
    if ($angka < $bulanAngka) {
        $bulan_sebelumnya[] = "'" . $nama . "'";
    }
}

if (count($bulan_sebelumnya) > 0) {
    $bulan_in_clause = implode(',', $bulan_sebelumnya);
    $sql_pemasukan_bulan_lalu_final = "
        SELECT COALESCE(SUM(nominal_pembayaran), 0) as total 
        FROM pembayaran 
        WHERE status_pembayaran = 'lunas'
          AND CAST(tahun_pembayaran AS UNSIGNED) = ?
          AND bulan_pembayaran IN ($bulan_in_clause)
    ";
    $stmt2 = $koneksi->prepare($sql_pemasukan_bulan_lalu_final);
    if (!$stmt2) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Error prepare: ' . $koneksi->error]);
        exit;
    }
    $stmt2->bind_param("i", $tahunInt);
    $stmt2->execute();
    $pemasukan_bulan_lalu = $stmt2->get_result()->fetch_assoc()['total'];
    $stmt2->close();
} else {
    $pemasukan_bulan_lalu = 0;
}

$total_pemasukan_sebelumnya = $pemasukan_tahun_lalu + $pemasukan_bulan_lalu;

$sql_pengeluaran_sebelumnya = "
    SELECT COALESCE(SUM(nominal_pengeluaran), 0) as total 
    FROM pengeluaran 
    WHERE status_persetujuan = 'Disetujui'
      AND (
          YEAR(tanggal_pengeluaran) < ? 
          OR (YEAR(tanggal_pengeluaran) = ? AND MONTH(tanggal_pengeluaran) < ?)
      )
";
$stmt3 = $koneksi->prepare($sql_pengeluaran_sebelumnya);
if (!$stmt3) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error prepare: ' . $koneksi->error]);
    exit;
}
$stmt3->bind_param("iii", $tahunInt, $tahunInt, $bulanAngka);
$stmt3->execute();
$total_pengeluaran_sebelumnya = $stmt3->get_result()->fetch_assoc()['total'];
$stmt3->close();

$saldo_awal = $total_pemasukan_sebelumnya - $total_pengeluaran_sebelumnya;

$sql_pemasukan = "
    SELECT COALESCE(SUM(nominal_pembayaran), 0) as total 
    FROM pembayaran 
    WHERE bulan_pembayaran = ? 
      AND CAST(tahun_pembayaran AS UNSIGNED) = ? 
      AND status_pembayaran = 'lunas'
";
$stmt4 = $koneksi->prepare($sql_pemasukan);
if (!$stmt4) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error prepare: ' . $koneksi->error]);
    exit;
}
$stmt4->bind_param("si", $namaBulan, $tahunInt);
$stmt4->execute();
$pemasukan = $stmt4->get_result()->fetch_assoc()['total'];
$stmt4->close();

$sql_detail_pemasukan = "
    SELECT p.tanggal_pembayaran, pg.nama, p.jenis_pembayaran, p.nominal_pembayaran
    FROM pembayaran p
    JOIN warga w ON p.id_warga = w.id_warga
    JOIN pengguna pg ON w.id_pengguna = pg.id_pengguna
    WHERE p.bulan_pembayaran = ? 
      AND CAST(p.tahun_pembayaran AS UNSIGNED) = ? 
      AND p.status_pembayaran = 'lunas'
    ORDER BY p.tanggal_pembayaran ASC
";
$stmt5 = $koneksi->prepare($sql_detail_pemasukan);
if (!$stmt5) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error prepare: ' . $koneksi->error]);
    exit;
}
$stmt5->bind_param("si", $namaBulan, $tahunInt);
$stmt5->execute();
$result_detail_pemasukan = $stmt5->get_result();

$sql_pengeluaran = "
    SELECT COALESCE(SUM(nominal_pengeluaran), 0) as total 
    FROM pengeluaran 
    WHERE MONTH(tanggal_pengeluaran) = ? 
      AND YEAR(tanggal_pengeluaran) = ?
      AND status_persetujuan = 'Disetujui'
";
$stmt6 = $koneksi->prepare($sql_pengeluaran);
if (!$stmt6) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error prepare: ' . $koneksi->error]);
    exit;
}
$stmt6->bind_param("ii", $bulanAngka, $tahunInt);
$stmt6->execute();
$pengeluaran = $stmt6->get_result()->fetch_assoc()['total'];
$stmt6->close();

$sql_detail_pengeluaran = "
    SELECT tanggal_pengeluaran, nama_pengeluaran, jenis_pengeluaran, nominal_pengeluaran
    FROM pengeluaran
    WHERE MONTH(tanggal_pengeluaran) = ? 
      AND YEAR(tanggal_pengeluaran) = ? 
      AND status_persetujuan = 'Disetujui'
    ORDER BY tanggal_pengeluaran ASC
";
$stmt7 = $koneksi->prepare($sql_detail_pengeluaran);
if (!$stmt7) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error prepare: ' . $koneksi->error]);
    exit;
}
$stmt7->bind_param("ii", $bulanAngka, $tahunInt);
$stmt7->execute();
$result_detail_pengeluaran = $stmt7->get_result();

$saldo_akhir = $saldo_awal + $pemasukan - $pengeluaran;

$warning_saldo_negatif = ($saldo_akhir < 0);

$nama_ketua = 'Ketua RT';
$nama_bendahara = 'Bendahara';
$q = mysqli_query($koneksi, "SELECT nama, role FROM pengguna WHERE role IN ('ketua','bendahara') AND status='Aktif'");
if ($q) {
    while ($r = mysqli_fetch_assoc($q)) {
        if ($r['role'] === 'ketua')     $nama_ketua = $r['nama'];
        if ($r['role'] === 'bendahara') $nama_bendahara = $r['nama'];
    }
}

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

if ($warning_saldo_negatif) {
    $pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(255,0,0);
    $pdf->Cell(0,6,'PERINGATAN: Saldo akhir negatif! Pengeluaran melebihi dana yang tersedia.',0,1,'C');
    $pdf->SetTextColor(0);
    $pdf->Ln(3);
}

$pdf->SummaryBox('Saldo Awal','Rp '.number_format($saldo_awal,0,',','.'),[158,158,158]);
$pdf->Ln(1);
$pdf->SummaryBox('Total Pemasukan','Rp '.number_format($pemasukan,0,',','.'),[76,175,80]);
$pdf->Ln(1);
$pdf->SummaryBox('Total Pengeluaran','Rp '.number_format($pengeluaran,0,',','.'),[244,67,54]);
$pdf->Ln(1);
$warna_saldo = $saldo_akhir >= 0 ? [33,150,243] : [244,67,54];
$pdf->SummaryBox('SALDO AKHIR','Rp '.number_format($saldo_akhir,0,',','.'),$warna_saldo);
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

$stmt5->close();
$stmt7->close();

$stmt_save = $koneksi->prepare("
    INSERT INTO laporan 
    (bulan_tahun, tahun, bulan_nama, bulan_angka, nama_file, path_file, generated_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
        nama_file=VALUES(nama_file),
        path_file=VALUES(path_file),
        generated_at=NOW()
");
if ($stmt_save) {
    $stmt_save->bind_param("sissss", $bulan_tahun, $tahunInt, $namaBulan, $bulanAngkaStr, $namaFile, $path);
    $stmt_save->execute();
    $stmt_save->close();
}

ob_end_clean();
echo json_encode([
    'success' => true,
    'message' => "Laporan $bulan_tahun berhasil digenerate!",
    'file' => $namaFile,
    'saldo_awal' => $saldo_awal,
    'saldo_akhir' => $saldo_akhir,
    'warning' => $warning_saldo_negatif ? 'Saldo akhir negatif! Pengeluaran melebihi dana tersedia.' : null
]);
?>