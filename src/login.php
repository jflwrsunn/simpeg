<?php
session_start();
include 'db.php';
$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // CELAH SQL INJECTION: String concatenation langsung tanpa prepared statement
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin'] = $row['username'];
        $_SESSION['role'] = $row['role']; // PENTING: Menyimpan tingkatan hak akses ke dalam session
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Gagal Otentikasi: NIP tidak terdaftar pada Sistem Manajemen Identitas Pegawai (SMIP).";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG - Sistem Informasi Manajemen Kepegawaian Aparatur Negara</title>
    <!-- 1. Perbaikan Jalur CDN Bootstrap 5 CSS -->
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body { min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .bg-gradient-gov {
            /* 2. Perbaikan Gambar Latar Belakang Perkantoran Realistis */
            background: linear-gradient(135deg, rgba(13, 71, 161, 0.95), rgba(25, 118, 210, 0.9)), url('https://unsplash.com') no-repeat center center/cover;
        }
        .login-sidebar { min-height: 100vh; }
        .btn-gov { background-color: #0d47a1; color: white; border: none; }
        .btn-gov:hover { background-color: #0a3680; color: white; }
        .announcement-box { background: rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 15px; }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        
        <!-- SISI KIRI: Panel Informasi (60% Layar) -->
        <div class="col-lg-7 d-none d-lg-flex bg-gradient-gov text-white align-items-center p-5">
            <div class="w-100 p-4">
                <!-- 3. Perbaikan Link Gambar Lambang Garuda Pancasila Resmi -->
                <img src="https://wikimedia.org" alt="Garuda" width="80" class="mb-4">
                <h1 class="display-5 fw-bold mb-2">Selamat Datang di Portal E-SIMPEG</h1>
                <p class="lead text-white-50 mb-5">Sistem Integrasi Manajemen Kepegawaian dan Transformasi Digital Aparatur Sipil Negara.</p>
                
                <div class="announcement-box shadow-sm mb-4">
                    <h6 class="fw-bold text-warning text-uppercase mb-2">📢 PENGUMUMAN REKONSILIASI DATA</h6>
                    <p class="small m-0 text-white-50">Batas akhir pemutakhiran berkas ijazah, SK Kenaikan Pangkat, dan data keluarga untuk alokasi tunjangan triwulan IV diperpanjang hingga akhir bulan ini. Harap pastikan dokumen digital Anda valid.</p>
                </div>

                <div class="d-flex gap-3 text-white-50 small mt-5 border-top border-secondary pt-3">
                    <div>🌐 Host: <span class="text-white">simpeg.go.id</span></div>
                    <div>🛠️ Versi Modul: <span class="text-white">v4.2.1-Enterprise</span></div>
                    <div>🔒 Enkripsi: <span class="text-white">SSL/TLS Active</span></div>
                </div>
            </div>
        </div>

        <!-- SISI KANAN: Form Login (40% Layar) -->
        <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white p-5 login-sidebar">
            <div class="w-100" style="max-width: 420px;">
                <div class="text-center text-lg-start mb-4">
                    <h3 class="fw-bold text-dark mb-1">Otentikasi Akun</h3>
                    <p class="text-muted small">Gunakan akun Single Sign-On (SSO) Kepegawaian Anda.</p>
                </div>

                <?php if($error): ?>
                    <div class="alert alert-danger small py-2" role="alert"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-bold text-secondary">Nomor Induk Pegawai (NIP)</label>
                        <input type="text" class="form-control form-control-lg fs-6" id="username" name="username" placeholder="Masukkan 18 digit NIP" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label small fw-bold text-secondary">Kata Sandi</label>
                        <input type="password" class="form-control form-control-lg fs-6" id="password" name="password" placeholder="Masukkan password keamanan">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-input-label text-muted" for="remember">Ingat Sesi Saya</label>
                        </div>
                        <a href="#" class="text-decoration-none text-primary">Lupa Password?</a>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="login" class="btn btn-gov btn-lg fs-6 fw-bold py-2.5 shadow-sm">Masuk Sistem Manajemen</button>
                    </div>
                </form>

                <div class="text-center mt-5 text-muted" style="font-size: 0.75rem;">
                    &copy; 2026 Badan Kepegawaian Nasional. <br>Protected by Government Cybersecurity Infrastructure.
                </div>
            </div>
        </div>

    </div>
</div>

<!-- 4. Perbaikan Jalur CDN JS Bootstrap 5 -->
<script src="https://jsdelivr.net"></script>
</body>
</html>