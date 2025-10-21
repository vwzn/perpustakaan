<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = "user"; // default user biasa

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        $error = "⚠️ Username sudah digunakan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Perpustakaan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #5a80b1;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            background-color: #5a80b1;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            height: 40px;
        }

        .title {
            font-weight: 600;
            font-size: 14px;
        }

        .subtitle {
            font-size: 12px;
            opacity: 0.9;
        }

        nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .badge {
            background-color: red;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 8px;
            margin-left: 4px;
        }

        .btn-login {
            background-color: transparent;
            border: 1px solid white;
            padding: 6px 12px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 40px 20px;
        }

        .register-box {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .register-box h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .register-box form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .register-box input {
            padding: 15px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.9);
            width: 100%;
            transition: all 0.3s ease;
        }

        .register-box input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.5);
            background-color: white;
        }

        .register-box button {
            padding: 15px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .register-box button:hover {
            background-color: #e69500;
        }

        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
            background-color: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #ff6b6b;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            opacity: 0.8;
        }

        .register-footer a {
            color: white;
            text-decoration: underline;
            font-weight: 600;
        }

        .register-footer a:hover {
            text-decoration: none;
        }

        /* Footer */
        footer {
            background-color: #2c3e59;
            color: white;
            padding: 40px 60px 20px 60px;
            font-size: 14px;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
        }

        .footer-left img {
            height: 50px;
            margin-bottom: 10px;
        }

        .footer-left p {
            margin: 5px 0;
            max-width: 250px;
        }

        .footer-center h4,
        .footer-right h4 {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .footer-center ul {
            list-style: none;
            padding: 0;
            margin: 0;
            columns: 2;
        }

        .footer-center li {
            margin-bottom: 8px;
        }

        .footer-center a {
            text-decoration: none;
            color: white;
        }

        .footer-center a:hover {
            text-decoration: underline;
        }

        .footer-right p {
            margin-bottom: 8px;
        }

        .footer-bottom {
            border-top: 1px solid #ffffff33;
            margin-top: 30px;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }

        .scroll-top {
            background-color: #f1c40f;
            color: black;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .register-box {
                padding: 30px 20px;
            }
            
            header {
                padding: 15px 20px;
            }
            
            footer {
                padding: 30px 20px 15px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <div class="register-box">
            <h2> Register Akun</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Daftar</button>
            </form>
            <div class="register-footer">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>

</html>