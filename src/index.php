<?php
session_start();

// Menyesuaikan dengan session dari login.php (bisa 'username' atau 'admin')
$logged_in_user = $_SESSION['username'] ?? $_SESSION['admin'] ?? null;

if (!$logged_in_user) {
    header("Location: login.php");
    exit;
}

// Sinkronkan session agar kedua variabel terisi
$_SESSION['username'] =$logged_in_user;
$_SESSION['admin'] =$logged_in_user;

include 'config.php';

// Ambil data role user dari database
$stmt_role =$conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt_role->bind_param("s", $logged_in_user);
$stmt_role->execute();$res_role = $stmt_role->get_result()->fetch_assoc();$stmt_role->close();

$real_role = $res_role['role'] ?? $_SESSION['role'] ?? 'user';
$_SESSION['role'] =$real_role;

// Cek apakah termasuk admin (berdasarkan seed database: 'admin')
$is_admin = (strtolower($real_role) === 'admin' \vert{}\vert{} stripos(strtolower($real_role), 'super') !== false);

$message = "";
$error_msg = "";

if (isset($_POST['submit'])) {$nama = trim($_POST['nama']);$nip = trim($_POST['nip']);$jabatan = trim($_POST['jabatan']);$target_dir = __DIR__ . "/uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $original_name = basename($_FILES['foto']['name']);$file_name = time() . "_" . preg_replace("/\s+/", "_", $original_name);$target_file = $target_dir .$file_name;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'],$target_file)) {
        $stmt =$conn->prepare("INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama,$nip, $jabatan,$file_name);
        $stmt->execute();$stmt->close();
        
        $log_stmt =$conn->prepare("INSERT INTO activity_logs (username, activity) VALUES (?, ?)");
        $log_act = "Mengunggah dokumen pegawai baru: $nama";
        $log_stmt->bind_param("ss", $logged_in_user, $log_act);$log_stmt->execute();
        $log_stmt->close();$message = "Dokumen arsip kepegawaian berhasil disimpan ke server.";
    } else {
        $error_msg = "Gagal memproses penyimpanan berkas ke direktori server.";
    }
}

// Logika Hapus Data (Khusus Admin)
if (isset($_GET['hapus']) && $is_admin) {$id = intval($_GET['hapus']);$conn->query("DELETE FROM pegawai WHERE id = $id");
    header("Location: index.php");
    exit;
}

$pegawai_result = null;
$logs_result = null;
if ($is_admin) {
    $pegawai_result =$conn->query("SELECT * FROM pegawai ORDER BY id DESC");
    $logs_result =$conn->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 5");
}

$total_pegawai =$conn->query("SELECT COUNT(*) as total FROM pegawai")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPEG — PT Telekomunikasi Media Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .card-header { background-color: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; padding: 1.25rem 1.5rem; }
        .btn-primary { background-color: #0f172a; border-color: #0f172a; border-radius: 8px; padding: 0.6rem 1rem; font-weight: 500; }
        .btn-primary:hover { background-color: #1e293b; border-color: #1e293b; }
        .form-control { border-radius: 8px; padding: 0.65rem 0.85rem; border-color: #cbd5e1; }
        .badge-soft-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-soft-primary { background-color: #e0f2fe; color: #0369a1; }
        .alert-announcement { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border-radius: 12px; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="index.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">