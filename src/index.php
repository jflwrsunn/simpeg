<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

// Validasi role ketat: Cek langsung ke database untuk memastikan role benar-benar admin
$current_username = $_SESSION['username'];
$role_check_stmt = $conn->prepare("SELECT role FROM users WHERE username = ?");
$role_check_stmt->bind_param("s", $current_username);
$role_check_stmt->execute();
$role_result = $role_check_stmt->get_result()->fetch_assoc();
$role_check_stmt->close();

$real_role = $role_result['role'] ?? 'user';
// Mengecek apakah string role mengandung kata "admin" (tidak peduli huruf besar/kecil)
$is_admin = (stripos(strtolower($real_role), 'admin') !== false);

// Sinkronkan session agar konsisten dengan database
$_SESSION['role'] = $real_role;

$message = "";
$error_msg = "";

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $nip = trim($_POST['nip']);
    $jabatan = trim($_POST['jabatan']);
    
    // Validasi input ketat
    if (!preg_match("/^[a-zA-Z\s\.\']+$/", $nama)) {
        $error_msg = "Format Nama tidak valid. Nama hanya boleh berisi huruf, spasi, dan titik.";
    } 
    elseif (!ctype_digit($nip)) {
        $error_msg = "Format NIP/NIK tidak valid. Kolom harus berupa angka.";
    } 
    else {
        $target_dir = __DIR__ . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Keamanan Upload: Validasi ekstensi file untuk mencegah webshell upload (.php, .phtml, dll)
        $original_name = basename($_FILES['foto']['name']);
        $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'txt'];
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $error_msg = "[SECURITY WARNING] Ekstensi berkas tidak diizinkan! Hanya diperbolehkan dokumen atau gambar.";
        } else {
            $file_name = time() . "_" . preg_replace("/\s+/", "_", preg_replace("/[^a-zA-Z0-9\.\-_]/", "", $original_name));
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nama, $nip, $jabatan, $file_name);
                $stmt->execute();
                $stmt->close();
                
                $log_stmt = $conn->prepare("INSERT INTO activity_logs (username, activity) VALUES (?, ?)");
                $log_act = "Mengunggah berkas terverifikasi: $file_name";
                $log_stmt->bind_param("ss", $current_username, $log_act);
                $log_stmt->execute();
                $log_stmt->close();
                
                $message = "Dokumen arsip kepegawaian berhasil divalidasi dan disimpan ke server.";
            } else {
                $error_msg = "[ERROR-ERR_FILE_IO] Gagal memproses penyimpanan berkas ke direktori server.";
            }
        }
    }
}

// Proteksi backend untuk aksi hapus (Double Check Admin Role)
if (isset($_GET['hapus'])) {
    if (!$is_admin) {
        // Logging percobaan unauthorized deletion
        $conn->query("INSERT INTO activity_logs (username, activity) VALUES ('$current_username', 'PERINGATAN: Mencoba akses ilegal menghapus data pegawai!')");
        header("Location: index.php?error=unauthorized");
        exit;
    }
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM pegawai WHERE id = $id");
    
    $conn->query("INSERT INTO activity_logs (username, activity) VALUES ('$current_username', 'Menghapus data pegawai ID: $id')");
    header("Location: index.php");
    exit;
}

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
    <title>SIMPEG — PT Telekomunikasi Media Nusantara</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .card-header { background-color: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; padding: 1.25rem 1.5rem; }
        .btn-primary { background-color: #0f172a; border-color: #0f172a; border-radius: 8px; padding: 0.6rem 1rem; font-weight: 500; }
        .btn-primary:hover { background-color: #1e293b; border-color: #1e293b; }
        .form-control, .form-select { border-radius: 8px; padding: 0.65rem 0.85rem; border-color: #cbd5e1; }
        .table-custom th { background-color: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; }
        .badge-soft-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-soft-primary { background-color: #e0f2fe; color: #0369a1; }
        .alert-announcement { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border-radius: 12px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="index.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-network-wired fa-sm"></i>
                </div>
                <span>SIMPEG <span class="text-muted fw-normal fs-6">Enterprise Corp</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <?php if ($is_admin): ?>
                    <a href="pegawai.php" class="btn btn-sm btn-light border text-secondary fw-medium"><i class="fas fa-users me-1 text-success"></i> Data Pegawai</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <?php if(isset($_GET['error']) && $_GET['error'] == 'unauthorized'): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fas fa-shield-alt me-2"></i><strong>Akses Ditolak!</strong> Aktivitas ilegal Anda telah tercatat di log keamanan sistem.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="alert alert-announcement shadow-sm p-4 mb-4 border-0 d-flex align-items-center justify-content-between" role="alert">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-15 p-3 rounded-3 me-3 text-info">
                    <i class="fas fa-bullhorn fa-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Pengumuman Internal Korporat</h6>
                    <p class="mb-0 small text-light opacity-75">Sistem menggunakan enkripsi berbasis database. Perubahan hak akses tanpa otorisasi akan masuk ke Audit Trail.</p>
                </div>
            </div>
        </div>

        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $error_msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

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
                            <span class="text-muted small d-block fw-medium">Sesi Terverifikasi</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($current_username); ?> 
                                <span class="badge badge-soft-primary fs-6 align-middle fw-semibold"><?= htmlspecialchars($real_role); ?></span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
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
                                <label class="form-label small fw-semibold text-secondary">NIP / NIK Karyawan</label>
                                <input type="text" name="nip" class="form-control" placeholder="Contoh: 198001012005011001" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Jabatan / Divisi</label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Senior Infrastructure Analyst" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Lampiran Berkas</label>
                                <input type="file" name="foto" class="form-control" required>
                                <div class="form-text text-muted" style="font-size: 11px;">Format dibatasi: PDF, JPG, PNG, DOC, DOCX.</div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="fas fa-save me-1"></i> Simpan Data
                            </button>
                        </form>
                    </div>
                </div>

                <?php if ($is_admin): ?>
                <div class="card bg-white mt-4">
                    <div class="card-header bg-transparent py-3">
                        <span class="fw-bold text-dark"><i class="fas fa-history text-secondary me-2"></i> Log Audit Keamanan</span>
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

            <div class="col-lg-8">
                <div class="card bg-white">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-table text-primary me-2"></i> Daftar Karyawan & Arsip File</span>
                        <span class="badge badge-soft-danger px-2 py-1 fw-medium">Khusus Admin</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($is_admin): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 table-custom">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Karyawan</th>
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
                                <p class="small text-muted mb-0" style="max-width: 400px; margin: 0 auto;">Modul direktori kepegawaian pusat dilindungi oleh sistem otorisasi tingkat lanjut. Percobaan akses ilegal akan terekam pada log auditor.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white text-center py-3 mt-auto border-top">
        <div class="container small text-muted">
            <p class="mb-1">© 2026 Divisi Teknologi Informasi — <strong>PT Telekomunikasi Media Nusantara</strong></p>
            <p class="mb-0" style="font-size: 11px;">SIMPEG Enterprise System v3.5.0-Hardened</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>