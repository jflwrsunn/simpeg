<?php
session_start();
$logged_in_user = $_SESSION['username'] ?? $_SESSION['admin'] ?? null;
if (!$logged_in_user) {
    header("Location: login.php");
    exit;
}
include 'db.php';

// Proses Input Data oleh Staff
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
    
    header("Location: staff_dashboard.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff — SIMPEG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="staff_dashboard.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-network-wired fa-sm"></i>
                </div>
                <span>SIMPEG <span class="text-muted fw-normal fs-6">Enterprise Corp (Staff)</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="profil.php" class="btn btn-sm btn-outline-dark fw-medium px-3"><i class="fas fa-user-circle me-1"></i> Profil Saya</a>
                <span class="badge bg-dark px-3 py-2 fw-semibold"><i class="fas fa-user me-1"></i> <?= htmlspecialchars($logged_in_user); ?> (hr_staff)</span>
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container my-5 flex-grow-1" style="max-width: 600px;">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="fas fa-check-circle me-1"></i> Data dan berkas berhasil disimpan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card bg-white p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-cloud-upload-alt text-primary me-2"></i> Input Data & Dokumen Karyawan</h5>
            <p class="text-muted small mb-4">Silakan masukkan data kepegawaian dan lampirkan berkas dokumen melalui formulir di bawah ini.</p>
            
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
                    <input type="file" name="berkas" class="form-control" required>
                </div>
                <button type="submit" name="tambah_data" class="btn btn-dark w-100 py-2 fw-semibold">
                    <i class="fas fa-save me-1"></i> Simpan Data & Unggah
                </button>
            </form>
        </div>
    </div>
</body>
</html>