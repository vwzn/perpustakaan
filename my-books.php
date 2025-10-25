<?php
session_start();
if(!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Koneksi database
require_once 'koneksi.php';

// Fungsi untuk mendapatkan buku yang sedang dipinjam
function getBukuDipinjam($user_id, $conn) {
    $query = "SELECT p.*, b.judul, b.pengarang, b.gambar 
              FROM pinjam p 
              JOIN buku b ON p.buku_id = b.id 
              WHERE p.user_id = ? 
              AND p.status = 'dipinjam'
              ORDER BY p.tanggal_pinjam DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $buku_dipinjam = [];
    while($row = mysqli_fetch_assoc($result)) {
        $buku_dipinjam[] = $row;
    }
    return $buku_dipinjam;
}

// Fungsi untuk mendapatkan riwayat peminjaman
function getRiwayatPeminjaman($user_id, $conn) {
    $query = "SELECT p.*, b.judul, b.pengarang, b.gambar 
              FROM pinjam p 
              JOIN buku b ON p.buku_id = b.id 
              WHERE p.user_id = ? 
              AND p.status = 'dikembalikan'
              ORDER BY p.tanggal_kembali DESC";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $riwayat = [];
    while($row = mysqli_fetch_assoc($result)) {
        $riwayat[] = $row;
    }
    return $riwayat;
}

// Ambil data
$buku_dipinjam = getBukuDipinjam($user_id, $conn);
$riwayat_peminjaman = getRiwayatPeminjaman($user_id, $conn);

// Debug: Cek apakah data berhasil diambil
// var_dump($buku_dipinjam);
// var_dump($riwayat_peminjaman);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Saya - Perpustakaan Digital</title>
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

        .main-container {
            flex-grow: 1;
            padding: 40px 60px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 12px;
            width: fit-content;
        }

        .tab {
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .tab.active {
            background: orange;
            color: white;
        }

        .tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .section-title {
            font-size: 22px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .book-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .book-cover {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #2c3e59;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
        }

        .book-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .book-author {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 10px;
        }

        .book-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            opacity: 0.7;
            margin-bottom: 15px;
        }

        .book-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            text-decoration: none;
        }

        .btn-primary {
            background: orange;
            color: white;
        }

        .btn-primary:hover {
            background: #e69500;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .empty-state img {
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .empty-state p {
            opacity: 0.8;
            margin-bottom: 20px;
        }

        .history-table {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .history-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .history-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .history-table tr:last-child td {
            border-bottom: none;
        }

        .history-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .book-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .book-info img {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-dipinjam {
            background: #f39c12;
            color: white;
        }

        .status-dikembalikan {
            background: #27ae60;
            color: white;
        }

        .status-terlambat {
            background: #e74c3c;
            color: white;
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
            
            .main-container {
                padding: 20px;
            }
            
            .user-info {
                flex-direction: column;
                gap: 10px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .tabs {
                width: 100%;
                justify-content: center;
            }
            
            .books-grid {
                grid-template-columns: 1fr;
            }
            
            .history-table {
                overflow-x: auto;
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
                <div class="subtitle">Buku Saya</div>
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

    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">📖 Buku Saya</h1>
        </div>

        <div class="tabs">
            <div class="tab active" onclick="switchTab('sedang-dipinjam')">📚 Sedang Dipinjam</div>
            <div class="tab" onclick="switchTab('riwayat')">📋 Riwayat Peminjaman</div>
        </div>

        <!-- Tab Sedang Dipinjam -->
        <div id="sedang-dipinjam" class="tab-content active">
            <h2 class="section-title">📚 Buku yang Sedang Dipinjam</h2>
            
            <?php if(!empty($buku_dipinjam)): ?>
                <div class="books-grid">
                    <?php foreach($buku_dipinjam as $buku): ?>
                        <div class="book-card">
                            <div class="book-cover">
                                <?php if(!empty($buku['gambar'])): ?>
                                    <img src="buku/<?= $buku['gambar'] ?>" alt="<?= htmlspecialchars($buku['judul']) ?>">
                                <?php else: ?>
                                    <span>📚 No Cover</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="book-title"><?= htmlspecialchars($buku['judul']) ?></h3>
                            <p class="book-author">Oleh: <?= htmlspecialchars($buku['pengarang']) ?></p>
                            <div class="book-meta">
                                
                                <span class="status-badge status-dipinjam">Dipinjam</span>
                            </div>
                            <div class="book-meta">
                                <span>Dipinjam: <?= date('d M Y', strtotime($buku['tanggal_pinjam'])) ?></span>
                                <span>Kembali: <?= date('d M Y', strtotime($buku['tanggal_kembali'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div>📚</div>
                    <h3>Tidak ada buku yang sedang dipinjam</h3>
                    <p>Anda belum meminjam buku apapun. Yuk jelajahi katalog buku kami!</p>
                    <a href="buku.php" class="btn btn-primary">Jelajahi Katalog</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab Riwayat -->
        <div id="riwayat" class="tab-content">
            <h2 class="section-title">📋 Riwayat Peminjaman</h2>
            
            <?php if(!empty($riwayat_peminjaman)): ?>
                <div class="history-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($riwayat_peminjaman as $riwayat): ?>
                                <tr>
                                    <td>
                                        <div class="book-info">
                                            <?php if(!empty($riwayat['gambar'])): ?>
                                                <img src="buku/<?= $riwayat['gambar'] ?>" alt="<?= htmlspecialchars($riwayat['judul']) ?>">
                                            <?php else: ?>
                                                <div style="width: 50px; height: 70px; background: #2c3e59; border-radius: 5px; display: flex; align-items: center; justify-content: center;">📚</div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($riwayat['judul']) ?></strong><br>
                                                <small>Oleh: <?= htmlspecialchars($riwayat['pengarang']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('d M Y', strtotime($riwayat['tanggal_pinjam'])) ?></td>
                                    <td><?= date('d M Y', strtotime($riwayat['tanggal_kembali'])) ?></td>
                                    <td>
                                        <span class="status-badge status-dikembalikan">Dikembalikan</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" onclick="pinjamLagi(<?= $riwayat['buku_id'] ?>)">Pinjam Lagi</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div>📋</div>
                    <h3>Belum ada riwayat peminjaman</h3>
                    <p>Riwayat peminjaman buku akan muncul di sini setelah Anda mengembalikan buku.</p>
                    <a href="buku.php" class="btn btn-primary">Jelajahi Katalog</a>
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

    <script>
        function switchTab(tabName) {
            // Sembunyikan semua tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Tampilkan tab yang dipilih
            document.getElementById(tabName).classList.add('active');
            
            // Update tab aktif
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        function bacaBuku(bukuId) {
            alert('Membuka buku dengan ID: ' + bukuId);
            // Redirect ke halaman baca buku
            // window.location.href = 'baca.php?id=' + bukuId;
        }

        function perpanjangBuku(pinjamId) {
            if(confirm('Apakah Anda ingin memperpanjang masa pinjam buku ini?')) {
                alert('Memperpanjang buku dengan ID pinjam: ' + pinjamId);
                // AJAX request untuk memperpanjang
                // window.location.href = 'perpanjang.php?id=' + pinjamId;
            }
        }

        function kembalikanBuku(pinjamId) {
            if(confirm('Apakah Anda yakin ingin mengembalikan buku ini?')) {
                alert('Mengembalikan buku dengan ID pinjam: ' + pinjamId);
                // AJAX request untuk pengembalian
                // window.location.href = 'kembalikan.php?id=' + pinjamId;
            }
        }

        function pinjamLagi(bukuId) {
            if(confirm('Apakah Anda ingin meminjam buku ini lagi?')) {
                alert('Meminjam buku dengan ID: ' + bukuId);
                // AJAX request untuk meminjam lagi
                // window.location.href = 'pinjam.php?id=' + bukuId;
            }
        }
    </script>
</body>
</html>