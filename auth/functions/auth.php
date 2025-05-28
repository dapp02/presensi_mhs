<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

class Auth {
    private $db;
    private $table = 'pengguna';
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        Session::start();
    }

    public function getCurrentUser() {
        if (!Session::isLoggedIn()) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([Session::get('user_id')]);
        return $stmt->fetch();
    }

    public function isLoggedIn() {
        return Session::isLoggedIn();
    }

    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /pages/login.php');
            exit();
        }
    }

    public function requireRole($role) {
        $this->requireLogin();
        if (!$this->hasRole($role)) {
            header('HTTP/1.1 403 Forbidden');
            exit('Akses ditolak');
        }
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Username atau password salah'];
        }

        Session::set('user_id', $user['id']);
        Session::set('role', $user['role']);

        return ['success' => true, 'user' => $user];
    }

    public function register($data) {
        if (empty($data['username']) || empty($data['password']) || empty($data['role'])) {
            return ['success' => false, 'message' => 'Semua field harus diisi'];
        }

        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE username = ?");
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username sudah digunakan'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (username, password, role) VALUES (?, ?, ?)");
        $success = $stmt->execute([
            $data['username'],
            $hashedPassword,
            $data['role']
        ]);

        if (!$success) {
            return ['success' => false, 'message' => 'Gagal mendaftarkan user'];
        }

        return ['success' => true, 'message' => 'Registrasi berhasil'];
    }

    public function resetPassword($username, $newPassword) {
        if (empty($username) || empty($newPassword)) {
            return ['success' => false, 'message' => 'Username dan password baru harus diisi'];
        }

        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Username tidak ditemukan'];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ? WHERE id = ?");
        $success = $stmt->execute([$hashedPassword, $user['id']]);

        if (!$success) {
            return ['success' => false, 'message' => 'Gagal mereset password'];
        }

        return ['success' => true, 'message' => 'Password berhasil direset'];
    }

    public function logout() {
        Session::destroy();
        header('Location: /pages/login.php');
        exit();
    }
}