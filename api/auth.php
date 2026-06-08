<?php
session_start();
include '../koneksi.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ==========================================
// LOGIN
// ==========================================
if ($action == 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username dan password wajib diisi']);
        exit();
    }

    $stmt = mysqli_prepare($koneksi, "SELECT id, username, password FROM tb_user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && $password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(['success' => true, 'message' => 'Login berhasil']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
    }
    exit();
}

// ==========================================
// LOGOUT
// ==========================================
if ($action == 'logout') {
    session_destroy();
    // Jika request dari browser (GET), redirect ke login
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        header('Location: ../login.php');
        exit();
    }
    echo json_encode(['success' => true, 'message' => 'Logout berhasil']);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
?>
