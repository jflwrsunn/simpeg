<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';
$msg = "";
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// Proses Upload (Hanya diproses server jika role adalah admin)
if (isset($_POST['upload']) && $user_role === 'admin') {
    $target_dir = "uploads/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_name = basename($_FILES["dokumen"]["name"]);
    $target_file = $target_dir . $file_name;

    // CELAH FILE UPLOAD: Tidak ada validasi tipe file di sisi server PHP
    if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
        $msg = "<div class='alert alert-success small shadow-sm'><strong>Sukses!</strong> Dokumen berhasil diarsipkan.<br>Akses berkas: <a href='$target_file' target='_blank' class='fw-bold text-decoration-none'>$target_file</a></div>";
    } else {
        $msg = "<div class='alert alert-danger small shadow-sm'><strong>Gagal!</strong> Terjadi kesalahan penulisan direktori server.</div>";
    }
}

$query_pegawai = "SELECT * FROM pegawai";
$result_pegawai = mysqli_query($conn, $query_pegawai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Layanan - E-SIMPEG Portal</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .navbar-gov { background-color: #0d47a1; }
        .sidebar { background-color: #ffffff; min-height: calc(100vh - 56px); box-shadow: 2px 0 10px rgba(0,0,0,0.05); }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
        .nav-link.active { background-color: #e3f2fd; color: #0d47a1 !important; font-weight: bold; border-radius: 6px; }
        .role-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; font-weight: bold; }
    </style>
    
    <?php if ($user_role === 'admin'): ?>
    <!-- JUBAH PROTEKSI JAVASCRIPT: Validasi ekstensi lemah di sisi client -->
    <script>
    function validateForm() {
        var fileInput = document.getElementById('dokumen');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.pdf|\.docx|\.jpg|\.png)$/i;
        if(!allowedExtensions.exec(filePath)){
            alert('Akses Ditolak: Format berkas ditolak oleh sistem keamanan browser!');
            fileInput.value = '';
            return false;
        }
        return true;
    }
    </script>
    <?php endif; ?>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-gov shadow-sm sticky-top">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold d-flex align-items-center fs-6">
            <img src="https://wikimedia.org" alt="Garuda" width="28" class="me-2">
            E-SIMPEG INTERNAL MANAGEMENT
        </span>
        <div class="d-flex align-items-center text-white small">
            <span class="me-2 d-none d-sm-inline">Pengguna: <strong><?php echo htmlspecialchars($_SESSION['admin']); ?></strong></span>
            <?php if ($user_role === 'admin'): ?>
                <span class="badge bg-danger role-badge">Super Administrator</span>
            <?php else: ?>
                <span class="badge bg-secondary role-badge">Akses Terbatas (Guest)</span>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar p-3 d-none d-md-block">
            <ul class="nav flex-column gap-2">
                <li class="nav-item"><a class="nav-link text-dark active" href="#">Informasi Utama</a></li>
                <li class="nav-item"><a class="nav-link text-muted disabled" href="#">Profil Mandiri</a></li>
                <?php if ($user_role === 'admin'): ?>
                <li class="nav-item"><a class="nav-link text-primary fw-bold" href="#">📁 Konsol Dokumen</a></li>
                <?php endif; ?>
                <li><hr class="text-black-50"></li>
                <li class="nav-item"><a class="nav-link text-danger fw-bold" href="logout.php">Keluar Aplikasi</a></li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 p-4">
            
            <!-- TAMPILAN JIKAN USER BUKAN ADMIN (ROLE GUEST) -->
            <?php if ($user_role !== 'admin'): ?>
                <div class="alert alert-warning card-custom p-4 mb-4" role="alert">
                    <h4 class="alert-heading fw-bold text-dark">⚠️ Hak Akses Akun Terbatas</h4>
                    <p class="m-0 text-secondary">Sistem mendeteksi Anda masuk menggunakan kredensial umum / *Guest Account*. Akun ini **tidak memiliki izin operasional** untuk mengelola data aparatur sipil negara, melihat log kepegawaian, ataupun melakukan unggah berkas digitalisasi dokumen negara.</p>
                </div>
                <div class="card card-custom bg-white p-4 text-center my-5">
                    <div class="fs-1 mb-2">🔒</div>
                    <h5 class="fw-bold text-secondary">Modul Administrasi Terkunci</h5>
                    <p class="text-muted small mx-auto" style="max-width: 500px;">Silakan gunakan otentikasi akun tingkat tinggi (Administrator Infrastruktur) untuk membuka fitur pengelolaan berkas kepegawaian nasional.</p>
                </div>

            <!-- TAMPILAN JIKA USER BERHASIL MENJADI ADMIN -->
            <?php else: ?>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h2 class="h3 text-secondary fw-bold">Pusat Kendali Data & Informasi ASN</h2>
                </div>

                <div class="card card-custom mb-4 bg-white">
                    <div class="card-header bg-white fw-bold text-primary py-3 border-0">
                        📋 Manajemen Berkas Pegawai Aktif (Sinkronisasi BKN)
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Nomor Induk Pegawai (NIP)</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jabatan Struktural</th>
                                        <th>Status Arsip</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_pegawai && mysqli_num_rows($result_pegawai) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($result_pegawai)): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-secondary"><?php echo $row['nip']; ?></td>
                                                <td><?php echo $row['nama']; ?></td>
                                                <td><span class="badge bg-light text-dark border"><?php echo $row['jabatan']; ?></span></td>
                                                <td><span class="text-success small fw-bold">✓ Tersimpan</span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-7">
                        <div class="card card-custom mb-4 bg-white border border-danger-subtle">
                            <div class="card-header bg-danger text-white fw-bold py-3">
                                📤 Gerbang Unggah Berkas Digitalisasi Negara (SK / Ijazah)
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted small">Modul ekstraksi otomatis berkas fisik menjadi dokumen digital terenkripsi.</p>
                                <?php echo $msg; ?>
                                <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
                                    <div class="mb-4">
                                        <label for="dokumen" class="form-label small fw-bold text-secondary">Pilih Salinan File Pendukung</label>
                                        <input class="form-control form-control-lg fs-6 shadow-sm" type="file" id="dokumen" name="dokumen" required>
                                    </div>
                                    <button type="submit" name="upload" class="btn btn-primary fw-bold shadow px-4">Proses Pemutakhiran Berkas</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card card-custom bg-dark text-white-50 p-4 border-0">
                            <h6 class="fw-bold text-warning text-uppercase mb-2 small">🛡️ Catatan Sistem Integrasi</h6>
                            <p class="small mb-0">Seluruh dokumen yang berhasil divalidasi akan langsung dilempar ke direktori publik lokal `/uploads/` di server utama untuk keperluan sinkronisasi repositori arsip.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>