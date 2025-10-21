<?php
session_start();
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 40px 20px;
        }

        .login-box {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            font-weight: 700;
        }

        .login-box form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .login-box input {
            padding: 15px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.9);
            width: 100%;
            transition: all 0.3s ease;
        }

        .login-box input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.5);
            background-color: white;
        }

        .login-box button {
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

        .login-box button:hover {
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

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            opacity: 0.8;
        }

        .login-footer a {
            color: white;
            text-decoration: underline;
        }

  

        @media (max-width: 768px) {
            .login-box {
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

    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Masuk</button>
            </form>
            <div class="login-footer">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>


</body>
</html>