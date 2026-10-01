<?php
session_start();
include 'config.php';

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            $curr_user = $row['username'];
            $conn->query("INSERT INTO activity_logs (username, activity) VALUES ('$curr_user', 'Pengguna berhasil masuk ke sistem')");
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Kombinasi Nama Pengguna atau Kata Sandi tidak valid.";
        }
    } else {
        $error = "Akun pengguna tidak ditemukan di direktori pusat.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SIMPEG Enterprise Corp</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #334155;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            width: 100%;
            max-width: 420px;
        }
        .btn-primary {
            background-color: #0f172a;
            border-color: #0f172a;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #1e293b;
            border-color: #1e293b;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 0.95rem;
            border-color: #cbd5e1;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
            border-color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="container px-3">
        <div class="card card-login bg-white mx-auto p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="bg-dark text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 48px; height: 48px;">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">SIMPEG Enterprise</h4>
                <p class="text-muted small">Sistem Informasi Kepegawaian Korporat</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-danger py-2 small rounded-3 mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i><?= $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Nama Pengguna</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><i class="fas fa-user fa-sm"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted" style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><i class="fas fa-lock fa-sm"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100 shadow-sm">
                    Masuk ke Sistem <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="text-muted" style="font-size: 11px;">© 2026 PT Telekomunikasi Media Nusantara<br>Protected Enterprise Infrastructure</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>