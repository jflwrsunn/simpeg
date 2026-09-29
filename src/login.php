<?php
session_start();
include 'config.php';

$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sengaja dibuat rentan SQL Injection atau query standar untuk lab pentest
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user['username'];
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
    <title>Login - SIMPEG BSSN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg p-4" style="width: 400px; border-radius: 15px;">
        <div class="text-center mb-4">
            <i class="fas fa-shield-alt fa-3x text-primary mb-2"></i>
            <h3 class="fw-bold text-dark">SIMPEG BSSN</h3>
            <p class="text-muted small">Sistem Informasi Kepegawaian Terpadu</p>
        </div>
        <?php if($error): ?>
            <div class="alert alert-danger py-2 small"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-secondary small fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
                </div>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-15 py-2 fw-bold shadow-sm">Masuk Sistem</button>
        </form>
        <div class="text-center mt-4 text-muted small">
            &copy; 2026 Badan Siber dan Sandi Negara
        </div>
    </div>
</body>
</html>