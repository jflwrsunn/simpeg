<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

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
        
        // Catat ke Audit Log
        $curr_user = $_SESSION['username'];
        $conn->query("INSERT INTO activity_logs (username, activity) VALUES ('$curr_user', 'Mengunggah file lampiran: $file_name')");
        
        $message = "Berhasil mengunggah dokumen arsip ke server pusat!";
    } else {
        $message = "Gagal mengunggah file. Periksa izin direktori uploads.";
    }
}

if (isset($_GET['hapus']) && isset($_SESSION['role']) && strpos(strtolower($_SESSION['role']), 'admin') !== false) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM pegawai WHERE id = $id");
    
    $curr_user = $_SESSION['username'];
    $conn->query("INSERT INTO activity_logs (username, activity) VALUES ('$curr_user', 'Menghapus data pegawai ID: $id')");
    
    header("Location: index.php");
    exit;
}

$result = $conn->query("SELECT * FROM pegawai ORDER BY id DESC");
$total_pegawai = $conn->query("SELECT COUNT(*) as total FROM pegawai")->fetch_assoc()['total'];
$logs_result = $conn->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMPEG — Badan Siber dan Sandi Negara</title>
    <!-- Favicon Resmi BSSN Dummy -->
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/9/9f/Logo_Badan_Siber_dan_Sandi_Negara.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-4">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-shield-alt text-primary me-2"></i>SIMPEG <span class="text-muted fs-6">BSSN</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <a href="report.php" class="btn btn-sm btn-outline-info me-2"><i class="fas fa-chart-bar me-1"></i> Laporan</a>
            <a href="helpdesk.php" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-headset me-1"></i> Bantuan</a>
            <a href="profile.php?id=<?= $_SESSION['user_id']; ?>" class="btn btn-sm btn-outline-light me-2"><i class="fas fa-user-cog me-1"></i> Profil</a>
            <a href="logout.php" class="btn btn-sm btn-danger"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <!-- Banner Pengumuman Resmi Instansi -->
        <div class="alert alert-primary shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-bullhorn fa-2x me-3 text-primary"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-1">PENGUMUMAN KEDINASAN SISTEM</h6>
                <p class="mb-0 small">Pemeliharaan rutin infrastruktur pusat data dijadwalkan pada hari Jumat pukul 21.00 WIB. Pastikan seluruh laporan sinkronisasi data pegawai telah diunggah sebelum batas waktu tersebut.</p>
            </div>
        </div>

        <?php if($message): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm"><?= $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm bg-primary text-white p-3">
                    <h6 class="text-uppercase small fw-bold">Total Pegawai Terdaftar</h6>
                    <h2 class="fw-bold mb-0"><?= $total_pegawai; ?> Orang</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-dark text-white p-3">
                    <h6 class="text-uppercase small fw-bold">Sesi Pengguna Aktif</h6>
                    <h2 class="fw-bold mb-0"><?= htmlspecialchars($_SESSION['username']); ?> <span class="badge bg-secondary fs-6 align-middle"><?= htmlspecialchars($_SESSION['role'] ?? 'Operator'); ?></span></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Upload Arsip -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3"><i class="fas fa-file-upload text-primary me-2"></i> Input Data & Dokumen Pegawai</div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso, S.Kom." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">NIP (Nomor Induk Pegawai)</label>
                                <input type="text" name="nip" class="form-control" placeholder="198xxxx xxxxx x xxx" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jabatan Struktural / Fungsional</label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Analis Sandi Madya" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Lampiran Berkas / Dokumen</label>
                                <input type="file" name="foto" class="form-control" required>
                                <div class="form-text text-muted" style="font-size: 11px;">Mendukung berbagai format dokumen dan arsip digital.</div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100 fw-bold shadow-sm"><i class="fas fa-save me-1"></i> Simpan ke Server</button>
                        </form>
                    </div>
                </div>

                <!-- Widget Audit Log Sederhana -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white fw-bold py-3"><i class="fas fa-history text-secondary me-2"></i> Log Aktivitas Terakhir</div>
                    <div class="card-body p-2">
                        <div class="list-group list-group-flush small">
                            <?php while($log = $logs_result->fetch_assoc()): ?>
                                <div class="list-group-item px-2 py-1">
                                    <span class="fw-bold text-dark"><?= htmlspecialchars($log['username']); ?></span>: 
                                    <span class="text-muted"><?= htmlspecialchars($log['activity']); ?></span>
                                    <div class="text-muted" style="font-size: 10px;"><?= $log['created_at']; ?></div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Khusus Admin -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-database text-primary me-2"></i> Daftar Pegawai & Arsip File Kepegawaian</span>
                        <span class="badge bg-danger">Akses Terbatas Administrator</span>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['role']) && strpos(strtolower($_SESSION['role']), 'admin') !== false): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama & NIP</th>
                                            <th>Jabatan</th>
                                            <th>File Lampiran</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td>
                                                <span class="fw-bold d-block"><?= htmlspecialchars($row['nama']); ?></span>
                                                <span class="text-muted small"><?= htmlspecialchars($row['nip']); ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                            <td>
                                                <?php if($row['foto']): ?>
                                                    <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt"></i> <?= $row['foto']; ?></a>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data arsip ini secara permanen?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-user-shield fa-3x mb-3 text-secondary"></i>
                                <h5 class="fw-bold">Pemeriksaan Hak Akses (Access Control)</h5>
                                <p class="small mb-0">Modul tabel rekapitulasi data dan manajemen arsip pusat hanya dapat diakses menggunakan kredensial tingkat Administrator.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Resmi Instansi -->
    <footer class="bg-dark text-white text-center py-3 mt-auto border-top border-secondary">
        <div class="container small">
            <p class="mb-1">© 2026 Direktorat Pengembangan Sistem Elektronik — <strong>Badan Siber dan Sandi Negara (BSSN)</strong></p>
            <p class="text-muted mb-0" style="font-size: 11px;">Sistem Informasi Kepegawaian Internal (SIMPEG) v3.4.2-RELEASE</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>