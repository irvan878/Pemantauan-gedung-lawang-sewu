<?php
session_start();
include '../koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    http_response_code(401);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? '';

// ==========================================
// GET - List notifikasi (pagination)
// ==========================================
if ($method == 'GET' && $action == 'list') {
    $page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(50, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;

    // Count total
    $countResult = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_notifikasi");
    $total = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($total / $limit);

    // Fetch data
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM tb_notifikasi ORDER BY waktu DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['waktu'] = date('d-m-Y H:i', strtotime($row['waktu']));
        $data[] = $row;
    }
    mysqli_stmt_close($stmt);

    echo json_encode([
        'success'    => true,
        'data'       => $data,
        'total'      => intval($total),
        'page'       => $page,
        'limit'      => $limit,
        'totalPages' => $totalPages
    ]);
    exit();
}

// ==========================================
// GET - Ambil settings
// ==========================================
if ($method == 'GET' && $action == 'settings') {
    $result = mysqli_query($koneksi, "SELECT nomor_wa, token FROM tb_fonnte LIMIT 1");
    $fonnte = mysqli_fetch_assoc($result);
    echo json_encode([
        'success'      => true,
        'token_fonnte' => $fonnte['token'] ?? '',
        'nomor_wa'     => $fonnte['nomor_wa'] ?? ''
    ]);
    exit();
}

// ==========================================
// POST - Delete single
// ==========================================
if ($method == 'POST' && $action == 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit();
    }

    $stmt = mysqli_prepare($koneksi, "DELETE FROM tb_notifikasi WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Notifikasi berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus']);
    }
    mysqli_stmt_close($stmt);
    exit();
}

// ==========================================
// POST - Delete all
// ==========================================
if ($method == 'POST' && $action == 'delete_all') {
    if (mysqli_query($koneksi, "DELETE FROM tb_notifikasi")) {
        echo json_encode(['success' => true, 'message' => 'Semua notifikasi berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus']);
    }
    exit();
}

// ==========================================
// POST - Save settings
// ==========================================
if ($method == 'POST' && $action == 'save_settings') {
    $token  = $_POST['token_fonnte'] ?? '';
    $nomor  = $_POST['nomor_wa'] ?? '';

    if (empty($token) || empty($nomor)) {
        echo json_encode(['success' => false, 'message' => 'Token dan Nomor WA wajib diisi']);
        exit();
    }

    // Replace table content
    mysqli_query($koneksi, "DELETE FROM tb_fonnte");
    $stmt = mysqli_prepare($koneksi, "INSERT INTO tb_fonnte (nomor_wa, token) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $nomor, $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode(['success' => true, 'message' => 'Pengaturan berhasil disimpan']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
?>
