<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Sekolah - Sistem Perpustakaan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

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
            background-color: rgba(255,255,255,0.2);
            padding: 6px 12px;
            border-radius: 5px;
        }


    /* Dropdown */
    .dropdown { position: relative; }
    .dropdown-content {
      display: none; position: absolute;
      background-color: white; color: black;
      min-width: 180px; border-radius: 5px;
      top: 35px; z-index: 1000;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
    }
    .dropdown-content a {
      display: block; padding: 10px;
      color: black; text-decoration: none; font-size: 14px;
    }
    .dropdown-content a:hover { background-color: #f1f1f1; }
    .dropdown:hover .dropdown-content { display: block; }

    /* Profil Sekolah */
    main {
      display: flex;
      justify-content: center;
      padding: 60px 20px;
    }
    .school-card {
      background-color: white;
      color: #2c3e50;
      padding: 30px;
      border-radius: 10px;
      max-width: 1000px;
      width: 100%;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .school-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .school-header img {
      width: 90px;
      height: auto;
      border-radius: 10px;
      margin-bottom: 10px;
    }
    .school-header h2 {
      margin-bottom: 10px;
      font-size: 22px;
    }

    /* Gambar sekolah */
    .school-photo {
      text-align: center;
      margin: 25px 0;
    }
    .school-photo img {
      width: 100%;
      max-width: 300px; /* ✅ gambar tidak terlalu besar */
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      height: auto;
    }
    .photo-caption {
      font-size: 13px;
      color: #555;
      margin-top: 8px;
      font-style: italic;
    }

    .section {
      margin-bottom: 25px;
    }
    .section h3 {
      margin-bottom: 10px;
      border-left: 5px solid #5a80b1;
      padding-left: 10px;
      font-size: 20px;
    }
    .section p {
      font-size: 15px;
      line-height: 1.6;
      text-align: justify;
    }
    .section ul {
      margin-left: 20px;
      list-style: disc;
    }
    .section ul li {
      margin-bottom: 8px;
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
            </div>
            <a href="profil.php">Profil</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

  <!-- Profil Sekolah -->
  <main>
    <div class="school-card">
      <div class="school-header">
        <img src="gambar/logo.jpeg" alt="Logo Sekolah">
        <h2>SMK PERINTIS KABUPATEN BANDUNG</h2>
        <p>SMK Perintis, Jalan Terusan Katapang Andir KM3 No.23, Sukaluyu, Bojongkunci, Kec. Pameungpeuk, Kabupaten Bandung, Jawa Barat 40921</p>
      </div>

      <!-- Gambar Sekolah -->
      <div class="school-photo">
        <img src="https://lh3.googleusercontent.com/p/AF1QipPZv6yr_NgrUCqIFCPlNmkDP8CVCayGEi2WhoC1=s680-w680-h510-rw" alt="Gedung Sekolah SMK Perintis">
        <p class="photo-caption">Tampak depan gedung SMK Perintis Kabupaten Bandung</p>
      </div>

      <div class="section">
        <h3>Sejarah Singkat</h3>
        <p>
          SMK Perintis berdiri pada tahun 2005 sebagai lembaga pendidikan kejuruan
          yang berkomitmen mencetak generasi muda yang berkarakter, berpengetahuan, 
          dan siap kerja. Sekolah ini terus berkembang dengan berbagai jurusan 
          unggulan sesuai kebutuhan dunia industri dan teknologi modern.
        </p>
      </div>

      <div class="section">
        <h3>Visi</h3>
        <p>
        "Menciptakan tamatan yang berahlak mulia, berjiwa perintis, dan kompeten dalam penguasaan IPTEK serta berwirausaha."
        </p>
        <ul>
          <li>Menciptakan tamatan yang berahlak mulia.</li>
          <li>Menciptakan tamatan berjiwa perintis.</li>
          <li>Menciptakan tamatan yang kompeten dalam penguasaan IPTEK.</li>
          <li>Menciptakan tamatan yang mampu berwirausaha.</li>
        </ul>
      </div>

      <div class="section">
        <h3>Misi</h3>
        <ul>
          <li>Mengembangkan kepribadian peserta didik agar beriman, bertakwa, dan berakhlak mulia.</li>
          <li>Menerapkan sistem pembelajaran berbasis kompetensi dan teknologi.</li>
          <li>Menyiapkan tamatan yang kompeten di bidangnya dan siap kerja.</li>
          <li>Membangun jiwa wirausaha yang handal, produktif, dan mandiri.</li>
          <li>Mengembangkan kemampuan adaptasi terhadap perkembangan IPTEK dan dunia usaha.</li>
        </ul>
      </div>

      <div class="section">
        <h3>Program Keahlian</h3>
        <ul>
          <li>Teknik Kendaraan Ringan (TKR)</li>
          <li>Teknik Komputer dan Jaringan (TKJ)</li>
          <li>Akuntansi dan Keuangan Lembaga (AKL)</li>
        </ul>
      </div>

    </div>
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
