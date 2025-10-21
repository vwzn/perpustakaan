<?php session_start(); ?>
<header>
    <div class="logo">📚 Sistem Informasi Perpustakaan</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="buku.php">Katalog Buku</a>
        <a href="profil.php">Profil</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="logout.php">Logout (<?= $_SESSION['username']; ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
