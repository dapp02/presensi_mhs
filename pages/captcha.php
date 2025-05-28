<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Captcha</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        .captcha-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 40%;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .captcha-box {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            font-size: 24px;
            font-family: 'Courier New', monospace;
            letter-spacing: 5px;
            margin: 20px 0;
            user-select: none;
        }
        .captcha-input {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
        }
        .refresh-button {
    background: white;
    border: none;
    color: #28a745;
    cursor: pointer;
    font-size: 16px;
    margin: 10px 0;
    padding: 5px 10px;
    transition: none;
    }

    .refresh-button:hover {
    background: white; /* Tetap putih saat hover */
    color: blue;
    }

    </style>
</head>
<body>
    <div class="captcha-container">
        <h2>Verifikasi Captcha</h2>
        <div id="captchaBox" class="captcha-box"></div>
        <button class="refresh-button" onclick="refreshCaptcha()">↻ Refresh Captcha</button>
        <input type="text" id="captchaInput" class="captcha-input" placeholder="Masukkan Captcha" required>
        <button type="button" onclick="validateCaptcha()">Verifikasi</button>
        <div class="message" style="display:none;"></div>
    </div>

    <script src="../assets/js/captcha.js"></script>
    <script>
        let currentCaptcha = '';

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
                // Ambil data login dari session storage
                const loginData = JSON.parse(sessionStorage.getItem('loginData'));
                
                // Kirim data login ke server menggunakan fetch
                fetch('process_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(loginData.email)}&password=${encodeURIComponent(loginData.password)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hapus data login dari session storage
                        sessionStorage.removeItem('loginData');
                        // Redirect ke dashboard
                        window.location.href = 'dashboard_admin.php';
                    } else {
                        messageElement.textContent = data.message;
                        messageElement.style.display = 'block';
                        messageElement.style.color = 'red';
                        // Redirect kembali ke halaman login
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    }
                })
                .catch(error => {
                    messageElement.textContent = 'Terjadi kesalahan saat memproses login';
                    messageElement.style.display = 'block';
                    messageElement.style.color = 'red';
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
</body>
</html>