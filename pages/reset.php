<?php
require_once __DIR__ . '/../auth/functions/auth.php';
require_once __DIR__ . '/../auth/middleware/auth.php';
require_once __DIR__ . '/../auth/utils/security.php';
require_once __DIR__ . '/../auth/utils/validation.php';

AuthMiddleware::requireGuest();

$error = '';
$success = '';
$csrf_token = Security::generateCSRFToken();
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
    <div class="form-container">
        <h2 class="form-title">Atur Ulang Kata Sandi</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form id="resetForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
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

    <script>
        function handleReset(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            if (formData.get('new_password') !== formData.get('confirm_password')) {
                alert('Konfirmasi password tidak sesuai');
                return;
            }

            // Simpan data reset password di session storage
            const resetData = {
                username: formData.get('username'),
                email: formData.get('email'),
                new_password: formData.get('new_password'),
                csrf_token: formData.get('csrf_token')
            };
            sessionStorage.setItem('resetData', JSON.stringify(resetData));

            // Redirect ke halaman captcha untuk verifikasi
            window.location.href = 'captcha.php?action=reset';
        }

        document.getElementById('resetForm').addEventListener('submit', handleReset);
    </script>
</body>
</html>