<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// ambil data lama
$stmt = $conn->prepare("SELECT * FROM buku WHERE id_buku=?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    die("Buku tidak ditemukan");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun = $_POST['tahun'];
    $kategori = $_POST['kategori'];

    // upload cover baru (opsional)
    $cover = $buku['cover'];
    if (!empty($_FILES['cover']['name'])) {
        $cover = time() . "_" . basename($_FILES['cover']['name']);
        move_uploaded_file($_FILES['cover']['tmp_name'], "uploads/" . $cover);
    }

    $stmt = $conn->prepare("UPDATE buku SET judul=?, pengarang=?, tahun=?, kategori=?, cover=? WHERE id_buku=?");
    $stmt->execute([$judul, $pengarang, $tahun, $kategori, $cover, $id]);

    header("Location: buku.php");
    exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku - Perpustakaan Digital</title>
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

        .content-container {
            flex-grow: 1;
            padding: 40px 60px;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #f1c40f;
            box-shadow: 0 0 0 2px rgba(241, 196, 15, 0.2);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px dashed rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #f1c40f;
        }

        .file-upload-label i {
            margin-right: 8px;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .current-cover {
            margin-top: 15px;
            text-align: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .current-cover p {
            margin-bottom: 10px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .current-cover img {
            max-width: 150px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .preview-container {
            margin-top: 15px;
            text-align: center;
            display: none;
        }

        .preview-image {
            max-width: 150px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-primary {
            background: #f1c40f;
            color: #2c3e50;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #e69500;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(241, 196, 15, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
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
            
            .form-actions {
                flex-direction: column;
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
                <div class="subtitle">Edit Buku</div>
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
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Buku</h1>
            <p>Perbarui informasi buku "<strong><?= htmlspecialchars($buku['judul']) ?></strong>" di form berikut.</p>
        </div>

        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="judul">Judul Buku</label>
                    <input type="text" id="judul" name="judul" class="form-control" 
                           value="<?= htmlspecialchars($buku['judul']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="pengarang">Pengarang</label>
                    <input type="text" id="pengarang" name="pengarang" class="form-control" 
                           value="<?= htmlspecialchars($buku['pengarang']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="tahun">Tahun Terbit</label>
                    <input type="number" id="tahun" name="tahun" class="form-control" 
                           value="<?= htmlspecialchars($buku['tahun']) ?>" 
                           min="1900" max="<?= date('Y') ?>" required>
                </div>

                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" class="form-control" required>
                        <option value="Pelajaran" <?= $buku['kategori']=="Pelajaran"?"selected":"" ?>>Pelajaran</option>
                        <option value="Fiksi" <?= $buku['kategori']=="Fiksi"?"selected":"" ?>>Fiksi</option>
                        <option value="Non-Fiksi" <?= $buku['kategori']=="Non-Fiksi"?"selected":"" ?>>Non-Fiksi</option>
                        <option value="Teknologi" <?= $buku['kategori']=="Teknologi"?"selected":"" ?>>Teknologi</option>
                        <option value="Sejarah" <?= $buku['kategori']=="Sejarah"?"selected":"" ?>>Sejarah</option>
                        <option value="Biografi" <?= $buku['kategori']=="Biografi"?"selected":"" ?>>Biografi</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cover Buku</label>
                    
                    <?php if ($buku['cover']): ?>
                    <div class="current-cover">
                        <p>Cover Saat Ini:</p>
                        <img src="uploads/<?= $buku['cover'] ?>" alt="Cover Buku Saat Ini">
                    </div>
                    <?php endif; ?>
                    
                    <div class="file-upload">
                        <label class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="file-name">Pilih file cover baru (opsional)</span>
                        </label>
                        <input type="file" name="cover" id="cover" accept="image/*">
                    </div>
                    <div class="preview-container">
                        <p>Preview Cover Baru:</p>
                        <img id="preview-image" class="preview-image" src="" alt="Preview Cover Baru">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Update Buku
                    </button>
                    <a href="buku.php" class="btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
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
        // Preview image untuk cover buku baru
        document.getElementById('cover').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview-image');
            const previewContainer = document.querySelector('.preview-container');
            const fileName = document.getElementById('file-name');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
                fileName.textContent = file.name;
            } else {
                previewContainer.style.display = 'none';
                fileName.textContent = 'Pilih file cover baru (opsional)';
            }
        });

        // Validasi tahun
        document.getElementById('tahun').addEventListener('input', function() {
            const currentYear = new Date().getFullYear();
            if (this.value > currentYear) {
                this.value = currentYear;
            }
        });

        // Tampilkan pesan konfirmasi sebelum meninggalkan halaman jika ada perubahan
        let formChanged = false;
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', (e) => {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        form.addEventListener('submit', () => {
            formChanged = false;
        });
    </script>
</body>
</html>