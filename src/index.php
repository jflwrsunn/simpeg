<?php
session_start();

// Cek session login
if (isset($_SESSION['username'])) {
    $logged_in_user = $_SESSION['username'];
} elseif (isset($_SESSION['admin'])) {
    $logged_in_user = $_SESSION['admin'];
} else {
    header("Location: login.php");
    exit;
}

include 'config.php';

// Ambil data role user dari database
$stmt_role = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt_role->bind_param("s", $logged_in_user);
$stmt_role->execute();
$res_role = $stmt_role->get_result()->fetch_assoc();
$stmt_role->close();

if (isset($res_role['role'])) {
    $real_role = $res_role['role'];
} elseif (isset($_SESSION['role'])) {
    $real_role = $_SESSION['role'];
} else {
    $real_role = 'user';
}
$_SESSION['role'] = $real_role;

// Cek level admin dan super admin
$is_admin = (strtolower($real_role) === 'admin' || stripos($real_role, 'super') !== false);
$is_super_admin = (stripos($real_role, 'super') !== false || strtolower($real_role) === 'superadmin');

$message = "";
$error_msg = "";

// Proses Tambah Data dengan Validasi Tipe Kolom
if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $nip = trim($_POST['nip']);
    $jabatan = trim($_POST['jabatan']);
    
    // Validasi Sisi Server: NIP harus angka, Nama/Jabatan harus huruf/spasi
    if (!ctype_digit($nip)) {
        $error_msg = "Validasi Gagal: Kolom NIP/NIK harus berupa angka murni (0-9).";
    } elseif (!preg_match("/^[a-zA-Z\s\.]+$/", $nama)) {
        $error_msg = "Validasi Gagal: Nama lengkap hanya boleh mengandung huruf, spasi, dan titik.";
    } else {
        $target_dir = dirname(__FILE__) . "/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $original_name = basename($_FILES['foto']['name']);
        $file_name = time() . "_" . preg_replace("/\s+/", "_", $original_name);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $nip, $jabatan, $file_name);
            $stmt.execute();
            $stmt.close();
            
            $log_stmt = $conn->prepare("INSERT INTO activity_logs (username, activity) VALUES (?, ?)");
            $log_act = "Mengunggah dokumen pegawai baru: $nama";
            $log_stmt->bind_param("ss", $logged_in_user, $log_act);
            $log_stmt->execute();
            $log_stmt->close();
            
            $message = "Dokumen arsip kepegawaian berhasil divalidasi dan disimpan ke server.";
        } else {
            $error_msg = "Gagal memproses penyimpanan berkas ke direktori server.";
        }
    }
}

// Logika Hapus Data (Khusus Admin)
if (isset($_GET['hapus']) && $is_admin) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM pegawai WHERE id = $id");
    header("Location: index.php");
    exit;
}

$pegawai_result = null;
$pegawai_rahasia_result = null;

if ($is_admin) {
    $pegawai_result = $conn->query("SELECT * FROM pegawai ORDER BY id DESC");
}

// DATA RAHASIA: Hanya bisa ditarik jika user adalah Super Admin
if ($is_super_admin) {
    // Pastikan tabel pegawai_rahasia sudah dibuat di database Anda
    $pegawai_rahasia_result = $conn->query("SELECT * FROM pegawai_rahasia ORDER BY id DESC");
}

$total_pegawai_res = $conn->query("SELECT COUNT(*) as total FROM pegawai");
$total_pegawai = ($total_pegawai_res) ? $total_pegawai_res->fetch_assoc()['total'] : 0;
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
        .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
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
                    <p class="mb-0 small text-light opacity-75">Sistem menggunakan validasi ketat dan tingkat akses berbasis peran (RBAC).</p>
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
                            <h3 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($logged_in_user); ?> 
                                <span class="badge badge-soft-primary fs-6 align-middle fw-semibold"><?= htmlspecialchars($real_role); ?></span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Form Input dengan Validasi Tipe Kolom -->
            <div class="col-lg-4">
                <div class="card bg-white">
                    <div class="card-header bg-transparent py-3">
                        <span class="fw-bold text-dark"><i class="fas fa-cloud-upload-alt text-primary me-2"></i> Input Data & Dokumen</span>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Nama Lengkap & Gelar (Harus Huruf)</label>
                                <input type="text" name="nama" class="form-control" placeholder="Contoh: Budi Santoso" pattern="[a-zA-Z\s\.]+" title="Hanya boleh huruf, spasi, dan titik" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">NIP / NIK Karyawan (Harus Angka)</label>
                                <input type="text" name="nip" class="form-control" placeholder="Contoh: 19800101200501" pattern="[0-9]+" title="NIP wajib berupa angka murni" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Jabatan / Divisi (Harus Huruf)</label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Senior Infrastructure" pattern="[a-zA-Z\s\.]+" title="Hanya boleh huruf" required>
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

            <!-- Tabel Data Utama & Tabel Data Rahasia Super Admin -->
            <div class="col-lg-8">
                <!-- Tabel Pegawai Umum -->
                <div class="card bg-white mb-4">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-table text-primary me-2"></i> Daftar Karyawan & Arsip File</span>
                        <span class="badge badge-soft-danger px-2 py-1 fw-medium">Khusus Admin</span>
                    </div>
                    <div class="card-body">
                        <?php if ($is_admin): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>NIP</th>
                                            <th>Jabatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while($row = $pegawai_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td class="fw-semibold"><?= htmlspecialchars($row['nama']); ?></td>
                                            <td><?= htmlspecialchars($row['nip']); ?></td>
                                            <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                            <td>
                                                <a href="?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted px-4">
                                <i class="fas fa-lock fa-2x mb-2 text-secondary"></i>
                                <h6 class="fw-bold text-dark">Akses Terbatas Administrator</h6>
                                <p class="small text-muted mb-0">Anda memerlukan role admin untuk melihat direktori ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tabel Rahasia: HANYA Super Admin yang Bisa Mengakses -->
                <div class="card bg-white border border-warning">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-danger"><i class="fas fa-user-secret me-2"></i> Arsip Data Rahasia Eksekutif (Restricted)</span>
                        <span class="badge badge-soft-warning px-2 py-1 fw-medium">Super Admin Only</span>
                    </div>
                    <div class="card-body">
                        <?php if ($is_super_admin && $pegawai_rahasia_result): ?>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Eksekutif</th>
                                            <th>Kode Sandi / Gaji</th>
                                            <th>Keterangan Rahasia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($sec_row = $pegawai_rahasia_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $sec_row['id']; ?></td>
                                            <td class="fw-bold text-danger"><?= htmlspecialchars($sec_row['nama']); ?></td>
                                            <td><code><?= htmlspecialchars($sec_row['gaji_sandi']); ?></code></td>
                                            <td><?= htmlspecialchars($sec_row['keterangan']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted px-4">
                                <div class="bg-light rounded-circle d-inline-flex p-3 mb-2 text-danger">
                                    <i class="fas fa-shield-alt fa-2x"></i>
                                </div>
                                <h6 class="fw-bold text-danger">AKSES DITOLAK (403 Forbidden)</h6>
                                <p class="small text-muted mb-0" style="max-width: 400px; margin: 0 auto;">
                                    Data arsip rahasia eksekutif ini dilindungi tingkat enkripsi tertinggi dan hanya terbuka bagi akun dengan hak akses <b>Super Administrator</b>.
                                </p>
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