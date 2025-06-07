<?php
require_once __DIR__ . '/../config/session.php';

// Definisikan path ke file log kustom untuk middleware
$custom_log_file_middleware = __DIR__ . '/../../logs/app_debug.log';

// Fungsi helper untuk logging kustom di middleware
function custom_error_log_middleware($message, $log_file) {
    $timestamp = date("Y-m-d H:i:s");
    error_log("[" . $timestamp . "] AUTH_MIDDLEWARE: " . $message . PHP_EOL, 3, $log_file);
}

class AuthMiddleware {
    public static function requireLogin() {
        global $custom_log_file_middleware;
        custom_error_log_middleware("Memulai AuthMiddleware::requireLogin()", $custom_log_file_middleware);
        Session::start();
        
        if (!Session::isLoggedIn()) {
            custom_error_log_middleware("Pengguna TIDAK login. Mengalihkan ke /pages/login.php", $custom_log_file_middleware);
            header('Location: /pages/login.php');
            exit();
        } else {
            custom_error_log_middleware("Pengguna SUDAH login. Melanjutkan eksekusi.", $custom_log_file_middleware);
        }
    }

    public static function requireGuest() {
        global $custom_log_file_middleware;
        custom_error_log_middleware("Memulai AuthMiddleware::requireGuest()", $custom_log_file_middleware);
        Session::start();
        
        if (Session::isLoggedIn()) {
            $role = Session::get('role');
            custom_error_log_middleware("Pengguna SUDAH login sebagai: " . $role . ". Mengalihkan.", $custom_log_file_middleware);
            switch($role) {
                case 'admin':
                    header('Location: /pages/crud_admin.php');
                    break;
                case 'dosen':
                    header('Location: /pages/dashboard_admin.php');
                    break;
                case 'mahasiswa':
                    header('Location: /pages/dashboard_user.php');
                    break;
            }
            exit();
        } else {
            custom_error_log_middleware("Pengguna TIDAK login. Melanjutkan eksekusi.", $custom_log_file_middleware);
        }
    }
}