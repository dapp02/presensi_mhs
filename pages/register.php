<?php
require_once '../assets/auth/functions/auth.php';
session_start();

$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = 'mahasiswa'; // Default role untuk registrasi
    $captcha = $_POST['captcha'] ?? '';
    $userCaptcha = $_POST['user_captcha'] ?? '';

    if ($captcha !== $userCaptcha) {
        $error = 'Captcha tidak sesuai';
    } else {
        $result = $auth->register([
            'username' => $username,
            'password' => $password,
            'role' => $role
        ]);

        if ($result['success']) {
            $success = $result['message'];
            header('Location: login.php');
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
    <title>Registrasi Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
</head>
<body>
    <div class="register-container">
        <h2>Registrasi Mahasiswa</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            
            <div class="container">
                <header>Masukkan Captcha terlebih dahulu!</header>
                <div class="input_field captch_box input">
                    <input type="text" name="captcha" value="" disabled />
                    <button type="button" class="refresh_button">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
                <div class="input_field captch_box input">
                    <input type="text" name="user_captcha" required/>
                </div>
                <div class="message">Masukkan Captcha</div>
                <div class="input_field button disabled"></div>
                <button type="submit">Daftar</button>
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </form>
        <script src="../assets/js/captcha.js"></script>
    </div>
</body>
</html>
