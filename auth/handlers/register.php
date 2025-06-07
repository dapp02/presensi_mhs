<?php
require_once __DIR__ . '/../functions/register.php';

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