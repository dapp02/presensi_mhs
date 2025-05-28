<?php
session_start();

// Terima data login dari POST request
$email = $_POST['email'];
$password = $_POST['password'];

// Lakukan validasi login (sesuaikan dengan logika autentikasi yang ada)
// Contoh sederhana:
if ($email === 'raufahafid@gmail.com' && $password === '123') {
    $_SESSION['user'] = $email;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Email atau password salah']);
}
?>