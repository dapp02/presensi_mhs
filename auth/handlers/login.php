<?php
require_once __DIR__ . '/../functions/login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
    if (!$username || !$password) {
        echo json_encode(['success' => false, 'message' => 'Username dan password harus diisi']);
        exit();
    }
    
    $result = loginUser($username, $password);
    echo json_encode($result);
    exit();
}