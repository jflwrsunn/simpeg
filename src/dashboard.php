<?php
session_start();
if (!isset(\$_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

\$msg = "";

// Proses Upload Dokumen
if (isset(\(_POST['upload'])) {\)target_dir = "uploads/";
    
    // Membuat folder uploads secara otomatis jika belum ada
    if (!file_exists(\$target_dir)) {
        mkdir(\$target_dir, 0755, true);
    }

    \(file_name = basename(\)_FILES["dokumen"]["name"]);
    \$target_file = \(target_dir .\)file_name;

    // KELONGGARAN KEAMANAN (BACKEND): Tidak ada validasi ekstensi sama sekali di server
    if (move_uploaded_file(\$_FILES["dokumen"]["tmp_name"], \(target_file)) {\)msg = "<div class='alert alert-success small'><strong>Sukses!</strong> Dokumen berhasil diarsipkan ke sistem. <br>Akses berkas: <a href='\(target_file' target='_blank' class='fw-bold'>\)target_file</a></div>";
    } else {
        \$msg = "<div class='alert alert-danger small'><strong>Gagal!</strong> Sistem gagal menulis berkas ke direktori penyimpanan. Periksa izin folder server.</div>";
    }
}

// Ambil Data Pegawai untuk Tabel Visual
\$query_pegawai = "SELECT * FROM pegawai";
\$result_pegawai = mysqli_query(conn, query_pegawai);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrator - SIMPEG Portal</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar-gov { background-color: #0d47a1; }
        .sidebar { background-color: #ffffff; min-height: calc(100vh - 56px); box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
        .card-custom { border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .nav-link.active { background-color: #e3f2fd; color: #0d47a1 !important; font-weight: bold; border-radius: 5px; }
    </style>
    
    <!-- PROTEKSI JUBAH (CLIENT-SIDE): Sengaja dibuat mudah ditembus dengan Burp Suite/matikan JS -->
    <script>
    function validateForm() {
        var fileInput = document.getElementById('dokumen');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.pdf|\.docx|\.jpg|\.png)\$/i;
        
        if(!allowedExtensions.exec(filePath)){
            alert('Sistem Menolak: Format berkas tidak diizinkan! Hanya menerima PDF, DOCX, atau Gambar (JPG/PNG).');
            fileInput.value = '';
            return false;
        }
        return true;
    }
    </script>
</head>
<body>

<!-- Navbar Atas -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-gov shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold d-flex align-items-center">
            <img src="https://wikimedia.org" alt="Garuda" width="30" class="me-2">
            E-SIMPEG ADMIN PANEL
        </span>
        <div class="d-flex align-items-center text-white small me-3">
            <span class="badge bg-success me-2">Sesi Aktif</span> Sdr. <?php echo htmlspecialchars(\$_SESSION['admin']); ?>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu Navigasi -->
        <div class="col-md-3 col-lg-2 sidebar p-3 d-none d-md-block">
            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a class="nav-link text-dark active" href="#">Dashboard Utama</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="#">Manajemen ASN</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-secondary" href="#">Struktur Jabatan</a>
                </li>
                <li class="nav-item">
                    <hr>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger fw-bold" href="logout.php">Keluar Sistem</a>
                </li>
            </ul>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2 text-secondary fw-bold">Pusat Data & Informasi Kepegawaian</h1>
            </div>

            <!-- Bagian Tabel Data Pegawai (Membuat Lab Terlihat Kaya Informasi) -->
            <div class="card card-custom mb-4">
                <div class="card-header bg-white fw-bold text-primary py-3">
                    Daftar Pegawai Aktif (Database Pusat)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">NIP</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan Struktural</th>
                                    <th>Arsip Dokumen SK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (\(result_pegawai && mysqli_num_rows(\)result_pegawai) > 0): ?>
                                    <?php while(\(row = mysqli_fetch_assoc(\)result_pegawai)): ?>
                                        <tr>
                                            <td class="ps-3 fw-bold"><?php echo \$row['nip']; ?></td>
                                            <td><?php echo \$row['nama']; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo \$row['jabatan']; ?></span></td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary py-0">Lihat Berkas</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted p-3">Tidak ada data pegawai. Silakan periksa tabel database Anda.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bagian Form Upload Rentan -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-custom mb-4">
                        <div class="card-header bg-white fw-bold text-danger py-3">
                            Pusat Unggah Digitalisasi SK / Ijazah ASN
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">Fitur ini digunakan untuk mengunggah salinan digital Surat Keputusan (SK) atau Ijazah dalam format dokumen resmi negara.</p>
                            
                            <?php echo \$msg; ?>
                            
                            <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
                                <div class="mb-3">
                                    <label for="dokumen" class="form-label small fw-bold">Pilih Berkas Dokumen Pendukung</label>
                                    <input class="form-control" type="file" id="dokumen" name="dokumen" required>
                                </div>
                                <button type="submit" name="upload" class="btn btn-primary shadow-sm px-4">Mulai Ekstraksi & Unggah</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card card-custom bg-light border-0">
                        <div class="card-body">
                            <h5 class="fw-bold text-dark small text-uppercase">Petunjuk Keamanan Server</h5>
                            <p class="text-muted small mb-0">Seluruh dokumen yang diunggah akan masuk ke dalam sub-direktori penyimpanan terisolasi `/uploads/`. Validasi struktur berkas dilakukan secara otomatis menggunakan modul validasi terintegrasi sistem penjamin mutu digital.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://jsdelivr.net"></script>
</body>
</html>