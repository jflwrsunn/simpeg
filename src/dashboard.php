<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$msg = "";
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// JALUR UPLOAD: Dibuka untuk SEMUA USER (Admin maupun Pegawai Biasa)
if (isset($_POST['upload'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) { 
        mkdir($target_dir, 0755, true); 
    }
    $file_name = basename($_FILES["dokumen"]["name"]);
    $target_file = $target_dir . $file_name;

    // CELAH FILE UPLOAD: Bebas tanpa filter ekstensi di sisi server PHP
    if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
        $msg = "<div class='alert-success'><strong>Sukses!</strong> Dokumen berhasil diarsipkan.<br>Akses berkas: <a href='" . $target_file . "' target='_blank' style='color:#065f46; font-weight:bold;'>" . $file_name . "</a></div>";
    } else {
        $msg = "<div class='alert-danger'><strong>Gagal!</strong> Terjadi kesalahan hak akses folder server.</div>";
    }
}

$query_pegawai = "SELECT * FROM pegawai";
$result_pegawai = $conn->query($query_pegawai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMPEG Enterprise - Dashboard</title>
    <style>
        body { background-color: #f3f4f6; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; color: #1f2937; }
        .navbar { background-color: #0f172a; color: #ffffff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 700; font-size: 1.1rem; }
        .session-info { font-size: 0.9rem; color: #9ca3af; }
        .badge-role { background-color: #ef4444; color: #ffffff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; margin-left: 8px; }
        .badge-guest { background-color: #4b5563; color: #ffffff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; margin-left: 8px; }
        .main-container { display: flex; min-height: calc(100vh - 54px); }
        .sidebar { width: 240px; background-color: #ffffff; border-right: 1px solid #e5e7eb; padding: 20px; box-sizing: border-box; }
        .nav-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .nav-link { display: block; padding: 12px 16px; color: #4b5563; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 0.95rem; }
        .nav-link.active { background-color: #f1f5f9; color: #0f172a; font-weight: 700; }
        .content-area { flex: 1; padding: 30px; box-sizing: border-box; }
        .welcome-card { background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .card-table { background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 25px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .card-header { background: #ffffff; padding: 16px 24px; font-weight: 700; border-bottom: 1px solid #e5e7eb; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem; }
        th { background-color: #f9fafb; padding: 14px 24px; color: #4b5563; border-bottom: 1px solid #e5e7eb; font-weight: 600; }
        td { padding: 14px 24px; border-bottom: 1px solid #e5e7eb; color: #374151; }
        tr:hover { background-color: #f9fafb; }
        .btn-view { background-color: #0f172a; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; }
        .btn-view:hover { background-color: #1e293b; }
        .grid-layout { display: grid; grid-template-columns: 1fr 400px; gap: 25px; }
        .card-upload { background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .upload-header { background: #f1f5f9; padding: 16px 24px; font-weight: 700; color: #1f2937; border-bottom: 1px solid #e5e7eb; }
        .upload-body { padding: 24px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.88rem; color: #4b5563; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { background-color: #0f172a; color: white; border: none; padding: 10px 20px; font-weight: 600; border-radius: 6px; cursor: pointer; margin-top: 15px; font-size: 0.9rem; transition: background 0.2s; width: 100%; }
        .btn-submit:hover { background-color: #1e293b; }
        .card-info { background: #1e293b; color: #9ca3af; padding: 24px; border-radius: 12px; font-size: 0.88rem; line-height: 1.5; }
        .alert-success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        .alert-danger { background-color: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">🗂️ SIMPEG ENTERPRISE MANAGEMENT</div>
    <div class="session-info">
        <span>Identitas: <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong></span>
        <?php if ($user_role === 'admin' || $user_role === 'Super Administrator'): ?>
            <span class="badge-role">Super Administrator</span>
        <?php else: ?>
            <span class="badge-guest">Akses Pegawai</span>
        <?php endif; ?>
    </div>
</nav>

<div class="main-container">
    <div class="sidebar">
        <ul class="nav-list">
            <li><a class="nav-link active" href="#">📊 Informasi Utama</a></li>
            <li><a class="nav-link" href="profile.php">👤 Profil Mandiri</a></li>
            <li><a class="nav-link" href="download.php" style="color:#2563eb; font-weight:bold;">📁 Konsol Dokumen</a></li>
            <li><hr style="border:0; border-top:1px solid #e5e7eb; margin:15px 0;"></li>
            <li><a class="nav-link" href="logout.php" style="color:#dc2626; font-weight:bold; text-decoration:none;">🚪 Keluar Aplikasi</a></li>
        </ul>
    </div>

    <div class="content-area">
        <div class="welcome-card">
            <h2 style="margin:0 0 8px 0;">Selamat Datang di Portal Pusat</h2>
            <p style="margin:0;">Gunakan menu kontrol internal ini untuk mengelola data kepegawaian, mengubah berkas profil, atau mengunduh arsip digital perusahaan.</p>
        </div>

        <!-- Tabel Data Karyawan (Lengkap dengan Tombol Lihat Berkas) -->
        <div class="card-table">
            <div class="card-header">📋 Basis Data Karyawan Aktif (Sinkronisasi Pusat)</div>
            <div style="width: 100%; overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Nomor Induk Pegawai (NIP)</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan Struktural</th>
                            <th>Arsip Dokumen SK</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_pegawai && $result_pegawai->num_rows > 0): ?>
                            <?php while($row = $result_pegawai->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nip']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nama']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                                    <td>
                                        <!-- CELAH IDOR/LFI: Tombol pemicu download berkas asli Anda -->
                                        <a href="download.php?file=<?php echo urlencode($row['foto']); ?>" class="btn-view">Lihat Berkas</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Unggah Dokumen (Dibuka untuk siapa saja yang sukses login) -->
        <div class="grid-layout">
            <div class="card-upload">
                <div class="upload-header">📥 Gerbang Unggah Digitalisasi Berkas (SK / Ijazah)</div>
                <div class="upload-body">
                    <?php echo $msg; ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <label class="form-label">Pilih Salinan File Digital</label>
                        <input class="form-control" type="file" name="dokumen" required>
                        <button type="submit" name="upload" class="btn-submit">Mulai Unggah Berkas &rarr;</button>
                    </form>
                </div>
            </div>
            <div class="card-info">
                <strong style="color: #f59e0b; display: block; margin-bottom: 6px;">🛡️ CATATAN SISTEM INTEGRASI</strong>
                Seluruh dokumen yang diunggah karyawan akan otomatis dipindahkan langsung ke direktori publik `/uploads/` untuk sinkronisasi repositori arsip. Hak akses eksekusi skrip diberikan penuh oleh peladen.
            </div>
        </div>

    </div>
</div>

</body>
</html>