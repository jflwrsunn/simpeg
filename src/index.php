<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-SIMPEG - Sistem Informasi Manajemen Kepegawaian</title>
    <!-- Bootstrap 5 & Google Fonts untuk Estetika Modern -->
    <link href="https://jsdelivr.net" rel="stylesheet">
    <link href="https://googleapis.com" rel="stylesheet">
    <!-- FontAwesome untuk Ikon Premium -->
    <link rel="stylesheet" href="https://cloudflare.com">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #fdfeff; 
            color: #1e293b;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .hero-section {
            background: radial-gradient(circle at 10% 20%, rgba(216, 241, 255, 0.46) 0.1%, rgba(253, 243, 255, 0.28) 90.1%);
            padding: 120px 0 100px 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -20%; right: -10%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(13, 71, 161, 0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }
        .btn-premium {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(13, 71, 161, 0.2);
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(13, 71, 161, 0.3);
            color: white;
        }
        .btn-outline-custom {
            border: 2px solid #0d47a1;
            color: #0d47a1;
            padding: 10px 28px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-outline-custom:hover {
            background-color: #0d47a1;
            color: white;
        }
        .card-aesthetic {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 20px;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        }
        .card-aesthetic:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            border-color: rgba(13, 71, 161, 0.2);
        }
        .icon-box {
            width: 60px; height: 60px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 24px;
        }
        .icon-blue { background: #eff6ff; color: #1d4ed8; }
        .icon-green { background: #f0fdf4; color: #15803d; }
        .icon-purple { background: #faf5ff; color: #7e22ce; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="#">
            <i class="fa-solid fa-layer-group text-primary me-2"></i> E-SIMPEG
        </a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-outline-custom btn-sm">Portal Masuk</a>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <div class="row align-items-center py-5">
            <div class="col-lg-6 text-center text-lg-start">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-3 small">Enterprise Kepegawaian v4.2</span>
                <h1 class="display-5 fw-bold text-dark tracking-tight mb-3">Sistem Tata Kelola<br><span class="text-primary">Kepegawaian Digital</span></h1>
                <p class="text-muted fs-6 mb-4">Integrasi ekosistem data aparatur yang transparan, cerdas, akuntabel, dan berbasis komputasi awan yang responsif.</p>
                <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                    <a href="login.php" class="btn btn-premium">Mulai Akses Layanan</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center position-relative">
                <!-- Ornamen Grafis Pengganti Gambar Rusak -->
                <div class="p-5 bg-white shadow-lg rounded-4 border border-light mx-auto" style="max-width: 450px; transform: rotate(2deg);">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3"><i class="fa-solid fa-circle-check text-success"></i></div>
                        <div class="text-start"><h6 class="m-0 fw-bold">Sinkronisasi Pusat</h6><small class="text-muted">Database Aktif Terhubung</small></div>
                    </div>
                    <div class="progress mb-3" style="height: 6px;"><div class="progress-bar bg-primary" style="width: 85%"></div></div>
                    <div class="d-flex justify-content-between small text-muted"><span>Enkripsi TLS 1.3</span><span>100% Secure</span></div>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="container my-5 py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-aesthetic p-4 h-100">
                <div class="icon-box icon-blue"><i class="fa-solid fa-chart-pie"></i></div>
                <h5 class="fw-bold mb-2">Analitik Real-Time</h5>
                <p class="text-muted small m-0">Validasi dan pemetaan jabatan ASN secara instan langsung dari pusat data administrasi nasional.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-aesthetic p-4 h-100">
                <div class="icon-box icon-green"><i class="fa-solid fa-folder-open"></i></div>
                <h5 class="fw-bold mb-2">Digitalisasi Berkas</h5>
                <p class="text-muted small m-0">Pengarsipan mandiri dokumen resmi berkas negara (SK Pengangkatan & Ijazah).</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-aesthetic p-4 h-100">
                <div class="icon-box icon-purple"><i class="fa-solid fa-shield-halved"></i></div>
                <h5 class="fw-bold mb-2">Infrastruktur Proteksi</h5>
                <p class="text-muted small m-0">Sistem keamanan berlapis yang dipantau berkala sesuai standar operasional siber korporat.</p>
            </div>
        </div>
    </div>
</main>

<footer class="text-center py-4 text-muted small border-top border-light mt-5">
    &copy; 2026 PT Telekomunikasi Media Nusantara. All Rights Reserved.
</footer>

<script src="https://jsdelivr.net"></script>
</body>
</html>
