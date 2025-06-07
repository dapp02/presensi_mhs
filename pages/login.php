<?php
require_once __DIR__ . '/../auth/functions/auth.php';
require_once __DIR__ . '/../auth/middleware/auth.php';
require_once __DIR__ . '/../auth/utils/security.php';

AuthMiddleware::requireGuest();

$error = '';
$csrf_token = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Presensi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"/>
</head>
<body>
    <div class="login-container">
        <h2>Login Presensi Mahasiswa</h2>
        <form id="loginForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
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
            const form = event.target;
            const formData = new FormData(form);

            // Simpan data login di session storage untuk digunakan di halaman captcha
            const loginData = {
                username: formData.get('username'),
                password: formData.get('password'),
                csrf_token: formData.get('csrf_token')
            };
            sessionStorage.setItem('loginData', JSON.stringify(loginData));

            // Redirect ke halaman captcha
            window.location.href = 'captcha.php';
        }

        document.getElementById('loginForm').addEventListener('submit', handleLogin);
    </script>
</body>
</html>