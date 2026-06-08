<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$startDate = $_GET['start_date'] ?? '';
$endDate   = $_GET['end_date'] ?? '';

// Build WHERE clause
$where = "";
$params = [];
$types  = "";

if (!empty($startDate) && !empty($endDate)) {
    $where = "WHERE DATE(waktu) BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    $types .= "ss";
}

// Fetch data
$dataQuery = "SELECT * FROM tb_monitor $where ORDER BY waktu DESC";
if (!empty($params)) {
    $stmt = mysqli_prepare($koneksi, $dataQuery);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, $dataQuery);
}

// ==========================================
// GENERATE EXCEL
// ==========================================
$filename = "Laporan_Sensor_Lawang_Sewu_" . date('d-m-Y_H-i') . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Sensor</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
echo '<body>';
echo '<table border="0" cellpadding="5" cellspacing="0">';

// ==========================================
// JUDUL (atas, center, bold)
// ==========================================
echo '<tr><td colspan="8" align="center" style="font-size:18px; font-weight:bold; padding:10px;">LAPORAN DATA SENSOR MONITORING</td></tr>';
echo '<tr><td colspan="8" align="center" style="font-size:14px; font-weight:bold;">GEDUNG LAWANG SEWU - SEMARANG</td></tr>';
echo '<tr><td colspan="8"></td></tr>';

// Periode
if (!empty($startDate) && !empty($endDate)) {
    $periodeText = "Periode: " . date('d-m-Y', strtotime($startDate)) . " s/d " . date('d-m-Y', strtotime($endDate));
} else {
    $periodeText = "Periode: Semua Data";
}
echo '<tr><td colspan="8" style="font-size:12px;">' . $periodeText . '</td></tr>';
echo '<tr><td colspan="8"></td></tr>';

// ==========================================
// HEADER TABEL
// ==========================================
echo '<tr style="background-color:#1e293b; color:#ffffff; font-weight:bold;">';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">No</td>';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">Waktu</td>';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">Suhu (°C)</td>';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">Kelembapan (%)</td>';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">Getaran (m/s²)</td>';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">Kemiringan (°)</td>';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">Status</td>';
echo '<td style="border:1px solid #334155; padding:8px; text-align:center;">Penyebab</td>';
echo '</tr>';

// ==========================================
// DATA ROWS
// ==========================================
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $bgColor = ($no % 2 == 0) ? '#f8fafc' : '#ffffff';
    $statusColor = '#22c55e';
    if ($row['status'] == 'WASPADA') $statusColor = '#f59e0b';
    if ($row['status'] == 'BAHAYA') $statusColor = '#ef4444';

    echo '<tr style="background-color:' . $bgColor . ';">';
    echo '<td style="border:1px solid #e2e8f0; text-align:center;">' . $no . '</td>';
    echo '<td style="border:1px solid #e2e8f0; text-align:center;">' . date('d-m-Y H:i:s', strtotime($row['waktu'])) . '</td>';
    echo '<td style="border:1px solid #e2e8f0; text-align:center;">' . $row['suhu'] . '</td>';
    echo '<td style="border:1px solid #e2e8f0; text-align:center;">' . $row['kelembapan'] . '</td>';
    echo '<td style="border:1px solid #e2e8f0; text-align:center;">' . $row['getaran'] . '</td>';
    echo '<td style="border:1px solid #e2e8f0; text-align:center;">' . $row['kemiringan'] . '</td>';
    echo '<td style="border:1px solid #e2e8f0; text-align:center; color:' . $statusColor . '; font-weight:bold;">' . $row['status'] . '</td>';
    echo '<td style="border:1px solid #e2e8f0; text-align:center;">' . ($row['penyebab'] == '-' ? 'Normal' : $row['penyebab']) . '</td>';
    echo '</tr>';
    $no++;
}

// ==========================================
// WAKTU CETAK (bawah)
// ==========================================
echo '<tr><td colspan="8"></td></tr>';
echo '<tr><td colspan="8" style="font-size:11px; font-style:italic; color:#64748b;">Waktu Cetak: ' . date('d-m-Y H:i:s') . ' WIB</td></tr>';

echo '</table>';
echo '</body></html>';

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}
?>
