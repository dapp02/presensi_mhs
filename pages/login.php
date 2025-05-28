<?php
require_once '../assets/auth/functions/auth.php';
session_start();

$auth = new Auth();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    $userCaptcha = $_POST['user_captcha'] ?? '';

    if ($captcha !== $userCaptcha) {
        $error = 'Captcha tidak sesuai';
    } else {
        $result = $auth->login($username, $password);

        if ($result['success']) {
            $user = $auth->getCurrentUser();
            if ($user && $user['role'] === 'admin') {
                header('Location: dashboard_admin.php');
            } else {
                header('Location: dashboard_user.php');
            }
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Presensi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
</head>
<body>
    <div class="login-container">
        <h2>Login Presensi Mahasiswa</h2>
        <form id="loginForm">
            <input type="text" placeholder="Email/Username" required>
            <input type="password" placeholder="Password" required>
            <div class="forgot-password">
                <a href="reset.php">Lupa kata sandi?</a>
            </div>
            <button type="submit">Login</button>
            <p>Belum punya akun? <a href="register.php">Daftar</a></p>
        </form>
        <div class="message" style="display:none; color: red; text-align: center; margin-top: 10px;"></div>
    </div>

    <script>
        function handleLogin(event) {
            event.preventDefault();
            const messageElement = document.querySelector(".message");
            const emailInput = document.querySelector('input[type="text"]');
            const passwordInput = document.querySelector('input[type="password"]');

            // Simpan data login di session storage untuk digunakan di halaman captcha
            const loginData = {
                email: emailInput.value,
                password: passwordInput.value
            };
            sessionStorage.setItem('loginData', JSON.stringify(loginData));

            // Redirect ke halaman captcha
            window.location.href = 'captcha.php';
        }

        document.getElementById('loginForm').addEventListener('submit', handleLogin);
</body>
</html>
