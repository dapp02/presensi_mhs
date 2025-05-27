<?php
require_once 'db.php';
session_start();

function loginUser($username, $password) {
    global $conn;
    
    try {
        // Prepare statement untuk mencegah SQL injection
        $stmt = $conn->prepare("SELECT id_pengguna, nama_lengkap, username, password, role FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verifikasi user dan password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id_pengguna'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            // Redirect berdasarkan role
            switch($user['role']) {
                case 'admin':
                    header('Location: /pages/dashboard_admin.php');
                    break;
                case 'dosen':
                case 'mahasiswa':
                    header('Location: /pages/dashboard_user.php');
                    break;
                default:
                    throw new Exception('Role tidak valid');
            }
            exit();
        } else {
            throw new Exception('Username atau password salah');
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
    if (!$username || !$password) {
        echo json_encode(['success' => false, 'message' => 'Username dan password harus diisi']);
        exit();
    }
    
    $result = loginUser($username, $password);
    echo json_encode($result);
    exit();
}
?>