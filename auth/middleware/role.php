<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/auth.php';

class RoleMiddleware {
    public static function requireRole($allowed_roles) {
        AuthMiddleware::requireLogin();
        
        if (!is_array($allowed_roles)) {
            $allowed_roles = [$allowed_roles];
        }
        
        $user_role = Session::get('role');
        if (!in_array($user_role, $allowed_roles)) {
            header('HTTP/1.1 403 Forbidden');
            exit('Akses ditolak');
        }
    }

    public static function requireAdmin() {
        self::requireRole('admin');
    }

    public static function requireDosen() {
        self::requireRole('dosen');
    }

    public static function requireMahasiswa() {
        self::requireRole('mahasiswa');
    }

    public static function requireDosenOrAdmin() {
        self::requireRole(['dosen', 'admin']);
    }
}