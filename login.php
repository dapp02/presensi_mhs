<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mahasiswa</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <style>
        body {
            background: url('/bg_login.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            height: 300px;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding-left: 600px;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 50%;
            height: 55vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: cente
            
        }
        input[type="text"], input[type="password"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            width: 80%;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .register-link {
            margin-top: 10px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.captch_box {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 180px;
    height: 30px;
    border: 1px solid #ccc;
    padding: 5px;
    border-radius: 5px;
    background: #fff;
    margin: 5px auto;
}

.captch_box input {
    width: 120px;
    height: 50px;
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    border: none;
    outline: none;
    background: transparent;
}

.refresh_button {
    width: 30px;
    height: 30px;
    background: #28a745;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 10px;
}

.refresh_button:hover {
    background: #218838;
}

.captch_input {
    text-align: center;
    margin-top: 10px;
}
        .forgot-password {
            text-align: right;
            margin: 10px 0;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .forgot-password a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login Mahasiswa</h2>
        <form>
            <input type="text" placeholder="Email/Username" required>
            <input type="password" placeholder="Password" required>
            
            <div class="forgot-password">
                <a href="reset.php">Lupa Password?</a>
            </div>
          
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
                <button type="submit" formaction="dashboard_admin.html">Login</button>
                <p>Belum punya akun?<a href="register.php">Daftar</a></p>
            </div>
        </form>
        <script src="captcha.js"></script>
    </div>
</body>
</html>
