<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';
$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Handle actions
if(isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    switch($action) {
        case 'delete':
            if($id) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['message'] = "User berhasil dihapus";
                $_SESSION['message_type'] = "success";
            }
            break;
            
        case 'toggle_role':
            if($id) {
                // Get current role
                $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch();
                
                if($user) {
                    $new_role = ($user['role'] == 'admin') ? 'user' : 'admin';
                    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                    $stmt->execute([$new_role, $id]);
                    $_SESSION['message'] = "Role user berhasil diubah menjadi " . $new_role;
                    $_SESSION['message_type'] = "success";
                }
            }
            break;
    }
    
    header("Location: users.php");
    exit;
}

// Get user data for editing if edit_id is set
$edit_user = null;
if(isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_user = $stmt->fetch();
}

// Get all users
$stmt = $pdo->query("SELECT id, username, password, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Perpustakaan Digital</title>
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

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 30px 60px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            color: #f1c40f;
        }

        .btn-add {
            background: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-add:hover {
            background: #219a52;
        }

        /* Table Styles */
        .table-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .users-table th {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .users-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Role Badges */
        .role-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .role-admin {
            background: #e74c3c;
            color: white;
        }

        .role-user {
            background: #3498db;
            color: white;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #f39c12;
            color: white;
        }

        .btn-edit:hover {
            background: #d68910;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .btn-role {
            background: #9b59b6;
            color: white;
        }

        .btn-role:hover {
            background: #8e44ad;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #2c3e59;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 20px;
            color: #f1c40f;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #f1c40f;
        }

        .password-note {
            font-size: 12px;
            color: #f1c40f;
            margin-top: 5px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-cancel {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background: #7f8c8d;
        }

        .btn-submit {
            background: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #219a52;
        }

        /* Message Styles */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 600;
        }

        .message.success {
            background: #27ae60;
            color: white;
        }

        .message.error {
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
            
            .main-content {
                padding: 20px;
            }
            
            .user-info {
                flex-direction: column;
                gap: 10px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .users-table {
                font-size: 14px;
            }
            
            .action-buttons {
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
                <div class="subtitle">Kelola Pengguna</div>
            </div>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="buku.php">Katalog Buku</a>
            <a href="manage-books.php">Kelola Buku</a>
            <a href="users.php" style="color: #f1c40f;">Kelola User</a>
            <div class="user-info">
                <span>Halo, <?= htmlspecialchars($username) ?></span>
                <span class="user-role"><?= ucfirst($role) ?></span>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </nav>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Kelola Data Pengguna</h1>
            <button class="btn-add" onclick="openModal('add')">
                <span>+</span> Tambah User Baru
            </button>
        </div>

        <?php if(isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($users)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Tidak ada data user</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <span class="role-badge role-<?= $user['role'] ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="users.php?edit_id=<?= $user['id'] ?>" 
                                       class="btn-action btn-edit">
                                        Edit
                                    </a>
                                    <a href="users.php?action=toggle_role&id=<?= $user['id'] ?>" 
                                       class="btn-action btn-role"
                                       onclick="return confirm('Yakin ingin mengubah role user ini?')">
                                        Ubah Role
                                    </a>
                                    <a href="users.php?action=delete&id=<?= $user['id'] ?>" 
                                       class="btn-action btn-delete"
                                       onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal untuk Tambah User -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah User Baru</h3>
                <span class="close" onclick="closeModal('add')">&times;</span>
            </div>
            <form action="add_user.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('add')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk Edit User -->
    <div id="editUserModal" class="modal" <?= $edit_user ? 'style="display: block;"' : '' ?>>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit User</h3>
                <span class="close" onclick="closeModal('edit')">&times;</span>
            </div>
            <form action="update_user.php" method="POST">
                <input type="hidden" name="id" value="<?= $edit_user ? $edit_user['id'] : '' ?>">
                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" id="edit_username" name="username" 
                           value="<?= $edit_user ? htmlspecialchars($edit_user['username']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="edit_password">Password</label>
                    <input type="password" id="edit_password" name="password">
                    <div class="password-note">Kosongkan jika tidak ingin mengubah password</div>
                </div>
                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <select id="edit_role" name="role" required>
                        <option value="user" <?= $edit_user && $edit_user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $edit_user && $edit_user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('edit')">Batal</button>
                    <button type="submit" class="btn-submit">Update</button>
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
        // Modal functions
        function openModal(type) {
            document.getElementById(type + 'UserModal').style.display = 'block';
        }

        function closeModal(type) {
            document.getElementById(type + 'UserModal').style.display = 'none';
            // Redirect to users.php without edit_id parameter when closing edit modal
            if(type === 'edit') {
                window.location.href = 'users.php';
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addUserModal');
            const editModal = document.getElementById('editUserModal');
            
            if (event.target == addModal) {
                closeModal('add');
            }
            if (event.target == editModal) {
                closeModal('edit');
            }
        }

        // Auto open edit modal if edit_id is set
        <?php if($edit_user): ?>
        document.addEventListener('DOMContentLoaded', function() {
            openModal('edit');
        });
        <?php endif; ?>
    </script>
</body>
</html>