<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG Enterprise - Dashboard</title>
    
    <!-- FIX PATH: Memanggil Bootstrap internal dari server Docker Anda -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar-dark-custom { background-color: #0f172a; }
        .sidebar { background-color: #ffffff; min-height: calc(100vh - 56px); box-shadow: 2px 0 5px rgba(0,0,0,0.02); }
        .card-custom { border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-dark-custom shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold fs-6">🗂️ SIMPEG ENTERPRISE MANAGEMENT</span>
        <div class="d-flex text-white small">
            <span>Sesi: <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-3">
            <ul class="nav flex-column gap-2">
                <li class="nav-item"><a class="nav-link active fw-bold text-dark" href="#">📊 Informasi Utama</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" href="profile.php">👤 Profil Mandiri</a></li>
                <li class="nav-item"><a class="nav-link text-secondary" href="download.php">📂 Konsol Dokumen</a></li>
                <li><hr></li>
                <li class="nav-item"><a class="nav-link text-danger fw-bold" href="logout.php">Keluar Aplikasi</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="card card-custom p-4 bg-white">
                <h2 class="fw-bold text-dark mb-2">Selamat Datang di Portal Pusat</h2>
                <p class="text-muted m-0">Gunakan menu di sebelah kiri untuk mengelola data kepegawaian, mengubah berkas profil, atau mengunduh arsip digital perusahaan.</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
