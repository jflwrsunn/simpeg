<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pusat Bantuan - SIMPEG BSSN</title>
    <!-- Favicon Resmi BSSN Dummy -->
    <link rel="icon" href="https://upload.wikimedia.org/wikipedia/commons/9/9f/Logo_Badan_Siber_dan_Sandi_Negara.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    </nav>
    <div class="container my-5 flex-grow-1" style="max-width: 700px;">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h3 class="fw-bold text-dark mb-3"><i class="fas fa-headset text-primary me-2"></i> Pusat Layanan Helpdesk BSSN</h3>
                <p class="text-muted">Butuh bantuan teknis terkait kendala akun, sinkronisasi data NIP, atau pelaporan insiden keamanan sistem? Silakan hubungi unit layanan di bawah ini:</p>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Email:</strong> helpdesk@bssn.go.id</li>
                    <li class="list-group-item"><strong>Ext. Internal:</strong> 404 (Pusat Data & Informasi)</li>
                    <li class="list-group-item"><strong>Jam Operasional:</strong> Senin - Jumat (08.00 - 16.00 WIB)</li>
                </ul>
                <div class="alert alert-warning small border-0 shadow-sm">
                    <i class="fas fa-exclamation-triangle me-1"></i> Jangan pernah memberikan kata sandi atau token akses Anda kepada siapa pun, termasuk administrator sistem.
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-dark text-white text-center py-3 mt-auto border-top border-secondary">
        <div class="container small">
            <p class="mb-0">© 2026 Badan Siber dan Sandi Negara (BSSN)</p>
        </div>
    </footer>
</body>
</html>