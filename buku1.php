<?php
session_start();
include "koneksi.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Buku tidak ditemukan.");
}

$sql = "SELECT * FROM buku WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$buku = $result->fetch_assoc();

if (!$buku) {
    die("Data buku tidak tersedia.");
}

// Cek session success dan error
$show_success = false;
$show_error = false;

if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    $show_success = true;
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    $show_error = true;
    unset($_SESSION['error']);
}

$id = $_GET['id'];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Buku - <?= htmlspecialchars($buku['judul']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            animation: fadeIn 0.5s;
            position: relative;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .close-alert {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body {
            margin: 0;
            background-color: #f4f6f8;
            font-family: 'Poppins', sans-serif;
            color: #2c3e50;
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
            color:white;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            height: 70px;
        }

        nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            transition: 0.3s;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            padding: 6px 12px;
        }

        nav a.active {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
            border-radius: 5px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 40px 20px;
        }

        .detail-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .detail-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .detail-container img {
            max-width: 250px;
            border-radius: 10px;
        }

        .detail-info {
            flex: 1;
            min-width: 250px;
        }

        h2 {
            margin-top: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            background: orange;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background: #e67e22;
        }

        .sinopsis {
            margin-top: 30px;
            padding: 15px;
            background: #f9f9f9;
            border-left: 5px solid #3498db;
            border-radius: 5px;
        }

        .sinopsis h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        footer {
            background-color: #2c3e59;
            color: white;
            padding: 40px 60px 20px 60px;
            font-size: 14px;
            margin-top: 40px;
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
    </style>
</head>

<body>
    <!-- Alert Success -->
    <?php if ($show_success): ?>
        <div class="alert alert-success">
            ✅ <?= htmlspecialchars($success_message) ?>
            <button class="close-alert" onclick="this.parentElement.style.display='none'">×</button>
        </div>
    <?php endif; ?>

    <!-- Alert Error -->
    <?php if ($show_error): ?>
        <div class="alert alert-error">
            ❌ <?= htmlspecialchars($error_message) ?>
            <button class="close-alert" onclick="this.parentElement.style.display='none'">×</button>
        </div>
    <?php endif; ?>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="gambar/perpus.png" alt="Logo">
            <div>
                <p class="title">Sistem Informasi</p>
                <p class="subtitle">Perpustakaan Digital</p>
            </div>
        </div>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="buku.php" class="active">Katalog Buku</a>
            <a href="profil.php">Profil</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <div class="detail-container">
            <div class="detail-flex">
                <img src="buku/<?= htmlspecialchars($buku['gambar']) ?>"
                    onerror="this.src='buku/default.jpg'"
                    alt="<?= htmlspecialchars($buku['judul']) ?>">
                <div class="detail-info">
                    <h2><?= htmlspecialchars($buku['judul']) ?></h2>
                    <p><strong>Pengarang:</strong> <?= htmlspecialchars($buku['pengarang']) ?></p>
                    <p><strong>Penerbit:</strong> <?= htmlspecialchars($buku['penerbit']) ?></p>
                    <p><strong>Tahun:</strong> <?= htmlspecialchars($buku['tahun']) ?></p>
                    <p><strong>Stok:</strong> <?= htmlspecialchars($buku['quantity']) ?></p>
                    <a href="pinjam.php?id=<?= $buku['id'] ?>" class="btn">📖 Pinjam Buku</a>
                    <a href="buku.php" class="btn" style="background:#3498db;">⬅ Kembali</a>
                </div>
            </div>

            <!-- Sinopsis / Deskripsi -->
            <?php if (!empty($buku['deskripsi'])): ?>
                <div class="sinopsis">
                    <h3>📖 Sinopsis / Deskripsi</h3>
                    <p><?= nl2br(htmlspecialchars($buku['deskripsi'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <!-- Footer -->
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
    <script>
        <?php if (isset($success_message)): ?>
            // Tampilkan alert ketika halaman selesai load
            window.onload = function() {
                alert("✅ <?= addslashes($success_message) ?>");
            };
        <?php endif; ?>
    </script>
</body>

</html>