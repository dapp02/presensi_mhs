<?php
require_once 'db.php';

function registerUser($nama_lengkap, $username, $email, $password, $role) {
    global $conn;
    
    try {
        // Validasi input
        if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password) || empty($role)) {
            throw new Exception('Semua field harus diisi');
        }
        
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid');
        }
        
        // Validasi role
        $allowed_roles = ['mahasiswa', 'dosen', 'admin'];
        if (!in_array($role, $allowed_roles)) {
            throw new Exception('Role tidak valid');
        }
        
        // Cek username dan email sudah ada atau belum
        $stmt = $conn->prepare("SELECT COUNT(*) FROM pengguna WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('Username atau email sudah terdaftar');
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert ke database
        $stmt = $conn->prepare("INSERT INTO pengguna (nama_lengkap, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nama_lengkap, $username, $email, $hashed_password, $role]);
        
        return ['success' => true, 'message' => 'Registrasi berhasil'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Handle register request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    
    $result = registerUser($nama_lengkap, $username, $email, $password, $role);
    echo json_encode($result);
    exit();
}
?>