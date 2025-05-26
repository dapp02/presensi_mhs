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
            <img src="../assets/images/knowledge.png " alt="Logo" class="logo">
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
            <form>
                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-input password-input" placeholder="contoh : email@example.com">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Masukkan Kode Verifikasi</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-key"></i></span>
                        <input type="text" class="form-input password-input" placeholder="Kode Verifikasi">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kata Sandi</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-input password-input" placeholder="password">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi kata sandi</label>
                    <div class="password-input-container">
                        <span class="password-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-input password-input" placeholder="password">
                    </div>
                </div>
                <div class="capslock-warning">
                    <span>B P q W e</span>
                </div>
                <div class="button-container">
                    <button type="submit" class="submit-button">Simpan</button>
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