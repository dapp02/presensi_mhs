<?php
require_once '../assets/auth/functions/reset.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak sesuai';
    } else {
        $result = resetPassword($username, $email, $new_password);
        
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
    <title>Atur Ulang Kata Sandi</title>
    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
</head>
<body>
    <div class="left-panel">
        <div class="logo-container">
            <img src="../assets/images/knowledge.png" alt="Logo" class="logo">
        </div>
        <div class="owl-container">
            <img src="../assets/images/burung.png" alt="Owl Mascot" class="owl-image">
            <h1 class="login-text">Presensi Mahasiswa</h1>
            <p class="login-subtext">Silahkan login dulu ya!</p>
        </div>
        <div></div>
    </div>
    <div class="right-panel">
        <div class="form-container">
            <h2 class="form-title">Atur Ulang Kata Sandi</h2>
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-input password-input" placeholder="Masukkan username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-input password-input" placeholder="contoh : email@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kata Sandi Baru</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="new_password" class="form-input password-input" placeholder="Masukkan kata sandi baru" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Kata Sandi</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="confirm_password" class="form-input password-input" placeholder="Konfirmasi kata sandi baru" required>
                    </div>
                </div>
                <div class="button-container">
                    <button type="submit" class="submit-button">Reset Password</button>
                </div>
                <div class="login-link-container">
                    <span>Sudah punya akun? </span>
                    <a href="login.php" class="login-link">Klik disini untuk Masuk</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>