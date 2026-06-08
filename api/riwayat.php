<?php
session_start();
include '../koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    http_response_code(401);
    exit();
}

$page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 15;
$offset = ($page - 1) * $limit;

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

// Count total
$countQuery = "SELECT COUNT(*) as total FROM tb_monitor $where";
if (!empty($params)) {
    $stmtCount = mysqli_prepare($koneksi, $countQuery);
    mysqli_stmt_bind_param($stmtCount, $types, ...$params);
    mysqli_stmt_execute($stmtCount);
    $countResult = mysqli_stmt_get_result($stmtCount);
    $totalRow = mysqli_fetch_assoc($countResult);
    mysqli_stmt_close($stmtCount);
} else {
    $countResult = mysqli_query($koneksi, $countQuery);
    $totalRow = mysqli_fetch_assoc($countResult);
}
$total = $totalRow['total'];
$totalPages = ceil($total / $limit);

// Fetch data
$dataQuery = "SELECT * FROM tb_monitor $where ORDER BY waktu DESC LIMIT ? OFFSET ?";
$dataParams = $params;
$dataParams[] = $limit;
$dataParams[] = $offset;
$dataTypes = $types . "ii";

$stmtData = mysqli_prepare($koneksi, $dataQuery);
mysqli_stmt_bind_param($stmtData, $dataTypes, ...$dataParams);
mysqli_stmt_execute($stmtData);
$result = mysqli_stmt_get_result($stmtData);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['waktu'] = date('d-m-Y H:i', strtotime($row['waktu']));
    $data[] = $row;
}
mysqli_stmt_close($stmtData);

echo json_encode([
    'success'    => true,
    'data'       => $data,
    'total'      => intval($total),
    'page'       => $page,
    'limit'      => $limit,
    'totalPages' => $totalPages
]);
?>
