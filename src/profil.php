<?php
session_start();

$logged_in_user = $_SESSION['admin'] ?? $_SESSION['username'] ?? null;

if (!$logged_in_user) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Pastikan tabel profil ada
$conn->query("CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama_lengkap VARCHAR(100),
    tgl_lahir DATE,
    jabatan VARCHAR(100),
    gaji VARCHAR(50),
    telepon VARCHAR(20),
    alamat TEXT
)");

// Cek dan isi data dummy jika kosong
$check_data = $conn->query("SELECT COUNT(*) as cnt FROM user_profiles");
if ($check_data) {
    $row_cnt = $check_data->fetch_assoc();
    if ($row_cnt['cnt'] == 0) {
        $conn->query("INSERT INTO user_profiles (user_id, nama_lengkap, tgl_lahir, jabatan, gaji, telepon, alamat) VALUES
        (1, 'Administrator Utama', '1985-05-12', 'Super Administrator', 'IDR 45.000.000', '081234567890', 'Jl. Merdeka No. 1, Jakarta Pusat'),
        (2, 'Andi Administrator', '1990-08-20', 'System Administrator', 'IDR 25.000.000', '081876543210', 'Jl. Sudirman No. 45, Jakarta Selatan'),
        (3, 'Hendra HR Staff', '1995-12-03', 'HR Development Staff', 'IDR 8.500.000', '085612345678', 'Jl. Gatot Subroto No. 12, Depok'),
        (4, 'Siti Finance Operator', '1996-02-14', 'Financial Staff', 'IDR 9.000.000', '087888999111', 'Jl. Margonda Raya No. 88, Depok')");
    }
}

// Ambil ID user yang sedang login dari tabel users
$stmt_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt_user->bind_param("s", $logged_in_user);
$stmt_user->execute();
$res_user = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

$my_user_id = $res_user['id'] ?? 1;

// --- LOGIKA UTAMA & IDOR ---
if (isset($_GET['id'])) {
    // Mode Uji IDOR: Jika user mengetik manual ?id=... di URL, ambil berdasarkan primary key (up.id)
    $profile_id = intval($_GET['id']);
    $stmt_profile = $conn->prepare("SELECT up.*, u.username, u.role FROM user_profiles up JOIN users u ON up.user_id = u.id WHERE up.id = ?");
    $stmt_profile->bind_param("i", $profile_id);
} else {
    // Mode Normal ("Profil Saya"): Ambil persis berdasarkan user_id akun yang sedang aktif login
    $stmt_profile = $conn->prepare("SELECT up.*, u.username, u.role FROM user_profiles up JOIN users u ON up.user_id = u.id WHERE up.user_id = ?");
    $stmt_profile->bind_param("i", $my_user_id);
}

$stmt_profile->execute();
$profile = $stmt_profile->get_result()->fetch_assoc();
$stmt_profile->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pegawai — SIMPEG Enterprise Corp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .card-header { background-color: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; padding: 1.25rem 1.5rem; }
        .profile-avatar { width: 100px; height: 100px; background-color: #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; border-radius: 50%; margin: 0 auto 1rem auto; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="index.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-network-wired fa-sm"></i>
                </div>
                <span>SIMPEG <span class="text-muted fw-normal fs-6">Enterprise Corp</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="index.php" class="btn btn-sm btn-outline-secondary fw-medium px-3"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card bg-white p-4">
                    <div class="text-center mb-4">
                        <div class="profile-avatar shadow-sm">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($profile['nama_lengkap'] ?? 'Profil Tidak Ditemukan'); ?></h4>
                        <p class="text-muted small mb-2">Username: <strong><?= htmlspecialchars($profile['username'] ?? '-'); ?></strong></p>
                        <span class="badge bg-primary px-3 py-1"><?= htmlspecialchars($profile['role'] ?? 'Unknown'); ?></span>
                    </div>

                    <?php if ($profile): ?>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span class="text-muted"><i class="fas fa-id-badge me-2 text-secondary"></i> Jabatan / Divisi</span>
                                <span class="fw-semibold"><?= htmlspecialchars($profile['jabatan']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span class="text-muted"><i class="fas fa-calendar-alt me-2 text-secondary"></i> Tanggal Lahir</span>
                                <span class="fw-semibold"><?= htmlspecialchars($profile['tgl_lahir']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span class="text-muted"><i class="fas fa-phone me-2 text-secondary"></i> Nomor Telepon</span>
                                <span class="fw-semibold"><?= htmlspecialchars($profile['telepon']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span class="text-muted"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> Alamat Rumah</span>
                                <span class="fw-semibold text-end"><?= htmlspecialchars($profile['alamat']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between py-3 bg-light rounded-2 mt-2">
                                <span class="text-danger fw-bold"><i class="fas fa-money-bill-wave me-2"></i> Informasi Gaji (Confidential)</span>
                                <span class="fw-bold text-danger"><?= htmlspecialchars($profile['gaji']); ?></span>
                            </li>
                        </ul>
                        <div class="alert alert-warning small text-center mb-0">
                            <i class="fas fa-info-circle me-1"></i> Tips Pentesting: Coba ubah URL menjadi <code>profil.php?id=3</code> untuk menguji celah IDOR!
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <p>Data profil dengan ID tersebut tidak ditemukan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>