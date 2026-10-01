<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

// Validasi ketat: Hanya izinkan admin yang bisa mengakses halaman ini
$is_admin = isset($_SESSION['role']) && strpos(strtolower($_SESSION['role']), 'admin') !== false;
if (!$is_admin) {
    // Jika bukan admin, redirect kembali ke index dengan pesan atau blokir
    header("Location: index.php?error=unauthorized");
    exit;
}

$result = $conn->query("SELECT * FROM pegawai ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direktori Pegawai — SIMPEG Enterprise</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .table-custom th { background-color: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar Modern -->
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="index.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-network-wired fa-sm"></i>
                </div>
                <span>SIMPEG <span class="text-muted fw-normal fs-6">Enterprise Corp</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="index.php" class="btn btn-sm btn-light border text-secondary fw-medium"><i class="fas fa-home me-1 text-primary"></i> Beranda</a>
                <a href="pegawai.php" class="btn btn-sm btn-dark fw-medium"><i class="fas fa-users me-1"></i> Data Pegawai</a>
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Direktori Pegawai Korporat</h3>
                <p class="text-muted small mb-0">Daftar informasi kepegawaian dan berkas data internal perusahaan.</p>
            </div>
            <div>
                <span class="badge bg-dark px-3 py-2">Masuk sebagai: <strong><?= htmlspecialchars($_SESSION['username']); ?></strong></span>
            </div>
        </div>

        <div class="card bg-white p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-custom">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Nama Lengkap</th>
                            <th>NIP / NIK</th>
                            <th>Jabatan</th>
                            <th class="text-end pe-4">Berkas Lampiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= $row['id']; ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><code><?= htmlspecialchars($row['nip']); ?></code></td>
                                    <td class="text-secondary"><?= htmlspecialchars($row['jabatan']); ?></td>
                                    <td class="text-end pe-4">
                                        <?php if(!empty($row['foto'])): ?>
                                            <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="btn btn-sm btn-light border text-primary fw-medium px-2 py-1 shadow-sm" style="font-size: 12px;">
                                                <i class="fas fa-external-link-alt me-1"></i> <?= $row['foto']; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">Tidak ada berkas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data pegawai tersedia.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="bg-white text-center py-3 mt-auto border-top">
        <div class="container small text-muted">
            <p class="mb-1">© 2026 Divisi Teknologi Informasi — <strong>PT Telekomunikasi Media Nusantara</strong></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>