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

    <script src="../assets/js/captcha.js"></script>
     <script>
         let currentCaptcha = ''; // Deklarasi global untuk CAPTCHA saat ini
         const action = new URLSearchParams(window.location.search).get('action') || 'login';

         function refreshCaptcha() {
             try {
                 currentCaptcha = generateCaptchaForPrompt();
                 if (typeof currentCaptcha === 'string' && currentCaptcha.length > 0) {
                     document.getElementById('captchaBox').textContent = currentCaptcha;
                 } else {
                     document.getElementById('captchaBox').textContent = 'Error!'; // Indikasi error
                     console.error('generateCaptchaForPrompt() tidak menghasilkan string yang valid.');
                 }
             } catch (e) {
                 console.error('Error saat memanggil generateCaptchaForPrompt():', e);
                 document.getElementById('captchaBox').textContent = 'Error!';
             }
             document.getElementById('captchaInput').value = '';
             document.querySelector('.message').style.display = 'none';
         }

         function validateCaptcha() {
             const userInput = document.getElementById('captchaInput').value;
             const messageElement = document.querySelector('.message');

             // Validasi CAPTCHA sisi klien
             if (userInput.toLowerCase() !== currentCaptcha.toLowerCase()) {
                 messageElement.textContent = 'Captcha salah! Coba lagi.';
                 messageElement.style.display = 'block';
                 messageElement.style.color = 'red';
                 refreshCaptcha();
                 return; // Hentikan jika CAPTCHA klien salah
             }

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

             if (!data) { // Pastikan data sesi ada
                 messageElement.textContent = 'Data sesi tidak ditemukan. Silakan kembali ke halaman login.';
                 messageElement.style.display = 'block';
                 messageElement.style.color = 'red';
                 setTimeout(() => { window.location.href = 'login.php'; }, 3000);
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
             .then(response => {
                 if (!response.ok) { // Tangani error HTTP (selain 2xx)
                     console.error('DEBUG: Network response error:', response.status, response.statusText);
                     // Coba dapatkan detail error jika ada
                     return response.text().then(text => {
                         throw new Error(text || `Server error: ${response.status}`);
                     });
                 }
                 return response.json(); // Lanjutkan parse JSON jika response.ok
             })
             .then(result => {
                 console.log('DEBUG: Server JSON Response Diterima:', result); // Log JSON mentah

                 // Validasi struktur dasar 'result'
                 if (result && typeof result.success === 'boolean') {
                     if (result.success) {
                         // Bersihkan data dari sessionStorage setelah sukses
                         sessionStorage.removeItem('loginData');
                         if (action === 'register') sessionStorage.removeItem('registerData');
                         if (action === 'reset') sessionStorage.removeItem('resetData');

                         if (action === 'login') {
                             if (result.user && result.user.role) {
                                 console.log('DEBUG: Login SUKSES. Peran Pengguna:', result.user.role);
                                 if (result.user.role === 'dosen' || result.user.role === 'admin') {
                                     console.log('DEBUG: Mengarahkan ke dashboard_admin.php untuk peran:', result.user.role);
                                     window.location.href = 'dashboard_admin.php';
                                 } else if (result.user.role === 'mahasiswa') {
                                     console.log('DEBUG: Mengarahkan ke dashboard_user.php');
                                     window.location.href = 'dashboard_user.php';
                                 } else { // Peran tidak dikenal dari server
                                     messageElement.textContent = 'Error: Peran pengguna (' + result.user.role + ') tidak dikenal.';
                                     console.error('DEBUG: Peran tidak dikenal diterima dari server:', result.user.role);
                                     setTimeout(() => { window.location.href = 'login.php'; }, 4000);
                                 }
                             } else { // Login sukses tapi data user.role tidak ada
                                 messageElement.textContent = 'Error: Login berhasil, namun data peran tidak diterima dari server.';
                                 console.error('DEBUG: Login sukses, tapi result.user atau result.user.role tidak ada:', result);
                                 setTimeout(() => { window.location.href = 'login.php'; }, 4000);
                             }
                         } else if (action === 'register' || action === 'reset') { // Sukses untuk register/reset
                             console.log('DEBUG: Aksi', action, 'berhasil.');
                             window.location.href = 'login.php?status=' + action + '_success'; // Redirect ke login dengan notifikasi
                         } else if (action === 'login' && !(result.user && result.user.role)) {
                             messageElement.textContent = 'Error: Login berhasil tapi data peran tidak lengkap.';
                             console.error('DEBUG: Login sukses tapi data user/role tidak lengkap dari server:', result);
                             setTimeout(() => { window.location.href = 'login.php'; }, 3000);
                         } else { // Fallback jika action tidak dikenal namun sukses
                             console.warn('DEBUG: Aksi sukses tidak dikenal, redirect ke login. Aksi:', action);
                             window.location.href = 'login.php';
                         }
                     } else { // result.success adalah false (kegagalan dari server)
                         messageElement.textContent = result.message || 'Operasi gagal. Silakan coba lagi.';
                         console.warn('DEBUG: Operasi GAGAL menurut server:', result.message);
                         refreshCaptcha(); // Refresh CAPTCHA pada kegagalan
                     }
                 } else { // Struktur JSON dari server tidak seperti yang diharapkan
                     messageElement.textContent = 'Format respons dari server tidak valid.';
                     console.error('DEBUG: Struktur JSON dari server tidak valid:', result);
                     refreshCaptcha();
                 }
             })
             .catch(error => { // Menangani error jaringan atau error dari !response.ok atau error parsing JSON
                 console.error('DEBUG: Error pada Fetch atau Parsing JSON:', error);
                 messageElement.textContent = 'Terjadi kesalahan komunikasi: ' + error.message + '. Periksa konsol.';
                 refreshCaptcha(); // Refresh CAPTCHA pada error
             });
         }

         // Pastikan ini dieksekusi setelah DOM dan fungsi generateCaptchaForPrompt() tersedia
         window.onload = function() {
             if (typeof generateCaptchaForPrompt === 'function') {
                 refreshCaptcha();
             } else {
                 console.error('Fungsi generateCaptchaForPrompt tidak ditemukan. Pastikan assets/js/captcha.js dimuat dengan benar sebelum skrip ini.');
                 document.getElementById('captchaBox').textContent = 'Error Init!';
             }
         };
     </script>
</body>
</html>