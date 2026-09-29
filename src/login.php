<?php
session_start();
require 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nip = $_POST['nip'];
    $password = $_POST['password'];

    // VULNERABILITY: SQL Injection
    // Variabel $nip dan $password dimasukkan langsung tanpa sanitasi/prepared statement
    $query = "SELECT * FROM pegawai WHERE nip = '$nip' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        header("Location: index.php");
        exit();
    } else {
        $error = "NIP atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | SIMPEG Target BSSN</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #e9ecef; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { width: 400px; padding: 20px; border-radius: 10px; }
    </style>
</head>
<body>
<div class="card card-login shadow-lg">
    <h3 class="text-center mb-4 font-weight-bold">SIMPEG Target</h3>
    <p class="text-center text-muted small">Sistem Informasi Kepegawaian Simulation</p>
    
    <?php if($error): ?>
        <div class="alert alert-danger p-2 small"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label>NIP Pegawai</label>
            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Masuk Ke Sistem</button>
    </form>
</div>
</body>
</html>