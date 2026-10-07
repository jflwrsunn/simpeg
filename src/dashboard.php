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
        $msg = "<div class='alert alert-success'><strong>Sukses!</strong> Berkas berhasil diarsipkan ke server.<br>Akses: <a href='$target_file' target='_blank' style='color:#065f46; font-weight:bold;'>$file_name</a></div>";
    } else {
        $msg = "<div class='alert alert-danger'><strong>Gagal!</strong> Terjadi kesalahan hak akses folder server.</div>";
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
    <style>
        /* =========================================================================
           KODE DESAIN INTERNAL MANDIRI - 100% ANTI-BERANTAKAN & OFFLINE COMPATIBLE
           ========================================================================= */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Navbar Atas */
        .navbar-custom {
            background-color: #0f172a;
            color: white;
            padding: 15px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .session-info {
            font-size: 0.9rem;
            color: #94a3b8;
        }
        .session-info strong { color: white; }
        .badge-admin {
            background-color: #ef4444;
            color: white;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-employee {
            background-color: #64748b;
            color: white;
            padding: 5px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .btn-exit {
            background-color: #dc2626;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            transition: background 0.2s;
        }
        .btn-exit:hover { background-color: #b91c1c; }

        /* Container & Banner */
        .main-container {
            max-width: 1250px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .announcement-banner {
            background-color: #1e293b;
            color: white;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: start;
            gap: 15px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .banner-icon { font-size: 1.5rem; background: rgba(255,255,255,0.1); padding: 5px 10px; border-radius: 8px; }
        .banner-title { font-weight: 700; margin: 0 0 4px 0; font-size: 0.95rem; }
        .banner-text { font-size: 0.85rem; color: #94a3b8; margin: 0; }

        /* Statistik Dua Kolom */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .stat-icon { font-size: 2rem; background: #f1f5f9; padding: 12px; border-radius: 10px; }
        .stat-label { font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 4px; display: block; }
        .stat-value { font-size: 1.4rem; font-weight: 700; margin: 0; }
        .stat-value-sub { font-size: 0.9rem; font-weight: normal; color: #64748b; }
        .text-primary-custom { color: #2563eb; font-size: 0.95rem; font-weight: 600; }
        .text-secondary-custom { color: #64748b; font-size: 0.95rem; font-weight: 600; }

        /* Grid Utama Layout Dashboard */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 25px;
        }
        .content-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 28px;
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
        }
        .card-title-custom {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .title-blue { color: #2563eb; }
        .title-dark { color: #0f172a; }

        /* Struktur Formulir Sisi Kiri */
        .form-group { margin-bottom: 15px; }
        .form-label-custom {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 6px;
        }
        .form-control-custom {
            width: 100%;
            padding: 11px 14px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 0.9rem;
            box-sizing: border-box;
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            outline: none;
            border-color: #1e293b;
            box-shadow: 0 0 0 3px rgba(30, 41, 59, 0.1);
        }
        .btn-dark-custom {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }
        .btn-dark-custom:hover { background-color: #1d4ed8; }

        /* Bagian Kunci & Tabel Sisi Kanan */
        .header-right-side {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .badge-restriction {
            background-color: rgba(220, 38, 38, 0.1);
            color: #dc2626;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
        }
        .locked-box {
            text-align: center;
            padding: 80px 20px;
            color: #64748b;
        }
        .locked-icon { font-size: 3rem; margin-bottom: 15px; }
        .locked-title { font-weight: 700; color: #0f172a; margin: 0 0 6px 0; font-size: 1.05rem; }
        .locked-text { font-size: 0.88rem; color: #64748b; margin: 0 auto; max-width: 440px; line-height: 1.5; }

        /* Tabel Data */
        .table-responsive { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; border: 1px solid #f1f5f9; border-radius: 8px; overflow: hidden; }
        th { background-color: #f8fafc; color: #64748b; font-weight: 600; padding: 14px 16px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        tr:hover { background-color: #f8fafc; }
        .badge-status { background-color: #d1fae5; color: #065f46; font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; }

        /* Notifikasi Peserta Alert */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 15px; line-height: 1.4; border: 1px solid transparent; }
        .alert-success { background-color: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .alert-danger { background-color: #fef2f2; color: #991b1b; border-color: #fca5a5; }
    </style>
</head>
<body>

<!-- Navbar Atas -->
<header class="navbar-custom">
    <div class="navbar-brand">
        <span>🗂️</span> SIMPEG Enterprise Corp
    </div>
    <div class="navbar-right">
        <div class="session-info">
            Identitas: <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong>
            <?php if ($user_role === 'admin' || $user_role === 'Super Administrator' || $user_role === 'admin_corporate'): ?>
                <span class="badge-admin">Super Administrator</span>
            <?php else: ?>
                <span class="badge-employee">Akses Karyawan</span>
            <?php endif; ?>
        </div>
        <a href="logout.php" class="btn-exit">🚪 Keluar</a>
    </div>
</header>

<div class="main-container">
    
    <!-- Banner Pengumuman Atas -->
    <section class="announcement-banner">
        <div class="banner-icon">📢</div>
        <div>
            <h6 class="banner-title">Pengumuman Internal Korporat</h6>
