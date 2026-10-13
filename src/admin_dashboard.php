<?php
session_start();
$logged_in_user = $_SESSION['admin'] ?? $_SESSION['username'] ?? null;
if (!$logged_in_user) {
    header("Location: login.php");
    exit;
}
include 'db.php';

// Cek role admin
$stmt_role = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt_role->bind_param("s", $logged_in_user);
$stmt_role->execute();
$role = $stmt_role->get_result()->fetch_assoc()['role'] ?? 'admin';
$stmt_role->close();

if ($role == 'super_admin' || $logged_in_user == 'administrator') {
    header("Location: super_admin_dashboard.php");
    exit;
}

// Proses Input Data oleh Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_data'])) {
    $nama = trim($_POST['nama']);
    $nip = trim($_POST['nip']);
    $jabatan = trim($_POST['jabatan']);
    
    $filename = '';
    if (isset($_FILES['berkas']) && $_FILES['berkas']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['berkas']['name']);
        move_uploaded_file($_FILES['berkas']['tmp_name'], $upload_dir . $filename);
    }

    $stmt = $conn->prepare("INSERT INTO employees (nama, nip, jabatan, berkas, uploader) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $nip, $jabatan, $filename, $logged_in_user);
    $stmt->execute();
    $stmt->close();

    if ($filename) {
        $stmt_log = $conn->prepare("INSERT INTO upload_logs (filename, uploader, action) VALUES (?, ?, 'Upload Berkas')");
        $stmt_log->bind_param("ss", $filename, $logged_in_user);
        $stmt_log->execute();
        $stmt_log->close();
    }
    
    header("Location: admin_dashboard.php");
    exit;
}

$employees = $conn->query("SELECT * FROM employees ORDER BY id DESC");
$logs = $conn->query("SELECT * FROM upload_logs ORDER BY uploaded_at DESC LIMIT 15");
$account_logs = $conn->query("SELECT * FROM account_logs ORDER BY registered_at DESC LIMIT 15");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — SIMPEG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .card-header { background-color: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; padding: 1.25rem 1.5rem; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="admin_dashboard.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-network-wired fa-sm"></i>
                </div>
                <span>SIMPEG <span class="text-muted fw-normal fs-6">Enterprise Corp (Admin)</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="users.php" class="btn btn-sm btn-outline-primary fw-medium px-3"><i class="fas fa-users-cog me-1"></i> Kelola Pengguna</a>
                <a href="profil.php" class="btn btn-sm btn-outline-dark fw-medium px-3"><i class="fas fa-user-circle me-1"></i> Profil Saya</a>
                <span class="badge bg-dark px-3 py-2 fw-semibold"><i class="fas fa-user-shield me-1"></i> <?= htmlspecialchars($logged_in_user); ?> (admin)</span>
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4 flex-grow-1">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card bg-white p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-cloud-upload-alt text-primary me-2"></i> Input Data & Dokumen</h5>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nama Lengkap & Gelar</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Contoh: Budi Santoso, S.Kom.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">NIP / NIK Karyawan</label>
                            <input type="text" name="nip" class="form-control" required placeholder="198501012010121001">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Jabatan / Divisi</label>
                            <input type="text" name="jabatan" class="form-control" required placeholder="Senior Infrastructure Analyst">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Lampiran Berkas</label>
                            <input type="file" name="berkas" class="form-control">
                        </div>
                        <button type="submit" name="tambah_data" class="btn btn-dark w-100 py-2 fw-semibold">
                            <i class="fas fa-save me-1"></i> Simpan Data
                        </button>
                    </form>
                </div>

                <div class="card bg-white mb-4">
                    <div class="card-header py-3"><span><i class="fas fa-history me-2 text-secondary"></i> Log Aktivitas Unggah</span></div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                            <table class="table table-striped align-middle mb-0 small">
                                <thead class="table-light text-muted sticky-top">
                                    <tr><th class="ps-3">Waktu</th><th>User</th><th>Aksi</th></tr>
                                </thead>
                                <tbody>
                                    <?php if ($logs && $logs->num_rows > 0): while($log = $logs->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3 text-muted small" style="font-size: 11px;"><?= htmlspecialchars($log['uploaded_at']); ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($log['uploader']); ?></span></td>
                                        <td><span class="badge <?= ($log['action'] == 'Upload Berkas') ? 'bg-success' : 'bg-danger'; ?>"><?= htmlspecialchars($log['action']); ?></span></td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada log.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card bg-white mb-4">
                    <div class="card-header py-3"><span><i class="fas fa-user-plus me-2 text-primary"></i> Log Pendaftaran Akun Baru</span></div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                            <table class="table table-striped align-middle mb-0 small">
                                <thead class="table-light text-muted sticky-top">
                                    <tr><th class="ps-3">Waktu Daftar</th><th>Username Baru</th></tr>
                                </thead>
                                <tbody>
                                    <?php if ($account_logs && $account_logs->num_rows > 0): while($alog = $account_logs->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3 text-muted small" style="font-size: 11px;"><?= htmlspecialchars($alog['registered_at']); ?></td>
                                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($alog['username']); ?></span></td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="2" class="text-center py-3 text-muted">Belum ada pendaftaran.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card bg-white mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-table me-2 text-secondary"></i> Daftar Karyawan & Arsip File</span>
                        <span class="badge bg-secondary">admin</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small">
                                    <tr><th class="ps-3">No</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Berkas Upload</th><th class="text-center">Aksi</th></tr>
                                </thead>
                                <tbody>
                                    <?php if ($employees->num_rows > 0): $no = 1; while($row = $employees->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold"><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nama']); ?></td>
                                        <td class="text-muted small"><?= htmlspecialchars($row['nip']); ?></td>
                                        <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                        <td>
                                            <?php if (!empty($row['berkas'])): ?>
                                                <span class="badge bg-light text-muted border px-2 py-1"><i class="fas fa-lock me-1"></i> Restricted</span>
                                            <?php else: ?>
                                                <span class="text-muted small italic">Tidak ada file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted small" title="Akses Dihapus"><i class="fas fa-ban text-secondary"></i></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data karyawan.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card bg-light border text-center py-4">
                    <div class="text-muted small">
                        <i class="fas fa-lock me-1"></i> Arsip Data Rahasia Eksekutif terkunci. (Memerlukan hak akses <strong>Super Administrator</strong>).
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>