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
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"/>
</head>
<body>
    <div class="login-container">
        <h2>Atur Ulang Kata Sandi</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form id="resetForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="new_password" placeholder="Kata Sandi Baru" required>
            <input type="password" name="confirm_password" placeholder="Konfirmasi Kata Sandi" required>
            <button type="submit">Reset Password</button>
            <p>Sudah ingat password? <a href="login.php">Masuk</a></p>
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