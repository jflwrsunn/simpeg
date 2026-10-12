<?php
session_start();

if (isset($_SESSION['username'])) {
    $logged_in_user = $_SESSION['username'];
} elseif (isset($_SESSION['admin'])) {
    $logged_in_user = $_SESSION['admin'];
} else {
    header("Location: login.php");
    exit;
}

include 'db.php';

$stmt_role = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt_role->bind_param("s", $logged_in_user);
$stmt_role->execute();
$res_role = $stmt_role->get_result()->fetch_assoc();
$stmt_role->close();

$real_role = $res_role['role'] ?? $_SESSION['role'] ?? 'user';
$_SESSION['role'] = $real_role;

// Logika RBAC yang Dinamis Berdasarkan Role Database
$role_lower = strtolower(trim($real_role));
$is_super_admin = ($role_lower === 'super_admin' || $role_lower === 'administrator');
$is_admin = $is_super_admin; // Hanya admin asli yang bisa hapus & akses penuh
$is_verifikator = ($role_lower === 'verifikator');

$pegawai_result = null;
$pegawai_rahasia_result = null;

// Verifikator dan Admin bisa melihat tabel pegawai umum
if ($is_admin || $is_verifikator) {
    $pegawai_result = $conn->query("SELECT * FROM pegawai ORDER BY id DESC");
}

// Hanya Super Admin yang bisa melihat tabel rahasia
if ($is_super_admin) {
    $pegawai_rahasia_result = $conn->query("SELECT id, nama, gaji_sandi, keterangan FROM pegawai_rahasia ORDER BY id DESC");
}

$message = "";
$error_msg = "";

if (isset($_POST['submit']) && $is_admin) {
    $nama = trim($_POST['nama']);
    $nip = trim($_POST['nip']);
    $jabatan = trim($_POST['jabatan']);
    
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
            if ($stmt) {
                $stmt->bind_param("ssss", $nama, $nip, $jabatan, $file_name);
                $stmt->execute();
                $stmt->close();
            }
            $message = "Dokumen arsip kepegawaian berhasil divalidasi dan disimpan ke server.";
        } else {
            $error_msg = "Gagal memproses penyimpanan berkas ke direktori server.";
        }
    }
}

if (isset($_GET['hapus']) && $is_admin) {
    $id = intval($_GET['hapus']);
    $q_file = $conn->query("SELECT foto FROM pegawai WHERE id = $id");
    if ($q_file && $q_file->num_rows > 0) {
        $f_row = $q_file->fetch_assoc();
        if (!empty($f_row['foto']) && file_exists(dirname(__FILE__) . "/uploads/" . $f_row['foto'])) {
            unlink(dirname(__FILE__) . "/uploads/" . $f_row['foto']);
        }
    }
    $conn->query("DELETE FROM pegawai WHERE id = $id");
    header("Location: index.php");
    exit;
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

        <div class="row g-4">
            <!-- Form Input hanya muncul untuk Super Admin -->
            <?php if($is_admin): ?>
            <div class="col-lg-4">
                <div class="card bg-white">
                    <div class="card-header bg-transparent py-3">
                        <span class="fw-bold text-dark"><i class="fas fa-cloud-upload-alt text-primary me-2"></i> Input Data & Dokumen</span>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">NIP / NIK Karyawan</label>
                                <input type="text" name="nip" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Jabatan / Divisi</label>
                                <input type="text" name="jabatan" class="form-control" required>
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
            <?php endif; ?>

            <div class="<?= $is_admin ? 'col-lg-8' : 'col-lg-12'; ?>">
                <!-- Tabel Pegawai Umum -->
                <div class="card bg-white mb-4">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-table text-primary me-2"></i> Daftar Karyawan & Arsip File</span>
                        <span class="badge badge-soft-danger px-2 py-1 fw-medium"><?= $is_admin ? 'Full Access' : 'Verifikator (Read-Only File)'; ?></span>
                    </div>
                    <div class="card-body">
                        <?php if ($pegawai_result): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>NIP</th>
                                            <th>Jabatan</th>
                                            <th>Berkas</th>
                                            <?php if($is_admin): ?><th>Aksi</th><?php endif; ?>
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
                                                <?php if (!empty($row['foto'])): ?>
                                                    <?php if ($is_admin): ?>
                                                        <a href="uploads/<?= htmlspecialchars($row['foto']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat File</a>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary text-white" title="Akses unduh/lihat file dibatasi untuk verifikator">Terkunci (Restricted)</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted small">Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if($is_admin): ?>
                                            <td>
                                                <a href="?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash-alt"></i></a>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tabel Arsip Rahasia Eksekutif -->
                <div class="card bg-white">
                    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="fas fa-user-secret text-danger me-2"></i> Arsip Data Rahasia Eksekutif (Restricted)</span>
                        <span class="badge badge-soft-warning px-2 py-1 fw-medium">Super Admin Only</span>
                    </div>
                    <div class="card-body">
                        <?php if ($pegawai_rahasia_result): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Rahasia</th>
                                            <th>Gaji & Sandi</th>
                                            <th>Keterangan Khusus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no_r = 1; while($row_r = $pegawai_rahasia_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no_r++; ?></td>
                                            <td class="fw-semibold text-danger"><?= htmlspecialchars($row_r['nama']); ?></td>
                                            <td><?= htmlspecialchars($row_r['gaji_sandi']); ?></td>
                                            <td><?= htmlspecialchars($row_r['keterangan'] ?? 'Confidential'); ?></td>
                                        </tr>
                                        <?php endfor; // Diperbaiki jadi endwhile ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted px-4">
                                <i class="fas fa-shield-alt fa-2x mb-2 text-danger opacity-50"></i>
                                <h6 class="fw-bold text-dark">AKSES DITOLAK (403 Forbidden)</h6>
                                <p class="small text-muted mb-0">Data arsip rahasia eksekutif ini hanya terbuka bagi akun dengan hak akses <strong>Super Administrator</strong>.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>