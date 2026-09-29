<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM pegawai WHERE id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | SIMPEG</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #343a40; color: white; padding-top: 20px;}
        .sidebar a { color: #c2c7d0; text-decoration: none; padding: 10px 20px; display: block; }
        .sidebar a:hover { background-color: #494e53; color: white; }
        .content { padding: 20px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <h4 class="text-center mb-4">SIMPEG</h4>
            <a href="index.php">Dashboard</a>
            <a href="profile.php">Update Profil</a>
            <a href="logout.php">Logout</a>
        </div>
        <div class="col-md-10 content">
            <h2>Selamat Datang, <?php echo $user['nama']; ?>!</h2>
            <p class="text-muted">NIP: <?php echo $user['nip']; ?> | Jabatan: <?php echo $user['jabatan']; ?></p>
            <hr>
            
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-dark text-white">Arsip Dokumen Kepegawaian Anda</div>
                <div class="card-body">
                    <p>Unduh dokumen resmi kepegawaian Anda di bawah ini:</p>
                    <a href="download.php?doc_id=<?php echo $user_id; ?>" class="btn btn-outline-primary">
                        Unduh Dokumen SK / Laporan (ID: <?php echo $user_id; ?>)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>