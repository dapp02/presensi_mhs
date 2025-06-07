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
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
</head>
<body>
    <div class="login-container">
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
            <select name="role" required>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
            </select>
                <button type="submit">Daftar</button>
                <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </form>
    </div>


    <script>
            // Fungsi untuk menangani pendaftaran
            function handleRegister(event) {
                event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            // Remove captcha related lines as captcha is handled in captcha.php
            // const captcha = formData.get('captcha');
            // const userCaptcha = formData.get('user_captcha');

            // if (captcha !== userCaptcha) {
            //     alert('Captcha tidak sesuai');
            //     return;
            // }

                 // Simpan data registrasi di sessionStorage
                 const registerData = {};
                 for (let [key, value] of formData.entries()) {
                     registerData[key] = value;
                 }
                 sessionStorage.setItem('registerData', JSON.stringify(registerData));

                 // Redirect ke halaman captcha untuk verifikasi
                 window.location.href = 'captcha.php?action=register';
        }

        document.getElementById('registerForm').addEventListener('submit', handleRegister);
    </script>
</body>
</html>
