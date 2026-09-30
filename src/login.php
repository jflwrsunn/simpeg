<?php
session_start();
include 'config.php';

$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sengaja dibuat rentan SQL Injection (tanpa prepared statement)
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SIMPEG BSSN Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg p-4" style="width: 420px; border-radius: 12px;">
        <div class="text-center mb-4">
            <i class="fas fa-fingerprint fa-3x text-primary mb-2"></i>
            <h4 class="fw-bold text-dark">SIMPEG BSSN</h4>
            <p class="text-muted small">Portal Layanan Kepegawaian Terpusat</p>
        </div>
        <?php if($error): ?>
            <div class="alert alert-danger py-2 small"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">Masuk Sistem</button>
        </form>
        <div class="text-center mt-4 text-muted" style="font-size: 0.75rem;">
            &copy; 2026 Badan Siber dan Sandi Negara • Lab Training
        </div>
    </div>
</body>
</html>