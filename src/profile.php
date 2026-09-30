<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'config.php';

// Rentan IDOR: mengambil data berdasarkan parameter GET id tanpa validasi kepemilikan session
$profile_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $profile_id";
$result = $conn->query($query);
$profile = $result ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna - SIMPEG BSSN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </nav>
    <div class="container my-5" style="max-width: 600px;">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white fw-bold py-3">
                <i class="fas fa-user-circle me-2"></i> Profil Pengguna (IDOR Training Target)
            </div>
            <div class="card-body">
                <?php if($profile): ?>
                    <table class="table table-bordered">
                        <tr>
                            <th class="w-25 bg-light">ID User</th>
                            <td><?= $profile['id']; ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Username</th>
                            <td><?= htmlspecialchars($profile['username']); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Password (Plain/Hash)</th>
                            <td><code><?= htmlspecialchars($profile['password']); ?></code></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Role Akses</th>
                            <td><span class="badge bg-success"><?= htmlspecialchars($profile['role']); ?></span></td>
                        </tr>
                    </table>
                    <div class="alert alert-warning small mt-3">
                        <i class="fas fa-info-circle me-1"></i> Ubah parameter <code>?id=...</code> di URL untuk menguji kerentanan IDOR (BOLA) antar akun pengguna!
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">Data pengguna tidak ditemukan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>