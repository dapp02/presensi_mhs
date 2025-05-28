<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

function loginUser($username, $password) {
    $database = new Database();
    $conn = $database->connect();
    Session::start();
    
    try {
        // Prepare statement untuk mencegah SQL injection
        $stmt = $conn->prepare("SELECT id_pengguna, nama_lengkap, username, password, role FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // Verifikasi user dan password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            Session::set('user_id', $user['id_pengguna']);
            Session::set('username', $user['username']);
            Session::set('nama_lengkap', $user['nama_lengkap']);
            Session::set('role', $user['role']);
            Session::set('logged_in', true);
            
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