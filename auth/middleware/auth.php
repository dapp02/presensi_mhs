<?php
require_once __DIR__ . '/../config/session.php';

class AuthMiddleware {
    public static function requireLogin() {
        Session::start();
        
        if (!Session::isLoggedIn()) {
            header('Location: /pages/login.php');
            exit();
        }
    }

    public static function requireGuest() {
        Session::start();
        
        if (Session::isLoggedIn()) {
            $role = Session::get('role');
            switch($role) {
                case 'admin':
                    header('Location: /pages/dashboard_admin.php');
                    break;
                case 'dosen':
                    header('Location: /pages/dashboard_admin.php');
                    break;
                case 'mahasiswa':
                    header('Location: /pages/dashboard_user.php');
                    break;
            }
            exit();
        }
    }
}