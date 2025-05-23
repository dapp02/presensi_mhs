<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Mahasiswa</title>
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
            width: 40%;
            height: 40vh; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        input[type="text"], input[type="password"], input[type="email"] {
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
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Daftar Akun Mahasiswa</h2>
        <form>
            <input type="text" placeholder="Nama Lengkap" required>
            <input type="text" placeholder="Username" required>
            <input type="email" placeholder="Email" required>
            <input type="password" placeholder="Password" required>
            <button type="submit">Daftar</button>
        </form>
        <div class="register-link">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
