<?php
session_start();
include 'db.php';

// Otomatis buat tabel account_logs jika belum ada di database
$conn->query("CREATE TABLE IF NOT EXISTS account_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama']);
    $role = 'hr_staff'; // Otomatis menjadi staff
    $jabatan = trim($_POST['jabatan']);
    $telepon = trim($_POST['telepon']);
    $alamat = trim($_POST['alamat']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $gaji = "IDR 7.500.000";

    // Cek ketersediaan username
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        $error = "Username sudah digunakan! Silakan gunakan username lain.";
    } else {
        // Simpan ke tabel users
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt_user->bind_param("sss", $username, $password, $role);

        if ($stmt_user->execute()) {
            $user_id = $conn->insert_id;

            // Simpan profil pengguna
            $stmt_prof = $conn->prepare("INSERT INTO profiles (user_id, nama_lengkap, jabatan, tanggal_lahir, no_telepon, alamat, gaji) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt_prof) {
                $stmt_prof->bind_param("issssss", $user_id, $nama, $jabatan, $tanggal_lahir, $telepon, $alamat, $gaji);
                $stmt_prof->execute();
                $stmt_prof->close();
            }

            // Catat log pendaftaran akun baru
            $stmt_alog = $conn->prepare("INSERT INTO account_logs (username) VALUES (?)");
            if ($stmt_alog) {
                $stmt_alog->bind_param("s", $username);
                $stmt_alog->execute();
                $stmt_alog->close();
            }

            $success = "Registrasi akun berhasil! Silakan login.";
        } else {
            $error = "Gagal mendaftarkan akun. Coba lagi.";
        }
        $stmt_user->close();
    }
    $stmt_check->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — SIMPEG Enterprise Corp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); width: 100%; max-width: 480px; }
    </style>
</head>
<body>
    <div class="card bg-white p-4">
        <div class="text-center mb-4">
            <div class="bg-dark text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h4 class="fw-bold">Pendaftaran Akun</h4>
            <p class="text-muted small">SIMPEG Enterprise Corp (Staff Registration)</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger small py-2"><i class="fas fa-exclamation-triangle me-1"></i> <?= $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success small py-2"><i class="fas fa-check-circle me-1"></i> <?= $success; ?> <a href="login.php" class="fw-semibold">Login sekarang</a></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row g-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Username">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Password">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required placeholder="Nama Lengkap">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Jabatan / Divisi</label>
                <input type="text" name="jabatan" class="form-control" required placeholder="Contoh: HR Officer">
            </div>

            <div class="row g-2">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" required value="1998-01-01">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-semibold">Nomor Telepon</label>
                    <input type="text" name="telepon" class="form-control" required placeholder="08123456789">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Alamat Rumah</label>
                <textarea name="alamat" class="form-control" rows="2" required placeholder="Alamat lengkap"></textarea>
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold mb-3">
                <i class="fas fa-user-check me-1"></i> Daftar Akun Staff
            </button>
        </form>

        <div class="text-center text-muted small">
            Sudah punya akun? <a href="login.php" class="text-decoration-none fw-semibold">Masuk di sini</a>
        </div>
    </div>
</body>
</html>