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
        <form id="registerForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="hidden" name="role" value="mahasiswa">
            
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
                <button type="submit">Daftar</button>
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script>
        function handleRegister(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const captcha = formData.get('captcha');
            const userCaptcha = formData.get('user_captcha');

            if (captcha !== userCaptcha) {
                alert('Captcha tidak sesuai');
                return;
            }

            // Simpan data registrasi di session storage
            const registerData = {
                nama_lengkap: formData.get('nama_lengkap'),
                username: formData.get('username'),
                email: formData.get('email'),
                password: formData.get('password'),
                role: formData.get('role'),
                csrf_token: formData.get('csrf_token')
            };
            sessionStorage.setItem('registerData', JSON.stringify(registerData));

            // Redirect ke halaman captcha untuk verifikasi final
            window.location.href = 'captcha.php?action=register';
        }

        document.getElementById('registerForm').addEventListener('submit', handleRegister);
    </script>
    <script src="../assets/js/captcha.js"></script>
</body>
</html>
