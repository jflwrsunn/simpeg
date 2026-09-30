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
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = $_FILES['foto']['name'];
    $target_file = $target_dir . basename($file_name);
    
    // Sengaja Unrestricted File Upload (tanpa filter ekstensi)
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $conn->query("INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES ('$nama', '$nip', '$jabatan', '$file_name')");
        $message = "Data pegawai berhasil diinput dan dokumen terunggah!";
    } else {
        $message = "Gagal mengunggah file.";
    }
}

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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm px-4">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-network-wired text-primary me-2"></i>SIMPEG BSSN</a>
        <div class="ms-auto d-flex align-items-center">
            <a href="profile.php?id=<?= $_SESSION['user_id']; ?>" class="btn btn-sm btn-outline-light me-2"><i class="fas fa-user-cog"></i> Profil Saya</a>
            <a href="logout.php" class="btn btn-sm btn-danger"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </div>
    </nav>

    <div class="container my-4">
        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= $message; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-primary text-white p-3">
                    <h6 class="text-uppercase small fw-bold">Total Pegawai Terdaftar</h6>
                    <h2 class="fw-bold mb-0"><?= $total_pegawai; ?> Orang</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-secondary text-white p-3">
                    <h6 class="text-uppercase small fw-bold">Session Login</h6>
                    <h2 class="fw-bold mb-0"><?= htmlspecialchars($_SESSION['username']); ?> (<?= $_SESSION['role']; ?>)</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Form Input -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3"><i class="fas fa-user-plus text-primary me-2"></i> Tambah Pegawai</div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">NIP</label>
                                <input type="text" name="nip" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">File / Dokumen (Rentan Upload Vuln)</label>
                                <input type="file" name="foto" class="form-control" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100 fw-bold">Simpan Data</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold py-3"><i class="fas fa-table text-primary me-2"></i> Daftar Pegawai</div>
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
                                            <span class="text-muted small"><?= htmlspecialchars($row['nip']); ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                        <td>
                                            <?php if($row['foto']): ?>
                                                <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> Unduh</a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="?hapus=<?= $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data?')"><i class="fas fa-trash"></i></a>
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