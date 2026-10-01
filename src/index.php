<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

// Cek apakah user saat ini memiliki role Administrator
$is_admin = isset($_SESSION['role']) && strpos(strtolower($_SESSION['role']), 'admin') !== false;

$message = "";
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $jabatan = $_POST['jabatan'];
    
    $target_dir = __DIR__ . "/uploads/";
    if (is_file(__DIR__ . "/uploads")) {
        unlink(__DIR__ . "/uploads");
    }
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $original_name = basename($_FILES['foto']['name']);
    $file_name = time() . "_" . preg_replace("/\s+/", "_", $original_name);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $conn->query("INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES ('$nama', '$nip', '$jabatan', '$file_name')");
        
        $curr_user = $_SESSION['username'];
        $conn->query("INSERT INTO activity_logs (username, activity) VALUES ('$curr_user', 'Mengunggah file lampiran: $file_name')");
        
        $message = "Dokumen arsip kepegawaian berhasil disinkronisasi ke server pusat.";
    } else {
        $message = "[ERROR-ERR_FILE_IO] Gagal memproses penyimpanan berkas ke direktori server. Periksa kembali ukuran atau ekstensi file.";
    }
}

// Proteksi backend untuk aksi hapus (Hanya admin yang boleh mengeksekusi)
if (isset($_GET['hapus']) && $is_admin) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM pegawai WHERE id = $id");
    
    $curr_user = $_SESSION['username'];
    $conn->query("INSERT INTO activity_logs (username, activity) VALUES ('$curr_user', 'Menghapus data pegawai ID: $id')");
    
    header("Location: index.php");
    exit;
}

// Ambil data hanya jika admin (menghemat resource dan memperketat akses backend)
$result = null;
$logs_result = null;
if ($is_admin) {
    $result = $conn->query("SELECT * FROM pegawai ORDER BY id DESC");
    $logs_result = $conn->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 5");
}

$total_pegawai = $conn->query("SELECT COUNT(*) as total FROM pegawai")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG — Badan Siber dan Sandi Negara</title>
    <!-- Favicon BSSN -->
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/9/9f/Logo_Badan_Siber_dan_Sandi_Negara.png" type="image/png">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #334155;
        }
        .navbar-custom {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f1f5f9;
            font-weight: 600;
            padding: 1.25rem 1.5rem;
        }
        .btn-primary {
            background-color: #0f172a;
            border-color: #0f172a;
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #1e293b;
            border-color: #1e293b;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.65rem 0.85rem;
            border-color: #cbd5e1;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
            border-color: #0f172a;
        }
        .table-custom th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
        }
        .badge-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-soft-primary {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .alert-announcement {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            border-radius: 12px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar Modern -->
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="index.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-shield-alt fa-sm"></i>
                </div>
                <span>SIMPEG <span class="text-muted fw-normal fs-6">BSSN</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="report.php" class="btn btn-sm btn-light border text-secondary fw-medium"><i class="fas fa-chart-bar me-1 text-primary"></i> Laporan</a>
                <a href="helpdesk.php" class="btn btn-sm btn-light border text-secondary fw-medium"><i class="fas fa-headset me-1 text-info"></i> Bantuan</a>
                <a href="profile.php?id=<?= $_SESSION['user_id']; ?>" class="btn btn-sm btn-light border text-secondary fw-medium"><i class="fas fa-user-cog me-1 text-dark"></i> Profil</a>
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <!-- Banner Pengumuman Estetik -->
        <div class="alert alert-announcement shadow-sm p-4 mb-4 border-0 d-flex align-items-center justify-content-between" role="alert">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-15 p-3 rounded-3 me-3 text-info">
                    <i class="fas fa-bullhorn fa-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Pengumuman Kedinasan Sistem</h6>
                    <p class="mb-0 small text-light opacity-75">Pemeliharaan rutin infrastruktur pusat data dijadwalkan pada hari Jumat pukul 21.00 WIB.</p>
                </div>
            </div>
        </div>

        <?php if($message): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i><?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistik Ringkas -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-light text-dark p-3 rounded-3 me-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block fw-medium">Total Pegawai Terdaftar</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= $total_pegawai; ?> <span class="fs-6 fw-normal text-muted">Orang</span></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-light text-dark p-3 rounded-3 me-3">
                            <i class="fas fa-user-shield fa-lg"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block fw-medium">Sesi Pengguna Aktif</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($_SESSION['username']); ?> 
                                <span class="badge badge-soft-primary fs-6 align-middle fw-semibold"><?= htmlspecialchars($_SESSION['role'] ?? 'Operator'); ?></span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Form Upload -->
            <div class="col-lg-4">
                <div class="card bg-white">
                    <div class="card-header bg-transparent py-3">
                        <span class="fw-bold text-dark"><i class="fas fa-cloud-upload-alt text-primary me-2"></i> Input Data & Dokumen</span>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso, S.Kom." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">NIP</label>
                                <input type="text" name="nip" class="form-control" placeholder="198xxxx xxxxx x xxx" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Analis Sandi Madya" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Lampiran Berkas</label>
                                <input type="file" name="foto" class="form-control" required>
                                <div class="form-text text-muted" style="font-size: 11px;">Mendukung berkas format PDF, gambar, atau dokumen arsip.</div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="fas fa-save me-1"></i> Simpan Data
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Widget Aktivitas Terakhir (Hanya muncul jika Administrator) -->
                <?php if ($is_admin): ?>
                <div class="card bg-white mt-4">
                    <div class="card-header bg-transparent py-3">
                        <span class="fw-bold text-dark"><i class="fas fa-history text-secondary me-2"></i> Log Aktivitas Sistem</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="list-group list-group-flush small">
                            <?php while($log = $logs_result->fetch_assoc()): ?>
                                <div class="list-group-item px-0 py-2 border-bottom">
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($log['username']); ?></div>
                                    <div class="text-muted text-truncate"><?= htmlspecialchars($log['activity']); ?></div>
                                    <div class="text-secondary opacity-50" style="font-size: 10px;"><?= $log['created_at']; ?></div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tabel Data Khusus Admin -->
            <div class="col-lg-8">
                <div class="card bg-white">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-table text-primary me-2"></i> Daftar Pegawai & Arsip File</span>
                        <span class="badge badge-soft-danger px-2 py-1 fw-medium">Khusus Admin</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($is_admin): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 table-custom">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Pegawai</th>
                                            <th>Jabatan</th>
                                            <th>File Lampiran</th>
                                            <th class="text-center pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4 text-muted"><?= $no++; ?></td>
                                            <td>
                                                <span class="fw-semibold text-dark d-block"><?= htmlspecialchars($row['nama']); ?></span>
                                                <span class="text-muted" style="font-size: 12px;"><?= htmlspecialchars($row['nip']); ?></span>
                                            </td>
                                            <td class="text-secondary"><?= htmlspecialchars($row['jabatan']); ?></td>
                                            <td>
                                                <?php if($row['foto']): ?>
                                                    <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="btn btn-sm btn-light border text-primary fw-medium px-2 py-1 shadow-sm" style="font-size: 12px;">
                                                        <i class="fas fa-external-link-alt me-1"></i> <?= $row['foto']; ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center pe-4">
                                                <a href="?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-light border text-danger px-2 py-1" onclick="return confirm('Hapus data arsip ini secara permanen?')" title="Hapus Data">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted px-4">
                                <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-secondary">
                                    <i class="fas fa-lock fa-2x"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Akses Terbatas Administrator</h6>
                                <p class="small text-muted mb-0" style="max-width: 400px; margin: 0 auto;">Modul rekapitulasi data dan arsip kepegawaian pusat hanya dapat diakses melalui kredensial tingkat Administrator.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Modern -->
    <footer class="bg-white text-center py-3 mt-auto border-top">
        <div class="container small text-muted">
            <p class="mb-1">© 2026 Direktorat Pengembangan Sistem Elektronik — <strong>Badan Siber dan Sandi Negara (BSSN)</strong></p>
            <p class="mb-0" style="font-size: 11px;">SIMPEG Internal Enterprise v3.4.2</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>