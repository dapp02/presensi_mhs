<?php

class Validation {
    public static function validateEmail($email) {
        if (empty($email)) {
            return ['valid' => false, 'message' => 'Email harus diisi'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Format email tidak valid'];
        }
        
        return ['valid' => true];
    }

    public static function validatePassword($password, $min_length = 8) {
        if (empty($password)) {
            return ['valid' => false, 'message' => 'Password harus diisi'];
        }
        
        if (strlen($password) < $min_length) {
            return ['valid' => false, 'message' => "Password minimal {$min_length} karakter"];
        }
        
        return ['valid' => true];
    }

    public static function validateUsername($username, $min_length = 4) {
        if (empty($username)) {
            return ['valid' => false, 'message' => 'Username harus diisi'];
        }
        
        if (strlen($username) < $min_length) {
            return ['valid' => false, 'message' => "Username minimal {$min_length} karakter"];
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return ['valid' => false, 'message' => 'Username hanya boleh mengandung huruf, angka, dan underscore'];
        }
        
        return ['valid' => true];
    }

    public static function validateRole($role) {
        $allowed_roles = ['mahasiswa', 'dosen', 'admin'];
        
        if (empty($role)) {
            return ['valid' => false, 'message' => 'Role harus diisi'];
        }
        
        if (!in_array($role, $allowed_roles)) {
            return ['valid' => false, 'message' => 'Role tidak valid'];
        }
        
        return ['valid' => true];
    }
}