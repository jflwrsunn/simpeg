<?php
session_start();
// Proteksi Sesi: Jika peserta belum login, tendang balik ke halaman login utama
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$msg = "";
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// Proses Jalur Unggah Dokumen (Hanya untuk Admin)
if (isset($_POST['upload']) && $user_role === 'admin') {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    $file_name = basename($_FILES["dokumen"]["name"]);
    $target_file = $target_dir . $file_name;

    // CELAH FILE UPLOAD: Tidak ada pemeriksaan ekstensi di sisi server PHP
    if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
        $msg = "<div class='alert-success'><strong>Sukses!</strong> Dokumen berhasil diarsipkan.<br>Akses berkas: <a href='$target_file' target='_blank' style='color:#065f46; font-weight:bold;'>$target_file</a></div>";
    } else {
        $msg = "<div class='alert-danger'><strong>Gagal!</strong> Terjadi kesalahan hak akses penulisan folder server.</div>";
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
    <title>SIMPEG Enterprise - Dashboard</title>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        /* Navbar Atas */
        .navbar {
            background-color: #0f172a;
            color: #ffffff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar-brand { font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; }
        .session-info { font-size: 0.9rem; color: #9ca3af; }
        .badge-role { background-color: #ef4444; color: #ffffff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; margin-left: 8px; }
        .badge-guest { background-color: #4b5563; color: #ffffff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; margin-left: 8px; }
        
        /* Layout Pembagian Halaman */
        .main-container { display: flex; min-height: calc(100vh - 54px); }
        
        /* Sidebar Menu */
        .sidebar {
            width: 240px;
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 20px;
            box-sizing: border-box;
        }
        .nav-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .nav-link {
            display: block;
            padding: 12px 16px;
            color: #4b5563;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .nav-link:hover { background-color: #f3f4f6; color: #1f2937; }
        .nav-link.active { background-color: #f1f5f9; color: #0f172a; font-weight: 700; }
        .nav-link.text-danger { color: #dc2626; }
        .nav-link.text-danger:hover { background-color: #fef2f2; }
        .sidebar-divider { border: 0; border-top: 1px solid #e5e7eb; margin: 15px 0; }
        
        /* Area Konten Utama */
        .content-area { flex: 1; padding: 30px; box-sizing: border-box; }
        .welcome-card { background: #ffffff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); margin-bottom: 25px; border: 1px solid #e5e7eb; }
        .welcome-card h2 { margin: 0 0 8px 0; font-size: 1.5rem; font-weight: 700; }
        .welcome-card p { margin: 0; color: #6b7280; font-size: 0.95rem; }
        
        /* Tabel Pegawai Premium */
        .card-table { background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); overflow: hidden; margin-bottom: 25px; }
        .card-header { background: #ffffff; padding: 16px 24px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e5e7eb; }
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem; }
        th { background-color: #f9fafb; padding: 14px 24px; font-weight: 600; color: #4b5563; border-bottom: 1px solid #e5e7eb; }
        td { padding: 14px 24px; border-bottom: 1px solid #e5e7eb; color: #374151; }
        tr:hover { background-color: #f9fafb; }
        .badge-status { background-color: #d1fae5; color: #065f46; font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        
        /* Grid Form Unggah */
        .grid-layout { display: grid; grid-template-columns: 1fr 400px; gap: 25px; }
        .card-upload { background: #ffffff; border-radius: 12px; border: 1px solid #fca5a5; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); overflow: hidden; }
        .upload-header { background: #fee2e2; padding: 16px 24px; font-weight: 700; color: #991b1b; }
        .upload-body { padding: 24px; }
        .form-label { display: block; font-size: 0.88rem; font-weight: 600; color: #4b5563; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 0.9rem; }
        .btn-submit { background-color: #2563eb; color: white; border: none; padding: 10px 20px; font-weight: 600; border-radius: 6px; cursor: pointer; font-size: 0.9rem; margin-top: 15px; transition: background 0.2s; }
        .btn-submit:hover { background-color: #1d4ed8; }
        
        .card-info { background: #1e293b; color: #9ca3af; padding: 24px; border-radius: 12px; font-size: 0.88rem; line-height: 1.5; height: fit-content; }
        .text-warning-custom { color: #f59e0b; font-weight: 700; margin-bottom: 6px; display: block; }
        
        /* Notifikasi Alert */
        .alert-success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px; border-radius: 6px; font-size: 0.88rem; margin-bottom: 15px; line-height: 1.4; }
        .alert-danger { background-color: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; padding: 12px; border-radius: 6px; font-size: 0.88rem; margin-bottom: 15px; }
    </style>
</head>
<body>

<!-- Navbar Atas -->
<nav class="navbar">
    <div class="navbar-brand">🗂️ SIMPEG ENTERPRISE MANAGEMENT</div>
    <div class="session-info">
        Identitas: <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong>
        <?php if ($user_role === 'admin'): ?>
            <span class="badge-role">Super Administrator</span>
        <?php else: ?>
            <span class="badge-guest">Akses Terbatas</span>
        <?php endif; ?>
    </div>
</nav>

<div class="main-container">
    <!-- Sidebar Kiri -->
    <div class="sidebar">
        <ul class="nav-list">
            <li><a class="nav-link active" href="#">📊 Informasi Utama</a></li>
            <li><a class="nav-link" href="#" style="color:#9ca3af; cursor:not-allowed;">👤 Profil Mandiri</a></li>
            <?php if ($user_role === 'admin'): ?>
                <li><a class="nav-link" href="#" style="color:#2563eb; font-weight:bold;">📁 Konsol Dokumen</a></li>
            <?php endif; ?>
            <li><hr class="sidebar-divider"></li>
            <li><a class="nav-link text-danger" href="logout.php">🚪 Keluar Aplikasi</a></li>
        </ul>
    </div>

    <!-- Area Konten Utama -->
    <div class="content-area">
        
        <!-- KONDISI 1: JIKA ROLE GUEST -->
        <?php if ($user_role !== 'admin'): ?>
            <div class="alert-danger" style="padding: 20px; border-radius:12px; font-size:1rem;">
                <h4 style="margin: 0 0 8px 0; font-weight:700;">⚠️ Hak Akses Operasional Terbatas</h4>
                Sistem mendeteksi Anda masuk menggunakan kredensial umum (*Guest Account*). Akun ini **tidak diizinkan** oleh arsitektur sistem untuk memuat database karyawan pusat, melihat audit log, ataupun membuka gerbang unggah berkas arsip perusahaan.
            </div>
            
            <div class="welcome-card text-center" style="margin-top: 30px; padding: 40px;">
                <div style="font-size: 3rem; margin-bottom: 10px;">🔒</div>
                <h5 style="margin:0 0 6px 0; font-weight:700; color:#4b5563;">Modul Administrasi Terkunci</h5>
                <p class="text-muted">Silakan keluar sistem dan gunakan otentikasi akun tingkat tinggi (Administrator Infrastruktur) untuk membuka modul eksekusi berkas digital.</p>
            </div>

        <!-- KONDISI 2: JIKA ROLE ADMIN -->
        <?php else: ?>
            <div class="welcome-card">
                <h2>Selamat Datang di Pusat Kendali Utama</h2>
                <p>Gunakan panel kontrol internal ini untuk memutakhirkan basis data korporat, mengelola berkas digitalisasi, dan meninjau audit trail.</p>
            </div>

            <!-- Tabel Data Karyawan -->
            <div class="card-table">
                <div class="card-header">📋 Basis Data Karyawan Aktif (Sinkronisasi Pusat)</div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nomor Induk Pegawai (NIP)</th>
                                <th>Nama Lengkap</th>
                                <th>Jabatan Struktural</th>
                                <th>Status Berkas</th>
                            </tr>
                        </thead>
                        <tbody>
