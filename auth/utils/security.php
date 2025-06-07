<?php

class Security {
    public static function sanitizeString($string) {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    public static function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public static function generateToken() {
        return bin2hex(random_bytes(32));
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function preventXSS($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::preventXSS($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    public static function validateCSRFToken() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            return false;
        }
        
        return true;
    }

    public static function generateCSRFToken() {
        $token = self::generateToken();
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
}