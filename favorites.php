<?php
session_start();
if(!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Belum Tersedia - Perpustakaan Digital</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS dari dashboard dengan beberapa penyesuaian */
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
        padding: 15px 60px;
        background-color: #2c3e59;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
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
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #f1c40f;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
        }

        .user-role {
            background: orange;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .btn-logout {
            background-color: #e74c3c;
            border: none;
            padding: 8px 16px;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-logout:hover {
            background-color: #c0392b;
        }

        /* Konten khusus untuk halaman ini */
        .content-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }

        .error-icon {
            font-size: 100px;
            margin-bottom: 30px;
        }

        .error-title {
            font-size: 36px;
            margin-bottom: 20px;
            color: #f1c40f;
        }

        .error-message {
            font-size: 18px;
            max-width: 600px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-primary {
            background: orange;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #e69500;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Footer */
        footer {
            background-color: #2c3e59;
            color: white;
            padding: 40px 60px 20px 60px;
            font-size: 14px;
            margin-top: auto;
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
            header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .content-container {
                padding: 20px;
            }
            
            .user-info {
                flex-direction: column;
                gap: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 300px;
            }
            
            footer {
                padding: 30px 20px 15px 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="gambar/perpus.png" alt="Logo">
            
            <div>
                <div class="title">Perpustakaan Digital</div>
                <div class="subtitle">Halaman Belum Tersedia</div>
            </div>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="buku.php">Katalog Buku</a>
            <?php if($role == 'admin'): ?>
                <a href="manage-books.php">Kelola Buku</a>
                <a href="users.php">Kelola User</a>
            <?php endif; ?>
            <div class="user-info">
                <span>Halo, <?= htmlspecialchars($username) ?></span>
                <span class="user-role"><?= ucfirst($role) ?></span>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>

    <div class="content-container">
        <div class="error-icon">🚧</div>
        <h1 class="error-title">Maaf, Halaman Ini Belum Tersedia</h1>
        <p class="error-message">
            Halaman yang Anda coba akses sedang dalam tahap pengembangan. 
            Tim developer kami sedang bekerja keras untuk menyelesaikan fitur ini 
            secepat mungkin. Terima kasih atas pengertian Anda.
        </p>
        
        <div class="action-buttons">
            <a href="dashboard.php" class="btn-primary">Kembali ke Dashboard</a>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="gambar/logo.jpeg" alt="Logo">
                <p><strong>SMK PERINTIS</strong></p>
                <p>Badan Standar, Kurikulum, dan Asesmen Pendidikan.<br>Kementerian Pendidikan Dasar dan Menengah.</p>
            </div>

            <div class="footer-center">
                <h4>Peta Situs</h4>
                <ul>
                    <li><a href="https://dapo.kemendikdasmen.go.id/sekolah/03B769B6CF65C9A2C91B">Kemendikdasmen</a></li>
                    <li><a href="https://www.instagram.com/smk.perintis.kab.bandung/">Instagram</a></li>
                    <li><a href="#">Buku Teks K-13</a></li>
                    <li><a href="#">Buku Nonteks</a></li>
                    <li><a href="#">Penilaian</a></li>
                    <li><a href="#">Kebijakan</a></li>
                    <li><a href="#">Pembinaan</a></li>
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="#">Sastra Masuk Kurikulum</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Sistem Informasi Perpustakaan Digital</p>
            <a href="#top" class="scroll-top">↑ Kembali ke Atas</a>
        </div>
    </footer>
</body>
</html>