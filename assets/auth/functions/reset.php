<?php
require_once 'db.php';

function resetPassword($username, $email, $new_password) {
    global $conn;
    
    try {
        // Validasi input
        if (empty($username) || empty($email) || empty($new_password)) {
            throw new Exception('Semua field harus diisi');
        }
        
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid');
        }
        
        // Cek username dan email cocok dengan data di database
        $stmt = $conn->prepare("SELECT id FROM pengguna WHERE username = ? AND email = ?");
        $stmt->execute([$username, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('Username atau email tidak ditemukan');
        }
        
        // Hash password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password di database
        $stmt = $conn->prepare("UPDATE pengguna SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        
        return ['success' => true, 'message' => 'Password berhasil direset'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Handle reset password request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    
    $result = resetPassword($username, $email, $new_password);
    echo json_encode($result);
    exit();
}
?>