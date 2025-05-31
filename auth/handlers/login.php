<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../functions/login.php';
require_once __DIR__ . '/../config/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = loginUser($username, $password);
    echo json_encode($result);
    exit();
}