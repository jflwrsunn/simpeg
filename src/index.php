<?php
// Koneksi Database
$host = 'db';
$user = 'simpeg_user';       // Ubah dari 'root' menjadi 'simpeg_user'
$pass = 'simpeg_password';   // Ubah menjadi 'simpeg_password'
$dbname = 'simpeg_db';       // Nama database-nya 'simpeg_db'

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    // Buat database otomatis jika belum ada
    $conn = new mysqli($host, $user, $pass);
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    $conn->query("CREATE TABLE IF NOT EXISTS pegawai (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100),
        jabatan VARCHAR(100),
        foto VARCHAR(255)
    )");
}

$message = "";
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    
    // Fitur Upload File (Sengaja dibuat rentan tanpa filter ekstensi ketat untuk bahan pentest)
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = $_FILES['foto']['name'];
    $target_file = $target_dir . basename($file_name);
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO pegawai (nama, jabatan, foto) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $jabatan, $file_name);
        $stmt->execute();
        $message = "Data pegawai berhasil ditambahkan!";
    } else {
        $message = "Gagal mengunggah foto.";
    }
}

$result = $conn->query("SELECT * FROM pegawai");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMPEG - Sistem Informasi Kepegawaian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">SIMPEG BSSN (Training Lab)</a>
        </div>
    </nav>

    <div class="container my-5">
        <?php if($message): ?>
            <div class="alert alert-info"><?= $message; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Input -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Tambah Data Pegawai</div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto / Dokumen Pendukung</label>
                                <input type="file" name="foto" class="form-control" required>
                                <div class="form-text text-muted">Format bebas (cocok untuk modul upload vuln).</div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary w-100">Simpan Data</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">Daftar Pegawai Terdaftar</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Foto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nama']); ?></td>
                                        <td><?= htmlspecialchars($row['jabatan']); ?></td>
                                        <td>
                                            <?php if($row['foto']): ?>
                                                <a href="uploads/<?= $row['foto']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat File</a>
                                            <?php endif; ?>
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
</body>
</html>