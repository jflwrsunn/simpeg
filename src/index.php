<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

$message = "";
// Tambah Pegawai & Upload File
if (isset($_POST['submit'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $nip = $conn->real_escape_string($_POST['nip']);
    $jabatan = $conn->real_escape_string($_POST['jabatan']);
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = $_FILES['foto']['name'];
    $target_file = $target_dir . basename($file_name);
    
    // Unrestricted file upload vulnerability intentionally kept for lab
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $conn->query("INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES ('$nama', '$nip', '$jabatan', '$file_name')");
        $message = "Data pegawai berhasil ditambahkan!";
    } else {
        $message = "Gagal mengunggah file dokumen.";
    }
}

// Hapus Pegawai
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM pegawai WHERE id = $id");
    header("Location: index.php");
    exit;
}

$result = $conn->query("SELECT * FROM pegawai ORDER BY id DESC");
$total_pegawai = $conn->query("SELECT COUNT(*) as total FROM pegawai")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SIMPEG BSSN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-id-card text-primary me-2"></i>SIMPEG BSSN</a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small"><i class="fas fa-user-circle me-1"></i> <?= $_SESSION['user']; ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 my-4">
        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="init">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-bold">Total Pegawai</h6>
                            <h3 class="fw-bold mb-0"><?= $total_pegawai; ?> Orang</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-bold">Status Sistem</h6>
                            <h3 class="fw-bold mb-0">Online (Lab)</h3>
                        </div>
                        <i class="fas fa-server fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-dark text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase small fw-bold">Hak Akses</h6>
                            <h3 class="fw-bold mb-0 text-capitalize"><?= $_SESSION['role']; ?></h3>
                        </div>
                        <i class="fas fa-shield-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Tambah -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3">
                        <i class="fas fa-user-plus text-primary me-2"></i> Input Pegawai Baru
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap & Gelar</label>
                                <input type="text" name="nama" class="form-control" required placeholder="Contoh: Budi Santoso, M.Kom">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">NIP</label>
                                <input type="text" name="nip" class="form-control" required placeholder="Nomor Induk Pegawai">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" required placeholder="Jabatan struktural">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Upload Dokumen / Foto Pendukung</label>
                                <input type="file" name="foto" class="form-control" required>
                                <div class="form-text text-muted" style="font-size: 0.75rem;">Format bebas (Modul training file upload).</div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100 fw-bold">Simpan Data Pegawai</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-table text-primary me-2"></i> Direktori Pegawai BSSN</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama & NIP</th>
                                        <th>Jabatan</th>
                                        <th>Dokumen</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td>
                                            <span class="fw-bold d-block"><?= htmlspecialchars($row['nama']); ?></span>
                                            <span class="text-muted small">NIP: <?= htmlspecialchars($row['nip']); ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                        <td>
                                            <?php if($row['foto']): ?>
                                                <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-download"></i> Lihat</a>
                                            <?php else: ?>
                                                <span class="text-muted small">Tidak ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>