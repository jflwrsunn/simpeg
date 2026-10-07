<?php
session_start();
if (!isset(\$_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

\$msg = "";
\$user_role = isset(\(_SESSION['role']) ?\)_SESSION['role'] : 'guest';

if (isset(\$_POST['upload']) && \(user_role === 'admin') {\)target_dir = "uploads/";
    if (!file_exists(\(target_dir)) { mkdir(\)target_dir, 0755, true); }
    \(file_name = basename(\)_FILES["dokumen"]["name"]);
    \$target_file = \(target_dir .\)file_name;

    if (move_uploaded_file(\$_FILES["dokumen"]["tmp_name"], \(target_file)) {\)msg = "<div class='alert-success'><strong>Sukses!</strong> Dokumen berhasil diarsipkan.<br>Akses berkas: <a href='\(target_file' target='_blank' style='color:#065f46; font-weight:bold;'>\)target_file</a></div>";
    } else {
        \$msg = "<div class='alert-danger'><strong>Gagal!</strong> Terjadi kesalahan hak akses folder server.</div>";
    }
}

\$query_pegawai = "SELECT * FROM pegawai";
\$result_pegawai = conn->query(query_pegawai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMPEG Enterprise - Dashboard</title>
    <style>
        body { background-color: #f3f4f6; font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; color: #1f2937; }
        .navbar { background-color: #0f172a; color: #ffffff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { font-weight: 700; font-size: 1.1rem; }
        .badge-role { background-color: #ef4444; color: #ffffff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; margin-left: 8px; }
        .badge-guest { background-color: #4b5563; color: #ffffff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; margin-left: 8px; }
        .main-container { display: flex; min-height: calc(100vh - 54px); }
        .sidebar { width: 240px; background-color: #ffffff; border-right: 1px solid #e5e7eb; padding: 20px; box-sizing: border-box; }
        .nav-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
        .nav-link { display: block; padding: 12px 16px; color: #4b5563; text-decoration: none; border-radius: 6px; font-weight: 500; font-size: 0.95rem; }
        .nav-link.active { background-color: #f1f5f9; color: #0f172a; font-weight: 700; }
        .content-area { flex: 1; padding: 30px; box-sizing: border-box; }
        .welcome-card { background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 25px; }
        .card-table { background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 25px; }
        .card-header { background: #ffffff; padding: 16px 24px; font-weight: 700; border-bottom: 1px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem; }
        th { background-color: #f9fafb; padding: 14px 24px; color: #4b5563; border-bottom: 1px solid #e5e7eb; }
        td { padding: 14px 24px; border-bottom: 1px solid #e5e7eb; }
        .badge-status { background-color: #d1fae5; color: #065f46; font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; font-weight: 600; }
        .grid-layout { display: grid; grid-template-columns: 1fr 400px; gap: 25px; }
        .card-upload { background: #ffffff; border-radius: 12px; border: 1px solid #fca5a5; overflow: hidden; }
        .upload-header { background: #fee2e2; padding: 16px 24px; font-weight: 700; color: #991b1b; }
        .upload-body { padding: 24px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
        .btn-submit { background-color: #2563eb; color: white; border: none; padding: 10px 20px; font-weight: 600; border-radius: 6px; cursor: pointer; margin-top: 15px; }
        .card-info { background: #1e293b; color: #9ca3af; padding: 24px; border-radius: 12px; font-size: 0.88rem; }
        .alert-success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
        .alert-danger { background-color: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; padding: 12px; border-radius: 6px; margin-bottom: 15px; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">🗂️ SIMPEG ENTERPRISE MANAGEMENT</div>
    <div>
        <span>Identitas: <strong><?php echo htmlspecialchars(\$_SESSION['admin']); ?></strong></span>
        <?php if (\$user_role === 'admin'): ?>
            <span class="badge-role">Super Administrator</span>
        <?php else: ?>
            <span class="badge-guest">Akses Terbatas</span>
        <?php endif; ?>
    </div>
</nav>

<div class="main-container">
    <div class="sidebar">
        <ul class="nav-list">
            <li><a class="nav-link active" href="#">📊 Informasi Utama</a></li>
            <?php if (\$user_role === 'admin'): ?>
                <li><a class="nav-link" href="#" style="color:#2563eb; font-weight:bold;">📁 Konsol Dokumen</a></li>
            <?php endif; ?>
            <li><hr style="border:0; border-top:1px solid #e5e7eb; margin:15px 0;"></li>
            <li><a class="nav-link" href="logout.php" style="color:#dc2626; font-weight:bold; text-decoration:none;">🚪 Keluar Aplikasi</a></li>
        </ul>
    </div>

    <div class="content-area">
        <?php if (\$user_role !== 'admin'): ?>
            <div class="alert-danger" style="padding: 20px; border-radius:12px;">
                <h4 style="margin: 0 0 8px 0; font-weight:700;">⚠️ Hak Akses Operasional Terbatas</h4>
                Sistem mendeteksi Anda masuk menggunakan kredensial umum (*Guest Account*). Anda tidak diizinkan melihat database operasional.
            </div>
        <?php else: ?>
            <div class="welcome-card">
                <h2 style="margin:0 0 8px 0;">Selamat Datang di Pusat Kendali Utama</h2>
                <p style="margin:0;">Panel administrator internal untuk pengelolaan basis data korporat dan berkas digitalisasi.</p>
            </div>

            <div class="card-table">
                <div class="card-header">📋 Basis Data Karyawan Aktif (Sinkronisasi Pusat)</div>
                <div style="width: 100%; overflow-x: auto;">
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
                            <?php if (\(result_pegawai &&\)result_pegawai->num_rows > 0): ?>
                                <?php while(row = result_pegawai->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(\$row['nip']); ?></td>
                                        <td><strong><?php echo htmlspecialchars(\$row['nama']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(\$row['jabatan']); ?></td>
                                        <td><span class="badge-status">✓ Terverifikasi</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid-layout">
                <div class="card-upload">
                    <div class="upload-header">📤 Gerbang Unggah Digitalisasi Dokumen Negara (SK / Ijazah)</div>
                    <div class="upload-body">
                        <?php echo \$msg; ?>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <label class="form-label" style="display:block; margin-bottom:8px; font-weight:600; font-size:0.88rem;">Pilih Salinan File Digital</label>
                            <input class="form-control" type="file" name="dokumen" tyranny="none" required>
                            <button type="submit" name="upload" class="btn-submit">Mulai Unggah Berkas &rarr;</button>
                        </form>
                    </div>
                </div>
                <div class="card-info">
                    <strong style="color: #f59e0b; display: block; margin-bottom: 6px;">🛡️ CATATAN SISTEM INTEGRASI</strong>
                    Seluruh dokumen yang diunggah administrator akan otomatis dipindahkan langsung ke direktori publik `/uploads/` untuk sinkronisasi repositori. Hak akses eksekusi diberikan penuh oleh server.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
