<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

$result = $conn->query("SELECT * FROM pegawai");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kepegawaian - SIMPEG BSSN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </nav>
    <div class="container my-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fas file-alt me-2"></i> Rekapitulasi Laporan Resmi Kepegawaian</span>
                <button onclick="window.print()" class="btn btn-sm btn-light"><i class="fas fa-print"></i> Cetak Laporan</button>
            </div>
            <div class="card-body">
                <p class="text-muted">Berikut adalah rekapitulasi data seluruh pegawai aktif di lingkungan Badan Siber dan Sandi Negara.</p>
                <table class="table table-bordered table-striped">
                    <thead class="table-secondary">
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>File / Lampiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($r['nama']); ?></td>
                            <td><?= htmlspecialchars($r['nip']); ?></td>
                            <td><?= htmlspecialchars($r['jabatan']); ?></td>
                            <td><a href="uploads/<?= $r['foto']; ?>" target="_blank"><?= $r['foto']; ?></a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>