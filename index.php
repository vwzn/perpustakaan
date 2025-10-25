<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Perpustakaan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        /* Reset & Font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #5a80b1;
            color: #fff;
        }

        /* Header */
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

        /* Dropdown */
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

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Hero Section */
        main {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 60px;
        }

        .text-content {
            max-width: 50%;
        }

        .text-content h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .subheading {
            font-size: 18px;
            margin-bottom: 30px;
            color: #f2f2f2;
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
            border: none;
            border-radius: 5px;
            width: 200px;
        }

        .search-box button {
            padding: 10px 20px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .hero-image img {
            width: 600px;
        }

        /* Best Seller Section */
        .best-seller {
            width: 100%;
            margin-top: 40px;
        }

        .best-seller h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            color: #f2f2f2;
        }

        .best-seller-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .book-item {
            position: relative;
            width: 200px;
            height: 280px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.4);
        }

        .book-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .book-item:hover img {
            transform: scale(1.05);
        }

        .book-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: white;
            padding: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .book-item:hover .book-overlay {
            opacity: 1;
        }

        .book-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .book-author {
            font-size: 14px;
            opacity: 0.9;
        }

        .book-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #f1c40f;
            color: #000;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
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

<body id="top">
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
            <div class="dropdown">
                <a href="buku.php">Kategori Buku </a>
                <div class="dropdown-content">
                </div>
            </div>
            <a href="profil.php">Profil</a>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Hero Section -->
    <main>
        <section class="hero">
            <div class="text-content">
                <h1>Buku untuk Semua</h1>
                <p class="subheading">Akses di mana pun, kapan pun, Baca buku yuk!</p>
                <form action="search.php" method="GET" class="search-box">
                    <input type="text" name="q" placeholder="Cari buku disini" required>
                    <select name="kurikulum">
                        <option value="">Semua Kategori</option>
                        <option value="1">Pelajaran</option>
                        <option value="2">Novel</option>
                        <option value="3">Komik</option>
                    </select>
                    <button type="submit">Cari</button>
                </form>
            </div>
            <div class="hero-image">
                <img src="gambar/depan.png" alt="Ilustrasi Buku">
            </div>
        </section>

        <!-- Best Seller Section -->
        <section class="best-seller">
            <h2>Buku Terpopuler</h2>
            <div class="best-seller-container">
                <?php
                // Koneksi ke database
                $host = "localhost";
                $username = "root";
                $password = "";
                $database = "perpus";

                $conn = mysqli_connect($host, $username, $password, $database);

                if (!$conn) {
                    die("Koneksi gagal: " . mysqli_connect_error());
                }

                // Query untuk mendapatkan 3 buku terpopuler berdasarkan jumlah peminjaman
                $query = "
                    SELECT b.id, b.judul, b.pengarang, b.gambar, COUNT(p.buku_id) as jumlah_pinjam
                    FROM buku b
                    LEFT JOIN pinjam p ON b.id = p.buku_id
                    GROUP BY b.id
                    ORDER BY jumlah_pinjam DESC
                    LIMIT 3
                ";

                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    $counter = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $book_id   = $row['id'];
                        $judul     = $row['judul'];
                        $pengarang = $row['pengarang'];
                        $gambar    = $row['gambar'];

                        // Tentukan path gambar dengan benar
                        if (empty($gambar)) {
                            $gambar_path = "gambar/default-book.jpg";
                        } else {
                            // jika field menyimpan path lengkap atau URL, pakai apa adanya
                            if (strpos($gambar, "/") !== false || preg_match('#^https?://#i', $gambar)) {
                                $gambar_path = $gambar;
                            } else {
                                // jika hanya nama file, tambahkan folder 'buku/'
                                $gambar_path = "buku/" . $gambar;
                            }
                        }

                        $img = htmlspecialchars($gambar_path, ENT_QUOTES);

                        echo "<a href='buku1.php?id=" . urlencode($book_id) . "' class='book-item'>
                                <div class='book-badge'>#" . $counter . "</div>
                                <img src='" . $img . "' alt='Cover Buku'>
                                <div class='book-overlay'>
                                    <div class='book-title'>" . htmlspecialchars($judul, ENT_QUOTES) . "</div>
                                    <div class='book-author'>" . htmlspecialchars($pengarang, ENT_QUOTES) . "</div>
                                </div>
                              </a>";

                        $counter++;
                    }
                } else {
                    echo "<p>Tidak ada data buku terpopuler.</p>";
                }

                mysqli_close($conn);
                ?>
            </div>
        </section>
    </main>

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