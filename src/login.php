<?php
session_start();
include 'config.php'; // Menghubungkan ke file konfigurasi database Docker Anda

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // =========================================================================
    // CELAH KEAMANAN REALISTIS (OWASP TOP 10: SQL INJECTION)
    // Kueri sengaja dibuat rentan menggunakan penggabungan string langsung
    // tanpa adanya fungsi sanitasi ataupun Prepared Statements.
    // =========================================================================
    $query = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password'";
    
    // Mengeksekusi kueri mentah langsung ke database MariaDB
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Menyimpan data identitas ke dalam sesi (Session) internal server
        $_SESSION['admin'] = $row['username'];
        $_SESSION['role'] = $row['role']; 
        
        // Pengalihan ke halaman administrasi dalam setelah sukses bypass
        header("Location: dashboard.php");
        exit();
    } else {
        // Pesan eror visual yang akan muncul di layar jika kueri salah/gagal
        $error = "Akun pengguna tidak ditemukan di direktori pusat.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG Enterprise - Otentikasi Portal</title>
    <!-- Memanggil Bootstrap 5 Resmi via CDN yang valid -->
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            width: 100%;
            max-width: 450px;
        }
        .brand-icon {
            font-size: 2.5rem;
            color: #ffffff;
            background-color: #1e293b;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin: 0 auto 20px auto;
        }
        .btn-custom {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 12px;
            transition: all 0.2s;
        }
        .btn-custom:hover {
            background-color: #1e293b;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="card login-card p-5">
    <div class="text-center mb-4">
        <div class="brand-icon">🗂️</div>
        <h3 class="fw-bold text-dark m-0">SIMPEG Enterprise</h3>
        <p class="text-muted small mt-1">Sistem Informasi Kepegawaian Korporat</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger py-2 small text-center" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label small fw-bold text-secondary">Nama Pengguna</label>
            <input type="text" class="form-control form-control-lg fs-6" id="username" name="username" placeholder="Masukkan username" required>
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label small fw-bold text-secondary">Kata Sandi</label>
            <input type="password" class="form-control form-control-lg fs-6" id="password" name="password" placeholder="Masukkan password" required>
        </div>
        
        <div class="d-grid">
            <button type="submit" name="login" class="btn btn-custom btn-lg fs-6 shadow-sm">Masuk ke Sistem &rarr;</button>
        </div>
    </form>

    <div class="text-center mt-5 text-muted small" style="font-size: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 20px;">
        &copy; 2026 PT Telekomunikasi Media Nusantara. <br> Protected Enterprise Infrastructure.
    </div>
</div>

<script src="https://jsdelivr.net"></script>
</body>
</html>
