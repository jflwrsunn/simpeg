<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // VULNERABILITY 1: Stored XSS
    // Input dimasukkan tanpa sanitasi
    $catatan = $_POST['catatan_profil'];
    $update_query = "UPDATE pegawai SET catatan_profil = '$catatan' WHERE id = $user_id";
    $conn->query($update_query);
    $message = "Profil berhasil diperbarui!";

    // VULNERABILITY 2: Unrestricted File Upload (RCE)
    // Tidak ada pemeriksaan ekstensi file (.php diizinkan terunggah)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fileName = $_FILES['foto']['name'];
        $fileTmpName = $_FILES['foto']['tmp_name'];
        $uploadDir = 'uploads/';
        $targetFilePath = $uploadDir . basename($fileName);

        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
            $message .= "<br>Foto profil berhasil diunggah ke: <a href='$targetFilePath' target='_blank'>$targetFilePath</a>";
        } else {
            $error = "Gagal mengunggah foto profil.";
        }
    }
}

$query = "SELECT * FROM pegawai WHERE id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Profil | SIMPEG</title>
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
            <h2 class="mb-4">Pengaturan Profil Pegawai</h2>

            <?php if($message) echo "<div class='alert alert-success'>$message</div>"; ?>
            <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">Detail Informasi</div>
                        <div class="card-body">
                            <p><b>NIP:</b> <?php echo $user['nip']; ?></p>
                            <p><b>Nama:</b> <?php echo $user['nama']; ?></p>
                            <p><b>Jabatan:</b> <?php echo $user['jabatan']; ?></p>
                            <p><b>Golongan:</b> <?php echo $user['golongan']; ?></p>
                            <hr>
                            <h5>Catatan Bio Saat Ini:</h5>
                            <div class="p-3 bg-light border rounded">
                                <!-- Stored XSS Tereksekusi Di Sini -->
                                <?php echo $user['catatan_profil']; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">Form Edit Profil & Unggah Pas Foto</div>
                        <div class="card-body">
                            <form action="profile.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Ubah Catatan Bio / Profil:</label>
                                    <textarea name="catatan_profil" class="form-control" rows="3"><?php echo $user['catatan_profil']; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Unggah Pas Foto Formal (.jpg / .png):</label>
                                    <input type="file" name="foto" class="form-control-file" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>