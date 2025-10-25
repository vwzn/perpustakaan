<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id']; // Pastikan user_id disimpan di session saat login

// Koneksi database
require_once 'koneksi.php'; // Sesuaikan dengan file koneksi Anda

// Fungsi untuk mendapatkan statistik
function getStatistics($role, $user_id, $conn)
{
    $stats = [];

    if ($role == 'admin') {
        // Total Buku
        $query = "SELECT COUNT(*) as total FROM buku";
        $result = mysqli_query($conn, $query);
        $stats['total_buku'] = mysqli_fetch_assoc($result)['total'];

        // Total Pengguna
        $query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
        $result = mysqli_query($conn, $query);
        $stats['total_users'] = mysqli_fetch_assoc($result)['total'];

        // Peminjaman Aktif
        $query = "SELECT COUNT(*) as total FROM pinjam WHERE status = 'dipinjam'";
        $result = mysqli_query($conn, $query);
        $stats['peminjaman_aktif'] = mysqli_fetch_assoc($result)['total'];

        // Rating Sistem (rata-rata)
        // $query = "SELECT AVG(rating) as rata_rata FROM reviews";
        // $result = mysqli_query($conn, $query);
        // $rating = mysqli_fetch_assoc($result)['rata_rata'];
        // $stats['rating_sistem'] = $rating ? round($rating, 1) : 0;

    } else {
        // Buku Dipinjam User
        $query = "SELECT COUNT(*) as total FROM pinjam WHERE user_id = '$user_id' AND status = 'dipinjam'";
        $result = mysqli_query($conn, $query);
        $stats['buku_dipinjam'] = mysqli_fetch_assoc($result)['total'];

        // Buku Favorit User
        // $query = "SELECT COUNT(*) as total FROM favorites WHERE user_id = '$user_id'";
        // $result = mysqli_query($conn, $query);
        // $stats['buku_favorit'] = mysqli_fetch_assoc($result)['total'];

        // Buku Harus Dikembalikan (yang mendekati tanggal kembali)
        $today = date('Y-m-d');
        $query = "SELECT COUNT(*) as total FROM pinjam 
                 WHERE user_id = '$user_id' 
                 AND status = 'dipinjam' 
                 AND tanggal_kembali <= DATE_ADD('$today', INTERVAL 3 DAY)";
        $result = mysqli_query($conn, $query);
        $stats['harus_dikembalikan'] = mysqli_fetch_assoc($result)['total'];

        // Total Buku Dibaca
        $query = "SELECT COUNT(*) as total FROM pinjam 
                 WHERE user_id = '$user_id' 
                 AND status = 'dikembalikan'";
        $result = mysqli_query($conn, $query);
        $stats['total_dibaca'] = mysqli_fetch_assoc($result)['total'];
    }

    return $stats;
}

// Ambil statistik
$statistics = getStatistics($role, $user_id, $conn);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Perpustakaan Digital</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS Anda tetap sama */
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
            background-color: #2c3e59;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            /* Ubah dari 'sticky' biasa */
            top: 0;
            /* Penting: beri nilai top 0 */
            z-index: 1000;
            /* Pastikan header di atas konten lain */
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

        .dashboard-container {
            flex-grow: 1;
            padding: 40px 60px;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-section h1 {
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .welcome-section p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.admin {
            border-left: 4px solid #e74c3c;
        }

        .stat-card.user {
            border-left: 4px solid #2ecc71;
        }

        .stat-icon {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
        }

        .action-card h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .action-card p {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 15px;
        }

        .btn-action {
            background: orange;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-action:hover {
            background: #e69500;
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

            .dashboard-container {
                padding: 20px;
            }

            .user-info {
                flex-direction: column;
                gap: 10px;
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
                <div class="subtitle">Dashboard</div>
            </div>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="buku.php">Katalog Buku</a>
            <?php if ($role == 'admin'): ?>
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

    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>👋 Selamat Datang, <?= htmlspecialchars($username) ?>!</h1>
            <p>Anda login sebagai <strong><?= ucfirst($role) ?></strong>. <?= $role == 'admin' ? 'Anda memiliki akses penuh untuk mengelola sistem.' : 'Nikmati akses ke koleksi buku digital kami.' ?></p>
        </div>

        <div class="stats-grid">
            <?php if ($role == 'admin'): ?>
                <div class="stat-card admin">
                    <div class="stat-icon">📚</div>
                    <div class="stat-number"><?= $statistics['total_buku'] ?></div>
                    <div class="stat-label">Total Buku</div>
                </div>
                <div class="stat-card admin">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?= $statistics['total_users'] ?></div>
                    <div class="stat-label">Pengguna Terdaftar</div>
                </div>
                <div class="stat-card admin">
                    <div class="stat-icon">📖</div>
                    <div class="stat-number"><?= $statistics['peminjaman_aktif'] ?></div>
                    <div class="stat-label">Peminjaman Aktif</div>
                </div>
                <div class="stat-card admin">
                    <div class="stat-icon">⭐</div>
                    <!-- <div class="stat-number"><?= $statistics['rating_sistem'] ?></div> -->
                    <div class="stat-label">Rating Sistem</div>
                </div>
            <?php else: ?>
                <div class="stat-card user">
                    <div class="stat-icon">📚</div>
                    <div class="stat-number"><?= $statistics['buku_dipinjam'] ?></div>
                    <div class="stat-label">Buku Dipinjam</div>
                </div>
                <div class="stat-card user">
                    <div class="stat-icon">❤️</div>
                    <!-- <div class="stat-number"><?= $statistics['buku_favorit'] ?></div> -->
                    <div class="stat-label">Buku Favorit</div>
                </div>
                <div class="stat-card user">
                    <div class="stat-icon">⏰</div>
                    <div class="stat-number"><?= $statistics['harus_dikembalikan'] ?></div>
                    <div class="stat-label">Harus Dikembalikan</div>
                </div>
                <div class="stat-card user">
                    <div class="stat-icon">📖</div>
                    <div class="stat-number"><?= $statistics['total_dibaca'] ?></div>
                    <div class="stat-label">Total Dibaca</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="actions-grid">
            <?php if ($role == 'admin'): ?>
                <div class="action-card" onclick="location.href='buku.php'">
                    <h3>📋 Kelola Buku</h3>
                    <p>Tambah, edit, atau hapus buku dari katalog perpustakaan</p>
                    <button class="btn-action">Kelola</button>
                </div>
                <div class="action-card" onclick="location.href='users.php'">
                    <h3>👥 Kelola User</h3>
                    <p>Kelola akun pengguna dan atur hak akses</p>
                    <button class="btn-action">Kelola</button>
                </div>
                <div class="action-card" onclick="location.href='reports.php'">
                    <h3>📊 Laporan</h3>
                    <p>Lihat laporan peminjaman dan statistik penggunaan</p>
                    <button class="btn-action">Lihat</button>
                </div>
                <div class="action-card" onclick="location.href='settings.php'">
                    <h3>⚙️ Pengaturan</h3>
                    <p>Atur konfigurasi sistem dan preferensi</p>
                    <button class="btn-action">Atur</button>
                </div>
            <?php else: ?>
                <div class="action-card" onclick="location.href='buku.php'">
                    <h3>📚 Jelajahi Katalog</h3>
                    <p>Temukan buku-buku menarik dari koleksi kami</p>
                    <button class="btn-action">Jelajahi</button>
                </div>
                <div class="action-card" onclick="location.href='my-books.php'">
                    <h3>📖 Buku Saya</h3>
                    <p>Lihat buku yang sedang Anda pinjam dan riwayat</p>
                    <button class="btn-action">Lihat</button>
                </div>
                <div class="action-card" onclick="location.href='favorites.php'">
                    <h3>❤️ Favorit</h3>
                    <p>Buku-buku yang telah Anda tandai sebagai favorit</p>
                    <button class="btn-action">Lihat</button>
                </div>
                <div class="action-card" onclick="location.href='profile.php'">
                    <h3>👤 Profil Saya</h3>
                    <p>Kelola informasi akun dan preferensi membaca</p>
                    <button class="btn-action">Kelola</button>
                </div>
            <?php endif; ?>
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