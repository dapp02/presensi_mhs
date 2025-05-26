<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />

</head>
<body>
    <div class="login-container">
        <h2>Login Mahasiswa</h2>
        <form>
            <input type="text" placeholder="Email/Username" required>
            <input type="password" placeholder="Password" required>
          
        <div class="register-link">
        </div>
     <div class="container">
                <header>Masukkan Captcha terlebih dahulu!</header>
                <div class="input_field captch_box input">
                    <input type="text" value="" disabled />
                    <button class="refresh_button">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            <div class="input_field captch_box input">
                <input type="text"required/>
            </div>
            <div class="message">Masukkan Captcha</div>
            <div class="input_field button disabled">
            </div>
            <button type="submit" formaction="dashboard_admin.php">Login</button>
            <p>Belum punya akun?<a href="register.php">Daftar</a></p>
        </form>
        <script src="captcha.js"></script>
          </div>
     </div>
</div>

</body>
</body>
</html>
