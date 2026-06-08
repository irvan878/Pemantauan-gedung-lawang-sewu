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

// ==========================================
// GET - Ambil semua data mitigasi
// ==========================================
if ($method == 'GET') {
    $result = mysqli_query($koneksi, "SELECT * FROM tb_mitigasi ORDER BY id ASC");
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit();
}

// ==========================================
// POST - Update data mitigasi
// ==========================================
if ($method == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'update') {
        $id     = intval($_POST['id'] ?? 0);
        $dampak = $_POST['dampak'] ?? '';
        $saran  = $_POST['saran'] ?? '';
        $sumber = $_POST['sumber'] ?? '';

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
            exit();
        }

        $stmt = mysqli_prepare($koneksi, "UPDATE tb_mitigasi SET dampak = ?, saran = ?, sumber = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $dampak, $saran, $sumber, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Data mitigasi berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate: ' . mysqli_error($koneksi)]);
        }
        mysqli_stmt_close($stmt);
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
?>
