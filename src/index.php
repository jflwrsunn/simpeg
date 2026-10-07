<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG - Sistem Informasi Manajemen Kepegawaian Negara</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .hero-section {
            background: linear-gradient(rgba(13, 71, 161, 0.85), rgba(21, 101, 192, 0.85)), url('https://unsplash.com') no-repeat center center/cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .gov-logo { width: 90px; height: auto; margin-bottom: 20px; }
        .feature-card { border: none; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s; }
        .feature-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<!-- Navbar Atas -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
            <img src="https://wikimedia.org" alt="Garuda" width="30" class="me-2">
            E-SIMPEG PORTAL
        </a>
        <div class="ms-auto">
            <!-- PENTING: Tombol ini mengarah ke halaman login.php Anda -->
            <a href="login.php" class="btn btn-outline-light fw-bold px-4">Portal Login</a>
        </div>
    </div>
</nav>

<!-- Hero Banner -->
<header class="hero-section shadow-sm">
    <div class="container">
        <img src="https://wikimedia.org" alt="Logo Garuda" class="gov-logo">
        <h1 class="display-5 fw-bold m-0">Sistem Informasi Manajemen Kepegawaian</h1>
        <p class="lead mt-2 text-white-50">Integrasi Data Aparatur Sipil Negara yang Transparan, Akuntabel, dan Responsif</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-warning btn-lg fw-bold px-5 text-dark shadow">Masuk Layanan ASN</a>
        </div>
    </div>
</header>

<!-- Informasi Tambahan Visual -->
<main class="container my-5">
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="card feature-card p-4">
                <div class="fs-1 text-primary mb-2">📊</div>
                <h5 class="fw-bold">Data Real-Time</h5>
                <p class="text-muted small m-0">Sinkronisasi data pangkat dan jabatan secara langsung dengan Badan Kepegawaian Pusat.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card p-4">
                <div class="fs-1 text-success mb-2">📁</div>
                <h5 class="fw-bold">Digitalisasi SK</h5>
                <p class="text-muted small m-0">Pengarsipan mandiri dokumen resmi berkas negara (SK Pengangkatan & Ijazah).</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card p-4">
                <div class="fs-1 text-danger mb-2">🔒</div>
                <h5 class="fw-bold">Keamanan Terjamin</h5>
                <p class="text-muted small m-0">Sistem dilindungi enkripsi berlapis dan audit keamanan berkala sesuai standar siber.</p>
            </div>
        </div>
    </div>
</main>

<footer class="bg-dark text-white-50 text-center py-3 border-top border-secondary small">
    &copy; 2026 Badan Kepegawaian dan Pengembangan Sumber Daya Manusia. Hak Cipta Dilindungi Undang-Undang.
</footer>

<script src="https://jsdelivr.net"></script>
</body>
</html>