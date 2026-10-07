<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

$current_username = $_SESSION['username'];
$real_role = $_SESSION['role'] ?? 'user';
$is_admin = (stripos(strtolower($real_role), 'admin') !== false);

$message = "";
$error_msg = "";

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $nip = trim($_POST['nip']);
    $jabatan = trim($_POST['jabatan']);
    
    $target_dir = __DIR__ . "/uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $original_name = basename($_FILES['foto']['name']);
    $file_name = time() . "_" . preg_replace("/\s+/", "_", $original_name);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $nip, $jabatan, $file_name);
        $stmt->execute();
        $stmt->close();
        $message = "Dokumen arsip kepegawaian berhasil disimpan ke server.";
    } else {
        $error_msg = "Gagal memproses penyimpanan berkas ke direktori server.";
    }
}

$total_pegawai = $conn->query("SELECT COUNT(*) as total FROM pegawai")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG — PT Telekomunikasi Media Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .card-header { background-color: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; padding: 1.25rem 1.5rem; }
        .btn-primary { background-color: #0f172a; border-color: #0f172a; border-radius: 8px; padding: 0.6rem 1rem; font-weight: 500; }
        .btn-primary:hover { background-color: #1e293b; border-color: #1e293b; }
        .form-control { border-radius: 8px; padding: 0.65rem 0.85rem; border-color: #cbd5e1; }
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
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
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
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="fas fa-save me-1"></i> Simpan Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card bg-white">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-table text-primary me-2"></i> Daftar Karyawan & Arsip File</span>
                        <span class="badge badge-soft-danger px-2 py-1 fw-medium">Khusus Admin</span>
                    </div>
                    <div class="card-body">
                        <?php if ($is_admin): ?>
                            <!-- Tabel data pegawai untuk admin -->
                            <p class="text-muted small">Tabel data pegawai aktif...</p>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>