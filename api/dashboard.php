<?php
session_start();
include '../koneksi.php';

header('Content-Type: application/json');

// Session check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    http_response_code(401);
    exit();
}

$action = $_GET['action'] ?? '';

// ==========================================
// DATA SENSOR TERBARU
// ==========================================
if ($action == 'latest') {
    $result = mysqli_query($koneksi, "SELECT * FROM tb_monitor ORDER BY waktu DESC LIMIT 1");
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $data['waktu'] = date('d-m-Y H:i:s', strtotime($data['waktu']));
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => true, 'data' => [
            'suhu' => 0, 'kelembapan' => 0, 'getaran' => 0,
            'kemiringan' => 0, 'status' => 'AMAN', 'penyebab' => '-', 'waktu' => '-'
        ]]);
    }
    exit();
}

// ==========================================
// DATA CHART
// ==========================================
if ($action == 'chart') {
    $range = $_GET['range'] ?? '12h';

    switch ($range) {
        case '1h':  $interval = '1 HOUR'; break;
        case '24h': $interval = '24 HOUR'; break;
        default:    $interval = '12 HOUR'; break;
    }

    // Temukan waktu terbaru untuk acuan (sehingga data demo masa lalu tetap tampil)
    $qMax = mysqli_query($koneksi, "SELECT MAX(waktu) as max_waktu FROM tb_monitor");
    $maxRow = mysqli_fetch_assoc($qMax);
    $maxTime = $maxRow['max_waktu'] ?? date('Y-m-d H:i:s');

    $query = "SELECT suhu, kelembapan, getaran, kemiringan, waktu 
              FROM tb_monitor 
              WHERE waktu >= DATE_SUB('$maxTime', INTERVAL $interval) 
              ORDER BY waktu ASC";
    $result = mysqli_query($koneksi, $query);

    $labels = [];
    $suhu = [];
    $kelembapan = [];
    $getaran = [];
    $kemiringan = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $labels[]      = date('Y-m-d\TH:i:s', strtotime($row['waktu']));
        $suhu[]        = floatval($row['suhu']);
        $kelembapan[]  = floatval($row['kelembapan']);
        $getaran[]     = floatval($row['getaran']);
        $kemiringan[]  = floatval($row['kemiringan']);
    }

    echo json_encode([
        'success'     => true,
        'labels'      => $labels,
        'suhu'        => $suhu,
        'kelembapan'  => $kelembapan,
        'getaran'     => $getaran,
        'kemiringan'  => $kemiringan
    ]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
?>
