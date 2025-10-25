<?php
session_start();
include "koneksi.php";

$search   = isset($_GET['search']) ? $_GET['search'] : "";
$kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;

// Mapping kategori ID ke nama kategori
$kategori_list = [
    1 => "Pelajaran",
    2 => "Novel",
    3 => "Komik",
    4 => "Teknologi",
    5 => "Sejarah"
];

$limit = 30; // jumlah buku per halaman
$page  = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

/* =============================
   QUERY TOTAL DATA (untuk pagination)
   ============================= */
$count_sql    = "SELECT COUNT(*) as total FROM buku WHERE 1=1";
$count_params = [];
$count_types  = "";

if ($search != "") {
    $count_sql .= " AND judul LIKE ?";
    $count_params[] = "%" . $search . "%";
    $count_types   .= "s";
}
if ($kategori > 0) {
    $count_sql .= " AND id_kategori = ?";
    $count_params[] = $kategori;
    $count_types   .= "i";
}

$count_stmt = $conn->prepare($count_sql);
if (!empty($count_params)) {
    $count_stmt->bind_param($count_types, ...$count_params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_buku   = $count_result->fetch_assoc()['total'];

/* =============================
   QUERY DATA BUKU (dengan LIMIT)
   ============================= */
$sql    = "SELECT * FROM buku WHERE 1=1";
$params = [];
$types  = "";

if ($search != "") {
    $sql .= " AND judul LIKE ?";
    $params[] = "%" . $search . "%";
    $types   .= "s";
}
if ($kategori > 0) {
    $sql .= " AND id_kategori = ?";
    $params[] = $kategori;
    $types   .= "i";
}

$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types   .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$kategori_nama = $kategori > 0 ? $kategori_list[$kategori] : "Semua Kategori";
$total_pages   = ceil($total_buku / $limit);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Katalog Buku</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
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
            position: relative;
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

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            color: black;
            min-width: 180px;
            border-radius: 5px;
            top: 35px;
            z-index: 1000;
            box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
        }

        .dropdown-content a {
            display: block;
            padding: 10px;
            color: black;
            text-decoration: none;
            font-size: 14px;
        }

        .dropdown-content a.active {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 40px 20px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box input,
        .search-box select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid black;
            border-radius: 5px;
            width: 200px;
            background-color: white;
            color: #2c3e50;
        }

        .search-box button {
            padding: 10px 20px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            /* 5 kolom */
            gap: 20px;
            margin-top: 20px;
        }

        .book-card {
            width: 100%;
            aspect-ratio: 3 / 4;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .book-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .book-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* PAGINATION */
        .pagination {
            margin-top: 30px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 14px;
            margin: 0 3px;
            background: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .pagination a.active {
            background: orange;
            font-weight: bold;
        }

        .pagination a:hover {
            background: #34495e;
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
            <div class="dropdown">
                <a href="buku.php">Kategori Buku </a>
            </div>
            <a href="profil.php">Profil</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="container">
        <form action="buku.php" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Cari buku disini" value="<?= htmlspecialchars($search) ?>">
            <select name="kategori">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori_list as $id => $nama): ?>
                    <option value="<?= $id ?>" <?= $kategori == $id ? "selected" : "" ?>><?= $nama ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Cari</button>
        </form>

        <h2><?= $kategori_nama ?></h2>
        <p>Menampilkan <strong><?= $total_buku ?></strong> buku</p>

        <div class="book-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="buku1.php?id=<?= $row['id'] ?>" class="book-card" title="<?= htmlspecialchars($row['judul']) ?>">
                    <img src="buku/<?= htmlspecialchars($row['gambar']) ?>"
                        onerror="this.src='buku/default.jpg'"
                        alt="<?= htmlspecialchars($row['judul']) ?>">
                </a>
            <?php endwhile; ?>
        </div>

        <!-- PAGINATION -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?search=<?= urlencode($search) ?>&kategori=<?= $kategori ?>&page=1">« Pertama</a>
                <a href="?search=<?= urlencode($search) ?>&kategori=<?= $kategori ?>&page=<?= $page - 1 ?>">‹ Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?search=<?= urlencode($search) ?>&kategori=<?= $kategori ?>&page=<?= $i ?>"
                    class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?search=<?= urlencode($search) ?>&kategori=<?= $kategori ?>&page=<?= $page + 1 ?>">Next ›</a>
                <a href="?search=<?= urlencode($search) ?>&kategori=<?= $kategori ?>&page=<?= $total_pages ?>">Terakhir »</a>
            <?php endif; ?>
        </div>
    </div>

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
</body>

</html>