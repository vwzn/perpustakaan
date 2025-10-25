<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = "user"; // default user biasa

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        header("Location: login.php?registered=true");
        exit();
    } else {
        $error = "⚠️ Username sudah digunakan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Perpustakaan Digital</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff10" points="0,0 0,1000 1000,1000"/></svg>');
            background-size: cover;
            z-index: 0;
        }

        .floating-books {
            position: absolute;
            font-size: 24px;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .floating-books:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; }
        .floating-books:nth-child(2) { top: 20%; right: 10%; animation-delay: 1s; }
        .floating-books:nth-child(3) { bottom: 30%; left: 15%; animation-delay: 2s; }
        .floating-books:nth-child(4) { bottom: 15%; right: 5%; animation-delay: 3s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .register-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
        }

        .register-left {
            flex: 1;
            background: linear-gradient(135deg, rgba(240, 147, 251, 0.9), rgba(245, 87, 108, 0.9));
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .register-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L0,100 L100,100 Z" fill="%23ffffff10"/></svg>');
            background-size: cover;
        }

        .register-left h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .register-left p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .benefits {
            list-style: none;
            position: relative;
            z-index: 1;
        }

        .benefits li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .benefits i {
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-right {
            flex: 1;
            padding: 50px;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
        }

        .register-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #f5576c;
            box-shadow: 0 0 0 3px rgba(245, 87, 108, 0.2);
            outline: none;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .strength-bar {
            flex: 1;
            height: 5px;
            background: #eee;
            border-radius: 5px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 5px;
        }

        .strength-weak .strength-fill {
            width: 33%;
            background: #e74c3c;
        }

        .strength-medium .strength-fill {
            width: 66%;
            background: #f39c12;
        }

        .strength-strong .strength-fill {
            width: 100%;
            background: #27ae60;
        }

        .terms {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.9rem;
            color: #666;
            margin: 10px 0;
        }

        .terms input {
            margin-top: 3px;
        }

        .terms a {
            color: #f5576c;
            text-decoration: none;
        }

        .terms a:hover {
            text-decoration: underline;
        }

        .register-btn {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 15px rgba(245, 87, 108, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .login-link a {
            color: #f5576c;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(255, 107, 107, 0.1);
            color: #e74c3c;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #e74c3c;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }
            
            .register-left, .register-right {
                padding: 30px;
            }
            
            .floating-books {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="floating-books">📚</div>
    <div class="floating-books">📖</div>
    <div class="floating-books">🔖</div>
    <div class="floating-books">📕</div>

    <div class="register-container">
        <div class="register-left">
            <h1>Bergabunglah Dengan Kami!</h1>
            <p>Daftar sekarang untuk mengakses semua fitur perpustakaan digital kami.</p>
            <ul class="benefits">
                <li><i class="fas fa-book-open"></i> Akses ke ribuan buku digital</li>
                <li><i class="fas fa-clock"></i> Pinjam buku 24/7</li>
                <li><i class="fas fa-heart"></i> Simpan buku favorit</li>
                <li><i class="fas fa-history"></i> Riwayat baca terpantau</li>
            </ul>
        </div>
        
        <div class="register-right">
            <div class="register-header">
                <h2>Buat Akun Baru</h2>
                <p>Isi informasi di bawah untuk mendaftar</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="register-form">
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <div class="password-strength">
                        <span>Kekuatan password:</span>
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm-password" placeholder="Konfirmasi Password" required>
                </div>
                
                <div class="terms">
                    <input type="checkbox" id="agree-terms" required>
                    <label for="agree-terms">Saya setuju dengan <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a></label>
                </div>
                
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>
            
            <div class="login-link">
                <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
            </div>
        </div>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.querySelector('.password-strength');
        const strengthFill = document.querySelector('.strength-fill');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength += 1;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
            if (password.match(/\d/)) strength += 1;
            if (password.match(/[^a-zA-Z\d]/)) strength += 1;
            
            // Reset classes
            strengthBar.className = 'password-strength';
            
            if (password.length > 0) {
                if (strength <= 1) {
                    strengthBar.classList.add('strength-weak');
                } else if (strength <= 2) {
                    strengthBar.classList.add('strength-medium');
                } else {
                    strengthBar.classList.add('strength-strong');
                }
            }
        });
        
        // Password confirmation check
        const confirmPasswordInput = document.getElementById('confirm-password');
        
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#e74c3c';
                this.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.2)';
            } else {
                this.style.borderColor = '#27ae60';
                this.style.boxShadow = '0 0 0 3px rgba(39, 174, 96, 0.2)';
            }
        });
    </script>
</body>
</html>