<?php
require_once __DIR__ . '/../auth/middleware/auth.php';
require_once __DIR__ . '/../auth/utils/security.php';

AuthMiddleware::requireGuest();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Captcha</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/captcha.css">
</head>
<body>
    <div class="captcha-container">
        <h2>Verifikasi Captcha</h2>
        <div id="captchaBox" class="captcha-box"></div>
        <button class="refresh-button" onclick="refreshCaptcha()">
            <i class="fa-solid fa-rotate-right"></i> Refresh Captcha
        </button>
        <input type="text" id="captchaInput" class="captcha-input" placeholder="Masukkan Captcha" required>
        <button type="button" onclick="validateCaptcha()">Verifikasi</button>
        <div class="message" style="display:none;"></div>
    </div>

    <script>
        let currentCaptcha = '';
        const action = new URLSearchParams(window.location.search).get('action') || 'login';

        function refreshCaptcha() {
            currentCaptcha = generateCaptchaForPrompt();
            document.getElementById('captchaBox').textContent = currentCaptcha;
            document.getElementById('captchaInput').value = '';
            document.querySelector('.message').style.display = 'none';
        }

        function validateCaptcha() {
            const userInput = document.getElementById('captchaInput').value;
            const messageElement = document.querySelector('.message');
            
            if (userInput.toLowerCase() === currentCaptcha.toLowerCase()) {
                let data;
                let endpoint;

                switch(action) {
                    case 'register':
                        data = JSON.parse(sessionStorage.getItem('registerData'));
                        endpoint = '../auth/handlers/register.php';
                        break;
                    case 'reset':
                        data = JSON.parse(sessionStorage.getItem('resetData'));
                        endpoint = '../auth/handlers/reset.php';
                        break;
                    default: // login
                        data = JSON.parse(sessionStorage.getItem('loginData'));
                        endpoint = '../auth/handlers/login.php';
                        break;
                }

                if (!data) {
                    messageElement.textContent = 'Data tidak valid';
                    messageElement.style.display = 'block';
                    messageElement.style.color = 'red';
                    return;
                }

                // Kirim data ke server
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: Object.entries(data)
                        .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
                        .join('&')
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Hapus data dari session storage
                        sessionStorage.removeItem('loginData');
                        sessionStorage.removeItem('registerData');
                        sessionStorage.removeItem('resetData');

                        // Redirect berdasarkan role atau ke halaman login
                        if (action === 'login' && result.user && result.user.role) {
                            window.location.href = result.user.role === 'admin' 
                                ? 'dashboard_admin.php' 
                                : 'dashboard_user.php';
                        } else {
                            window.location.href = 'login.php';
                        }
                    } else {
                        messageElement.textContent = result.message;
                        messageElement.style.display = 'block';
                        messageElement.style.color = 'red';
                        refreshCaptcha();
                    }
                })
                .catch(error => {
                    messageElement.textContent = 'Terjadi kesalahan saat memproses permintaan';
                    messageElement.style.display = 'block';
                    messageElement.style.color = 'red';
                    refreshCaptcha();
                });
            } else {
                messageElement.textContent = 'Captcha salah! Silakan coba lagi.';
                messageElement.style.display = 'block';
                messageElement.style.color = 'red';
                refreshCaptcha();
            }
        }

        // Generate captcha saat halaman dimuat
        window.onload = refreshCaptcha;
    </script>
    <script src="../assets/js/captcha.js"></script>
</body>
</html>