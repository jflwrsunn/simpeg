<?php
session_start();
include 'db.php';

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // CELAH KEAMANAN TETAP DIJAGA: SQL Injection langsung lewat penyambungan string
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin'] = $row['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Kombinasi NIP/Username atau Password salah, atau akun Anda belum diaktivasi oleh Biro Kepegawaian.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG - Sistem Informasi Manajemen Kepegawaian Negara</title>
    <!-- Perbaikan Jalur CDN Bootstrap 5 -->
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            background: rgba(255, 255, 255, 0.95);
            overflow: hidden;
        }
        .brand-header {
            background-color: #0d47a1;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .brand-logo {
            width: 70px;
            height: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.2));
        }
        .btn-gov {
            background-color: #0d47a1;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-gov:hover {
            background-color: #0a3680;
            color: white;
            transform: translateY(-1px);
        }
        .notice-box {
            font-size: 0.8rem;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card">
                <!-- Bagian Atas / Header Instansi -->
                <div class="brand-header">
                    <!-- Perbaikan Link Gambar Lambang Garuda Pancasila -->
                    <img src="https://wikimedia.org" alt="Logo Negara" class="brand-logo">
                    <h5 class="m-0 fw-bold tracking-wide">SIMPEG PORTAL</h5>
                    <small class="text-white-50">Sistem Informasi Manajemen Kepegawaian Daerah</small>
                </div>
                
                <!-- Form Login -->
                <div class="card-body p-4">
                    <p class="text-muted text-center small mb-4">Silakan masuk menggunakan akun SIAKAD / NIP resmi Anda yang terdaftar.</p>
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger d-flex align-items-center small" role="alert">
                            <div><?php echo $error; ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label small fw-bold text-secondary">Nomor Induk Pegawai (NIP) / Username</label>
                            <input type="text" class="form-control form-control-lg fs-6" id="username" name="username" placeholder="Contoh: 19920101..." required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-secondary">Kata Sandi</label>
                            <input type="password" class="form-control form-control-lg fs-6" id="password" name="password" placeholder="Masukkan password Anda">
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="login" class="btn btn-gov btn-lg fs-6 fw-bold py-2.5 shadow-sm">Masuk Aplikasi</button>
                        </div>
                    </form>
                    
                    <!-- Catatan Kaki Hukum / Disclaimer Khas Web Pemerintah -->
                    <div class="notice-box text-center">
                        <p class="mb-1 fw-bold text-danger">⚠️ PERINGATAN HUKUM</p>
                        <p class="m-0">Sistem ini hanya diizinkan untuk aparatur sipil negara yang sah. Akses ilegal atau penyalahgunaan data akan diproses sesuai UU ITE yang berlaku.</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3 text-white-50 small">
                &copy; 2026 Badan Kepegawaian dan Pengembangan Sumber Daya Manusia. All Rights Reserved.
            </div>
        </div>
    </div>
</div>

<!-- Perbaikan Jalur CDN JS Bootstrap 5 -->
<script src="https://jsdelivr.net"></script>
</body>
</html>