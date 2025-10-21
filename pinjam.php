<?php
session_start();
include "koneksi.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Buku tidak ditemukan.");
}

// Ambil data buku
$sql_buku = "SELECT * FROM buku WHERE id = ?";
$stmt_buku = $conn->prepare($sql_buku);
$stmt_buku->bind_param("i", $id);
$stmt_buku->execute();
$result_buku = $stmt_buku->get_result();
$buku = $result_buku->fetch_assoc();

if (!$buku) {
    die("Data buku tidak tersedia.");
}

// Cek stok buku
if ($buku['quantity'] <= 0) {
    $_SESSION['error'] = "Maaf, buku sedang tidak tersedia untuk dipinjam.";
    header("Location: buku1.php?id=" . $id);
    exit();
}

// Ambil data user
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Proses form peminjaman
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $keterangan = $_POST['keterangan'] ?? '';
    
    // Validasi tanggal
    if (empty($tanggal_pinjam) || empty($tanggal_kembali)) {
        $error = "Tanggal pinjam dan tanggal kembali harus diisi.";
    } else {
        // Insert data peminjaman
        $sql_insert = "INSERT INTO pinjam ( user_id, buku_id, tanggal_pinjam, tanggal_kembali, keterangan, status) 
                      VALUES (?, ?, ?, ?, ?, 'dipinjam')";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iisss", $user_id, $id, $tanggal_pinjam, $tanggal_kembali, $keterangan);
        
        if ($stmt_insert->execute()) {
            // Update stok buku
            $sql_update_stok = "UPDATE buku SET quantity = quantity - 1 WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update_stok);
            $stmt_update->bind_param("i", $id);
            $stmt_update->execute();
            
            $_SESSION['success'] = "Buku berhasil dipinjam!";
            header("Location: buku1.php?id=" . $id);
            exit();
        } else {
            $error = "Gagal meminjam buku. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pinjam Buku - <?= htmlspecialchars($buku['judul']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background-color: #f4f6f8;
            font-family: 'Poppins', sans-serif;
            color: #2c3e50;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .container {
            max-width: 800px;
            margin: auto;
            padding: 40px 20px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .book-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="date"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: orange;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .btn:hover {
            background: #e67e22;
        }
        .btn-secondary {
            background: #3498db;
        }
        .btn-secondary:hover {
            background: #2980b9;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
    </style>
</head>
<body>
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
        <a href="buku.php">Katalog Buku</a>
        <a href="profil.php">Profil</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <div class="form-container">
        <h2>Form Peminjaman Buku</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="book-info">
            <h3>Informasi Buku</h3>
            <p><strong>Judul:</strong> <?= htmlspecialchars($buku['judul']) ?></p>
            <p><strong>Pengarang:</strong> <?= htmlspecialchars($buku['pengarang']) ?></p>
            <p><strong>Penerbit:</strong> <?= htmlspecialchars($buku['penerbit']) ?></p>
            <p><strong>Stok Tersedia:</strong> <?= htmlspecialchars($buku['quantity']) ?></p>
        </div>
        
        <div class="user-info">
            <h3>Informasi Peminjam</h3>
            <p><strong>Nama:</strong> <?= htmlspecialchars($user['username'] ?? 'User') ?></p>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam *</label>
                <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" 
                       value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="tanggal_kembali">Tanggal Kembali *</label>
                <input type="date" id="tanggal_kembali" name="tanggal_kembali" 
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan (Opsional)</label>
                <textarea id="keterangan" name="keterangan" 
                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">📖 Ajukan Peminjaman</button>
                <a href="buku1.php?id=<?= $id ?>" class="btn btn-secondary">⬅ Kembali</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>