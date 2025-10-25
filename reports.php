<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Koneksi ke database
$host = 'localhost';
$username_db = 'root';
$password = '';
$database = 'perpus';

$conn = new mysqli($host, $username_db, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses pengembalian buku
if (isset($_POST['kembalikan_buku'])) {
    $peminjaman_id = $_POST['peminjaman_id'];
    $tanggal_dikembalikan = date('Y-m-d');
    
    // Update status peminjaman
    $sql_update = "UPDATE pinjam SET status = 'dikembalikan', tanggal_dikembalikan = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("si", $tanggal_dikembalikan, $peminjaman_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Buku berhasil dikembalikan!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal mengembalikan buku: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Query untuk data peminjaman dengan JOIN ke tabel user dan buku
$sql_peminjaman = "SELECT p.id, u.username, b.judul, p.tanggal_pinjam, p.tanggal_kembali, p.keterangan, p.status 
                  FROM pinjam p 
                  JOIN users u ON p.user_id = u.id 
                  JOIN buku b ON p.buku_id = b.id 
                  ORDER BY p.tanggal_pinjam DESC";

$result_peminjaman = $conn->query($sql_peminjaman);

// Query untuk statistik
$sql_total_peminjaman = "SELECT COUNT(*) as total FROM pinjam";
$sql_dipinjam = "SELECT COUNT(*) as total FROM pinjam WHERE status = 'dipinjam'";
$sql_dikembalikan = "SELECT COUNT(*) as total FROM pinjam WHERE status = 'dikembalikan'";

$total_peminjaman = $conn->query($sql_total_peminjaman)->fetch_assoc()['total'];
$total_dipinjam = $conn->query($sql_dipinjam)->fetch_assoc()['total'];
$total_dikembalikan = $conn->query($sql_dikembalikan)->fetch_assoc()['total'];

// Hitung keterlambatan
$sql_terlambat = "SELECT COUNT(*) as total FROM pinjam 
                 WHERE status = 'dipinjam' AND tanggal_kembali < CURDATE()";
$total_terlambat = $conn->query($sql_terlambat)->fetch_assoc()['total'];

// Ambil data pengembalian untuk laporan
$sql_pengembalian = "SELECT p.id, u.username, b.judul, p.tanggal_pinjam, p.tanggal_kembali, 
                     p.keterangan, p.status, p.tanggal_dikembalikan
                     FROM pinjam p 
                     JOIN users u ON p.user_id = u.id 
                     JOIN buku b ON p.buku_id = b.id 
                     WHERE p.status = 'dikembalikan'
                     ORDER BY p.tanggal_dikembalikan DESC";

$result_pengembalian = $conn->query($sql_pengembalian);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perpustakaan - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .main-content {
            flex-grow: 1;
            padding: 40px 60px;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-description {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.total {
            border-left: 4px solid #3498db;
        }

        .stat-card.dikembalikan {
            border-left: 4px solid #2ecc71;
        }

        .stat-card.dipinjam {
            border-left: 4px solid #f39c12;
        }

        .stat-card.terlambat {
            border-left: 4px solid #e74c3c;
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

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.3), rgba(41, 128, 185, 0.3));
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .table {
            color: #fff;
            margin-bottom: 0;
        }

        .table th {
            background-color: rgba(44, 62, 80, 0.5);
            border-bottom: 2px solid rgba(52, 152, 219, 0.5);
            font-weight: 600;
            padding: 15px;
        }

        .table td {
            border-color: rgba(255, 255, 255, 0.1);
            vertical-align: middle;
            padding: 15px;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-dipinjam {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }

        .badge-dikembalikan {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }

        .badge-terlambat {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .badge-tepat-waktu {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-kembalikan {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }

        .btn-kembalikan:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }

        .btn-kembalikan:disabled {
            background: #7f8c8d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-print {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(155, 89, 182, 0.4);
        }

        .section-title {
            border-left: 4px solid #f1c40f;
            padding-left: 15px;
            margin: 30px 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(46, 204, 113, 0.2), rgba(39, 174, 96, 0.2));
            border: 1px solid rgba(46, 204, 113, 0.3);
            color: #2ecc71;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.2), rgba(192, 57, 43, 0.2));
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
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
            
            .main-content {
                padding: 20px;
            }
            
            .user-info {
                flex-direction: column;
                gap: 10px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                font-size: 14px;
            }
            
            footer {
                padding: 30px 20px 15px 20px;
            }
        }

        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }
            
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                background: white !important;
            }
            
            .btn-print, .filter-section, .btn-kembalikan {
                display: none !important;
            }
            
            .table {
                color: black !important;
            }
        }

        /* Animation for stats */
        @keyframes countUp {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .stat-number {
            animation: countUp 0.8s ease-out;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(241, 196, 15, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(241, 196, 15, 0.7);
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="gambar/perpus.png" alt="Logo">
            <div>
                <div class="title">Perpustakaan Digital</div>
                <div class="subtitle">Laporan & Analytics</div>
            </div>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="buku.php">Katalog Buku</a>
            <a href="manage-books.php">Kelola Buku</a>
            <a href="users.php">Kelola User</a>
            <a href="reports.php" style="color: #f1c40f;">Laporan</a>
            <div class="user-info">
                <span>Halo, <?= htmlspecialchars($username) ?></span>
                <span class="user-role"><?= ucfirst($role) ?></span>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-chart-line"></i>Dashboard Laporan Perpustakaan
            </h1>
            <p class="page-description">
                Analisis komprehensif data peminjaman dan pengembalian buku. Pantau aktivitas perpustakaan secara real-time.
            </p>
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i>Cetak Laporan
            </button>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?= $total_peminjaman ?></div>
                <div class="stat-label">Total Peminjaman</div>
            </div>
            <div class="stat-card dikembalikan">
                <div class="stat-icon">✅</div>
                <div class="stat-number"><?= $total_dikembalikan ?></div>
                <div class="stat-label">Buku Dikembalikan</div>
            </div>
            <div class="stat-card dipinjam">
                <div class="stat-icon">📖</div>
                <div class="stat-number"><?= $total_dipinjam ?></div>
                <div class="stat-label">Sedang Dipinjam</div>
            </div>
            <div class="stat-card terlambat">
                <div class="stat-icon">⏰</div>
                <div class="stat-number"><?= $total_terlambat ?></div>
                <div class="stat-label">Keterlambatan</div>
            </div>
        </div>

        <!-- Peminjaman Aktif Section -->
        <h3 class="section-title">
            <i class="fas fa-clipboard-list"></i>Data Peminjaman Aktif
        </h3>
        
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-sync-alt me-2"></i>Peminjaman Berlangsung</h3>
                <span class="badge badge-dipinjam"><?= $total_dipinjam ?> Aktif</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID</th>
                                <th>Pengguna</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_peminjaman->num_rows > 0) {
                                $no = 1;
                                $active_loans = 0;
                                while($row = $result_peminjaman->fetch_assoc()) {
                                    if ($row['status'] == 'dipinjam') {
                                        $active_loans++;
                                        $status_class = 'badge-dipinjam';
                                        $status_text = 'Dipinjam';
                                        
                                        // Cek apakah terlambat
                                        $today = date('Y-m-d');
                                        $terlambat = ($row['tanggal_kembali'] < $today) ? true : false;
                                        
                                        if ($terlambat) {
                                            $status_class = 'badge-terlambat';
                                            $status_text = 'Terlambat';
                                        }
                                        
                                        echo "<tr>";
                                        echo "<td>{$no}</td>";
                                        echo "<td><strong>PJ" . str_pad($row['id'], 3, '0', STR_PAD_LEFT) . "</strong></td>";
                                        echo "<td>{$row['username']}</td>";
                                        echo "<td>{$row['judul']}</td>";
                                        echo "<td>{$row['tanggal_pinjam']}</td>";
                                        echo "<td>" . ($terlambat ? '<span style="color: #e74c3c">' . $row['tanggal_kembali'] . '</span>' : $row['tanggal_kembali']) . "</td>";
                                        echo "<td><span class='badge {$status_class}'>{$status_text}</span></td>";
                                        echo "<td>";
                                        echo "<form method='POST' style='display:inline;'>";
                                        echo "<input type='hidden' name='peminjaman_id' value='{$row['id']}'>";
                                        echo "<button type='submit' name='kembalikan_buku' class='btn-action btn-kembalikan'>";
                                        echo "<i class='fas fa-undo me-1'></i>Kembalikan";
                                        echo "</button>";
                                        echo "</form>";
                                        echo "</td>";
                                        echo "</tr>";
                                        $no++;
                                    }
                                }
                                if ($active_loans == 0) {
                                    echo "<tr><td colspan='8' class='text-center py-4'>Tidak ada peminjaman aktif</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-4'>Tidak ada data peminjaman</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Pengembalian Section -->
        <h3 class="section-title">
            <i class="fas fa-history"></i>Riwayat Pengembalian Buku
        </h3>
        
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-file-alt me-2"></i>Laporan Pengembalian</h3>
                <span class="badge badge-dikembalikan"><?= $total_dikembalikan ?> Data</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID</th>
                                <th>Pengguna</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Dikembalikan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_pengembalian->num_rows > 0) {
                                $no = 1;
                                while($row = $result_pengembalian->fetch_assoc()) {
                                    $tanggal_dikembalikan = isset($row['tanggal_dikembalikan']) ? $row['tanggal_dikembalikan'] : '-';
                                    
                                    // Tentukan status pengembalian
                                    if ($tanggal_dikembalikan != '-') {
                                        $status_pengembalian = ($tanggal_dikembalikan <= $row['tanggal_kembali']) ? 'Tepat Waktu' : 'Terlambat';
                                        $status_class = ($status_pengembalian == 'Tepat Waktu') ? 'badge-tepat-waktu' : 'badge-terlambat';
                                    } else {
                                        $status_pengembalian = 'Belum Dikembalikan';
                                        $status_class = 'badge-dipinjam';
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td><strong>PJ" . str_pad($row['id'], 3, '0', STR_PAD_LEFT) . "</strong></td>";
                                    echo "<td>{$row['username']}</td>";
                                    echo "<td>{$row['judul']}</td>";
                                    echo "<td>{$row['tanggal_pinjam']}</td>";
                                    echo "<td>{$row['tanggal_kembali']}</td>";
                                    echo "<td>{$tanggal_dikembalikan}</td>";
                                    echo "<td><span class='badge {$status_class}'>{$status_pengembalian}</span></td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-4'>Belum ada data pengembalian</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation for numbers
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach((number, index) => {
                setTimeout(() => {
                    number.style.animation = 'countUp 0.8s ease-out';
                }, index * 200);
            });
        });

        // Confirm before returning book
        document.addEventListener('DOMContentLoaded', function() {
            const returnForms = document.querySelectorAll('form[method="POST"]');
            returnForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Apakah Anda yakin ingin menandai buku ini sebagai dikembalikan?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>