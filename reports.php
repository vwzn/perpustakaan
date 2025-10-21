<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #2c3e59;
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            padding: 2rem 0;
            border-bottom: 3px solid #3498db;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .card {
            background-color: #34495e;
            border: none;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border-radius: 10px 10px 0 0 !important;
            padding: 1.2rem;
            font-weight: 600;
            border-bottom: none;
        }
        
        .table {
            color: #ecf0f1;
            margin-bottom: 0;
        }
        
        .table th {
            background-color: #2c3e50;
            border-bottom: 2px solid #3498db;
            font-weight: 600;
        }
        
        .table td {
            border-color: #4a5f7a;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #3a506b;
        }
        
        .badge-dipinjam {
            background-color: #e74c3c;
        }
        
        .badge-dikembalikan {
            background-color: #2ecc71;
        }
        
        .badge-terlambat {
            background-color: #f39c12;
        }
        
        .stats-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #3498db;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #bdc3c7;
        }
        
        .section-title {
            border-left: 4px solid #3498db;
            padding-left: 1rem;
            margin: 2rem 0 1.5rem 0;
        }
        
        .print-btn {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border: none;
            border-radius: 50px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .print-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }
        
        .filter-section {
            background-color: #34495e;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .form-control, .form-select {
            background-color: #2c3e50;
            border: 1px solid #4a5f7a;
            color: #ecf0f1;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #2c3e50;
            border-color: #3498db;
            color: #ecf0f1;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .form-label {
            color: #bdc3c7;
            font-weight: 500;
        }
        
        .btn-kembalikan {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border: none;
            padding: 0.3rem 0.8rem;
            font-size: 0.85rem;
        }
        
        .btn-kembalikan:disabled {
            background: #7f8c8d;
            cursor: not-allowed;
        }
        
        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }
            
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            .print-btn, .filter-section, .btn-kembalikan {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <?php
    // Koneksi ke database
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'perpus';
    
    $conn = new mysqli($host, $username, $password, $database);
    
    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Proses pengembalian buku
    if (isset($_POST['kembalikan_buku'])) {
        $peminjaman_id = $_POST['peminjaman_id'];
        $tanggal_dikembalikan = date('Y-m-d');
        
        // Update status peminjaman
        $sql_update = "UPDATE pinjam SET status = 'dikembalikan', keterangan = 'Buku telah dikembalikan' WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $peminjaman_id);
        
        if ($stmt->execute()) {
            $success_message = "Buku berhasil dikembalikan!";
        } else {
            $error_message = "Gagal mengembalikan buku: " . $conn->error;
        }
        $stmt->close();
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

    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-book-open me-3"></i>Laporan Perpustakaan</h1>
                    <p class="lead mb-0">Rekap Data Peminjaman dan Pengembalian Buku</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-success print-btn" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <!-- Alert Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_peminjaman; ?></div>
                    <div class="stats-label">Total Peminjaman</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_dikembalikan; ?></div>
                    <div class="stats-label">Buku Dikembalikan</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_dipinjam; ?></div>
                    <div class="stats-label">Sedang Dipinjam</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="stats-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stats-number"><?php echo $total_terlambat; ?></div>
                    <div class="stats-label">Keterlambatan</div>
                </div>
            </div>
        </div>

        <!-- Peminjaman Section -->
        <h3 class="section-title"><i class="fas fa-list-alt me-2"></i>Data Peminjaman Buku</h3>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-list me-2"></i>Daftar Peminjaman</span>
                <span class="badge bg-light text-dark"><?php echo $total_peminjaman; ?> Data</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="text-white">No.</th>
                                <th class="text-white">ID Peminjaman</th>
                                <th class="text-white">Username</th>
                                <th class="text-white">Judul Buku</th>
                                <th class="text-white">Tanggal Pinjam</th>
                                <th class="text-white">Tanggal Kembali</th>
                                <th class="text-white">Status</th>
                                <th class="text-white">Keterangan</th>
                                <th class="text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_peminjaman->num_rows > 0) {
                                $no = 1;
                                while($row = $result_peminjaman->fetch_assoc()) {
                                    $status_class = $row['status'] == 'dipinjam' ? 'badge-dipinjam' : 'badge-dikembalikan';
                                    $status_text = $row['status'] == 'dipinjam' ? 'Dipinjam' : 'Dikembalikan';
                                    
                                    // Cek apakah terlambat
                                    $today = date('Y-m-d');
                                    $terlambat = ($row['status'] == 'dipinjam' && $row['tanggal_kembali'] < $today) ? true : false;
                                    
                                    if ($terlambat) {
                                        $status_class = 'badge-terlambat';
                                        $status_text = 'Terlambat';
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td>PJ" . str_pad($row['id'], 3, '0', STR_PAD_LEFT) . "</td>";
                                    echo "<td>{$row['username']}</td>";
                                    echo "<td>{$row['judul']}</td>";
                                    echo "<td>{$row['tanggal_pinjam']}</td>";
                                    echo "<td>{$row['tanggal_kembali']}</td>";
                                    echo "<td><span class='badge {$status_class}'>{$status_text}</span></td>";
                                    echo "<td>{$row['keterangan']}</td>";
                                    echo "<td>";
                                    if ($row['status'] == 'dipinjam') {
                                        echo "<form method='POST' style='display:inline;'>";
                                        echo "<input type='hidden' name='peminjaman_id' value='{$row['id']}'>";
                                        echo "<button type='submit' name='kembalikan_buku' class='btn btn-kembalikan btn-sm'>";
                                        echo "<i class='fas fa-undo me-1'></i>Kembalikan";
                                        echo "</button>";
                                        echo "</form>";
                                    } else {
                                        echo "<button class='btn btn-secondary btn-sm' disabled>";
                                        echo "<i class='fas fa-check me-1'></i>Sudah Dikembalikan";
                                        echo "</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>Tidak ada data peminjaman</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Laporan Pengembalian Section -->
        <h3 class="section-title"><i class="fas fa-chart-bar me-2"></i>Laporan Pengembalian Buku</h3>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-alt me-2"></i>Riwayat Pengembalian</span>
                <span class="badge bg-light text-dark"><?php echo $total_dikembalikan; ?> Data</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="text-white">No.</th>
                                <th class="text-white">ID Peminjaman</th>
                                <th class="text-white">Username</th>
                                <th class="text-white">Judul Buku</th>
                                <th class="text-white">Tanggal Pinjam</th>
                                <th class="text-white">Tanggal Jatuh Tempo</th>
                                <th class="text-white">Tanggal Dikembalikan</th>
                                <th class="text-white">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_pengembalian->num_rows > 0) {
                                $no = 1;
                                while($row = $result_pengembalian->fetch_assoc()) {
                                    $tanggal_dikembalikan = isset($row['tanggal_dikembalikan']) ? $row['tanggal_dikembalikan'] : date('Y-m-d');
                                    
                                    // Tentukan status pengembalian
                                    $status_pengembalian = ($tanggal_dikembalikan <= $row['tanggal_kembali']) ? 'Tepat Waktu' : 'Terlambat';
                                    $status_class = ($status_pengembalian == 'Tepat Waktu') ? 'badge-dikembalikan' : 'badge-terlambat';
                                    
                                    echo "<tr>";
                                    echo "<td>{$no}</td>";
                                    echo "<td>PJ" . str_pad($row['id'], 3, '0', STR_PAD_LEFT) . "</td>";
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
                                echo "<tr><td colspan='8' class='text-center'>Belum ada data pengembalian</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default dates untuk filter
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.querySelector('input[name="start_date"]');
            const endDate = document.querySelector('input[name="end_date"]');
            
            if (!startDate.value) {
                // Set default start date to 30 days ago
                let defaultStart = new Date();
                defaultStart.setDate(defaultStart.getDate() - 30);
                startDate.valueAsDate = defaultStart;
            }
            
            if (!endDate.value) {
                // Set default end date to today
                endDate.valueAsDate = new Date();
            }
        });
    </script>
</body>
</html>