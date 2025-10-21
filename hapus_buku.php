<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// hapus dari database
$stmt = $conn->prepare("DELETE FROM buku WHERE id_buku=?");
$stmt->execute([$id]);

header("Location: buku.php");
exit;
