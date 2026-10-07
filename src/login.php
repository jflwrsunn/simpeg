<?php
session_start();
include 'config.php';

$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // VULNERABILITY: SQL Injection 100% konsisten jebol langsung
    $query = "SELECT id, username, password, role FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['admin'] = $row['username'];
        $_SESSION['role'] = $row['role']; 
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Kredensial salah atau identitas tidak terdaftar di direktori pusat.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otentikasi Akses - SIMPEG Enterprise</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <link href="https://googleapis.com" rel="stylesheet">
    <link rel="stylesheet" href="https://cloudflare.com">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Ornamen Lingkaran Estetik di Latar Belakang */
        body::before {
            content: ''; position: absolute; width: 300px; height: 300px;
            background: linear-gradient(#0d47a1, #1565c0);
            top: 10%; left: 15%; border-radius: 50%; opacity: 0.15; filter: blur(50px);
        }
        body::after {
            content: ''; position: absolute; width: 400px; height: 400px;
            background: linear-gradient(#7e22ce, #ec4899);
            bottom: 5%; right: 10%; border-radius: 50%; opacity: 0.12; filter: blur(60px);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            width: 100%; max-width: 440px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .form-control-custom {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .form-control-custom:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }
        .form-control-custom::placeholder { color: rgba(255,255,255,0.4); }
        .btn-modern {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white; font-weight: 600; padding: 12px;
            border: none; border-radius: 12px; transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.3);
        }
        .btn-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(29, 78, 216, 0.4);
            color: white;
        }
    </style>
</head>
<body>

<div class="glass-card">
    <div class="text-center mb-4">
        <div class="text-primary mb-2 fs-2"><i class="fa-solid fa-cube text-info"></i></div>
        <h4 class="fw-bold text-white m-0 tracking-tight">SIMPEG Enterprise</h4>
        <p class="small mt-1" style="color: rgba(255,255,255,0.5);">Gerbang Masuk Infrastruktur Korporat</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger py-2 small text-center border-0 text-white" style="background: rgba(239, 68, 68, 0.2);" role="alert">
            <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label small fw-semibold" style="color: rgba(255,255,255,0.8);">Nama Pengguna</label>
            <input type="text" class="form-control form-control-custom" name="username" placeholder="Masukkan ID atau username" required>
        </div>
        
        <div class="mb-4">
            <label class="form-label small fw-semibold" style="color: rgba(255,255,255,0.8);">Kata Sandi</label>
            <input type="password" class="form-control form-control-custom" name="password" placeholder="Masukkan password" required>
        </div>
        
        <div class="d-grid">
            <button type="submit" name="login" class="btn btn-modern">Masuk Aplikasi &rarr;</button>
        </div>
    </form>

    <div class="text-center mt-4 pt-3 text-white-50" style="font-size: 0.7rem; border-top: 1px solid rgba(255,255,255,0.06);">
        Protected by Enterprise Security Framework.
    </div>
</div>

</body>
</html>
