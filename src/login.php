<?php
session_start();
include 'config.php';

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // CELAH KEAMANAN: SQL Injection 100% Aktif & Langsung Tembus
    $query = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['admin'] = $row['username'];
        $_SESSION['role'] = $row['role']; 
        header("Location: dashboard.php");
        exit();
    } else {
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
    <style>
        /* KODE STYLING INTERNAL RE-DESIGN - ANTI BERANTAKAN / ANTI OFFLINE */
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .login-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .mt-5 { margin-top: 3rem; }
        .fw-bold { font-weight: 700; }
        .m-0 { margin: 0; }
        .text-dark { color: #1f2937; }
        .text-muted { color: #6b7280; }
        .small { font-size: 0.875rem; }
        
        .brand-icon {
            font-size: 2.5rem;
            color: #ffffff;
            background-color: #1e293b;
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin: 0 auto 20px auto;
        }
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            font-size: 0.95rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #1e293b;
        }
        .btn-custom {
            width: 100%;
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 600;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background-color 0.2s;
        }
        .btn-custom:hover {
            background-color: #1e293b;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        .footer-line {
            font-size: 0.75rem;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 20px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="brand-icon">🗂️</div>
        <h3 class="fw-bold text-dark m-0">SIMPEG Enterprise</h3>
        <p class="text-muted small" style="margin-top: 4px;">Sistem Informasi Kepegawaian Korporat</p>
    </div>

    <?php if($error): ?>
        <div class="alert-danger text-center" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Nama Pengguna</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
        </div>
        
        <div>
            <button type="submit" name="login" class="btn-custom">Masuk ke Sistem &rarr;</button>
        </div>
    </form>

    <div class="text-center mt-5 footer-line">
        &copy; 2026 PT Telekomunikasi Media Nusantara. <br> Protected Enterprise Infrastructure.
    </div>
</div>

</body>
</html>