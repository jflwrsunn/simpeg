<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$msg = "";
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// Proses Unggah Berkas (Dibuka untuk semua user yang berhasil masuk)
if (isset($_POST['upload'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    $file_name = basename($_FILES["dokumen"]["name"]);
    $target_file = $target_dir . $file_name;

    // CELAH FILE UPLOAD: Sisi server tidak memeriksa ekstensi sama sekali
    if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
        $msg = "<div class='alert alert-success mt-2' style='font-size:0.85rem;'><strong>Sukses!</strong> Berkas berhasil diarsipkan ke server.<br>Akses: <a href='$target_file' target='_blank' class='fw-bold'>$file_name</a></div>";
    } else {
        $msg = "<div class='alert alert-danger mt-2' style='font-size:0.85rem;'><strong>Gagal!</strong> Terjadi kesalahan hak akses folder server.</div>";
    }
}

// Menarik data pegawai untuk tabel administrasi
$query_pegawai = "SELECT * FROM pegawai";
$result_pegawai = $conn->query($query_pegawai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG Enterprise Corp - Dashboard</title>
    
    <!-- Bootstrap via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .navbar-custom {
            background-color: #0f172a;
            color: white;
            padding: 15px 24px;
        }
        .main-container {
            max-width: 1300px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .announcement-banner {
            background-color: #1e293b;
            color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            height: 100%;
        }
        .content-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 28px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            height: 100%;
        }
        .card-title-custom {
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
        }
        .form-control-custom {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
        }
        .form-control-custom:focus {
            border-color: #1e293b;
            box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.1);
        }
        .btn-dark-custom {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            transition: background 0.2s;
        }
        .btn-dark-custom:hover { background-color: #1d4ed8; color: white; }
        .locked-box {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        .locked-icon { font-size: 2.5rem; margin-bottom: 15px; }
        table { font-size: 0.9rem; }
        th { background-color: #f8fafc !important; color: #64748b; font-weight: 600; padding: 12px 16px !important; }
        td { padding: 14px 16px !important; vertical-align: middle; }
        .badge-status { background-color: #d1fae5; color: #065f46; font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
    </style>
</head>
<body>

<!-- Navbar Atas -->
<nav class="navbar navbar-custom shadow-sm navbar-expand">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <span class="fw-bold d-flex align-items-center gap-2 m-0 fs-5 text-white">
            🗂️ SIMPEG Enterprise Corp
        </span>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-white-50">Identitas: <strong class="text-white"><?php echo htmlspecialchars($_SESSION['admin']); ?></strong></span>
            <?php if ($user_role === 'admin' || $user_role === 'Super Administrator' || $user_role === 'admin_corporate'): ?>
                <span class="badge bg-danger rounded-pill px-3 py-1.5" style="font-size:0.75rem; font-weight:bold; color: white;">Super Administrator</span>
            <?php else: ?>
                <span class="badge bg-secondary rounded-pill px-3 py-1.5" style="font-size:0.75rem; font-weight:bold; color: white;">Akses Karyawan</span>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-danger btn-sm px-3 fw-bold rounded-3 d-flex align-items-center gap-1 text-white" style="text-decoration: none;"> Keluar</a>
        </div>
    </div>
</nav>

<div class="main-container container-fluid">
    
    <!-- Banner Pengumuman Atas -->
    <div class="announcement-banner d-flex align-items-start gap-3 mb-4">
        <div class="fs-4 text-white">📢</div>
        <div>
            <h6 class="fw-bold m-0 mb-1 text-white">Pengumuman Internal Korporat</h6>
            <p class="small m-0 text-white-50">Sistem menggunakan enkripsi berbasis database. Perubahan hak akses tanpa otorisasi akan masuk ke Audit Trail.</p>
        </div>
    </div>

    <!-- Dua Kotak Ringkasan Statistik -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="fs-3 bg-light rounded-3 p-3">👥</div>
                <div>
                    <small class="text-muted d-block fw-semibold mb-1">Total Pegawai Terdaftar</small>
                    <h3 class="fw-bold m-0 text-dark">5 <span class="fs-6 fw-normal text-muted">Orang</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="fs-3 bg-light rounded-3 p-3">👤</div>
                <div>
                    <small class="text-muted d-block fw-semibold mb-1">Sesi Terverifikasi</small>
                    <h4 class="fw-bold m-0 text-dark"><?php echo htmlspecialchars($_SESSION['admin']); ?> 
                        <?php if ($user_role === 'admin' || $user_role === 'Super Administrator' || $user_role === 'admin_corporate'): ?>
                            <span class="text-primary fs-6 fw-normal ms-1">Super Administrator</span>
                        <?php else: ?>
                            <span class="text-secondary fs-6 fw-normal ms-1">Akses Terbatas</span>
                        <?php endif; ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Utama: Input Berkas vs Daftar Karyawan -->
    <div class="row g-4">
        
        <!-- SISI KIRI: Form Input Data & Dokumen -->
        <div class="col-lg-4">
            <div class="content-card">
                <div class="card-title-custom text-primary">🔵 Input Data & Dokumen</div>
                
                <?php echo $msg; ?>
                
                <form method="POST" action="" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                    <div class="w-100">
                        <label class="form-label-custom">Nama Lengkap & Gelar</label>
                        <input type="text" class="form-control form-control-custom w-100" placeholder="Contoh: Budi Santoso, S.Kom.">
                    </div>
                    <div class="w-100">
                        <label class="form-label-custom">NIP / NIK Karyawan</label>
                        <input type="text" class="form-control form-control-custom w-100" placeholder="Contoh: 198001012005011001">
                    </div>
                    <div class="w-100">
                        <label class="form-label-custom">Jabatan / Divisi</label>
                        <input type="text" class="form-control form-control-custom w-100" placeholder="Senior Infrastructure Analyst">
                    </div>
                    <div class="w-100">
                        <label class="form-label-custom">Lampiran Berkas</label>
                        <input class="form-control form-control-custom w-100" type="file" name="dokumen" required>
                    </div>
                    <div class="mt-2 w-100">
                        <button type="submit" name="upload" class="btn-dark-custom">Mulai Unggah Berkas &rarr;</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SISI KANAN: Daftar Karyawan & Berkas -->
        <div class="col-lg-8">
            <div class="content-card position-relative">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title-custom text-dark m-0">🌐 Daftar Karyawan & Arsip File</div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-1 py-1 px-2 fw-bold" style="font-size:0.75rem;">Khusus Admin</span>
                </div>