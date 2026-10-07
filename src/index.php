<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG - Sistem Informasi Manajemen Kepegawaian Negara</title>
    <!-- MENGGUNAKAN BOOTSTRAP LOKAL AGAR STRUKTUR WEB TETAP MEWAH DAN RAPI -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .hero-section {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .gov-logo-box { 
            font-size: 4rem; 
            margin-bottom: 15px; 
            filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.15));
        }
        .feature-card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 6px 20px rgba(0,0,0,0.04); 
            transition: all 0.3s; 
            background: #ffffff;
        }
        .feature-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
            <span class="me-2">🇮🇩</span> E-SIMPEG PORTAL
        </a>
        <div class="ms-auto">
            <!-- Menghubungkan tombol langsung ke gerbang login.php -->
            <a href="login.php" class="btn btn-warning fw-bold px-4 shadow-sm text-dark">Portal Login &rarr;</a>
        </div>
    </div>
</nav>

<!-- Hero Banner (Bagian Utama) -->
<header class="hero-section shadow">
    <div class="container">
        <div class="gov-logo-box">🏛️</div>
        <h1 class="display-6 fw-bold m-0">Sistem Informasi Manajemen Kepegawaian</h1>
        <p class="lead mt-2 text-white-50 fs-6">Integrasi Data Aparatur Sipil Negara yang Transparan, Akuntabel, dan Responsif</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-light btn-lg fw-bold px-5 text-primary shadow-sm fs-6">Masuk Layanan ASN</a>
        </div>
    </div>
</header>

<!-- Panel Informasi Tambahan (OWASP Lab Target) -->
<main class="container my-5">
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="card feature-card p-4">
                <div class="fs-1 text-primary mb-2">📊</div>
                <h5 class="fw-bold text-dark">Data Real-Time</h5>
                <p class="text-muted small m-0">Sinkronisasi data pangkat dan jabatan secara langsung dengan Badan Kepegawaian Pusat.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card p-4">
                <div class="fs-1 text-success mb-2">📁</div>
                <h5 class="fw-bold text-dark">Digitalisasi SK</h5>
                <p class="text-muted small m-0">Pengarsipan mandiri dokumen resmi berkas negara (SK Pengangkatan & Ijazah).</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card feature-card p-4">
                <div class="fs-1 text-danger mb-2">🔒</div>
                <h5 class="fw-bold text-dark">Keamanan Terjamin</h5>
                <p class="text-muted small m-0">Sistem dilindungi enkripsi berlapis dan audit keamanan berkala sesuai standar siber.</p>
            </div>
        </div>
    </div>
</main>

<footer class="bg-dark text-white-50 text-center py-3 border-top border-secondary small mt-5">
    &copy; 2026 Badan Kepegawaian dan Pengembangan Sumber Daya Manusia. Hak Cipta Dilindungi Undang-Undang.
</footer>

</body>
</html>