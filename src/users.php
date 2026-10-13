<?php
session_start();
$logged_in_user = $_SESSION['admin'] ?? $_SESSION['username'] ?? null;

if (!$logged_in_user) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Cek role user
$stmt_role = $conn->prepare("SELECT role FROM users WHERE username = ?");
$stmt_role->bind_param("s", $logged_in_user);
$stmt_role->execute();
$role = $stmt_role->get_result()->fetch_assoc()['role'] ?? 'hr_staff';
$stmt_role->close();

$is_super_admin = ($role == 'super_admin' || $logged_in_user == 'administrator');
$is_admin = ($role == 'admin');

// Hanya Admin dan Super Admin yang boleh membuka halaman ini
if (!$is_super_admin && !$is_admin) {
    die("Akses ditolak: Halaman ini hanya untuk Admin & Super Admin.");
}

// Proses Hapus Akun Pengguna
if (isset($_POST['delete_user_account'])) {
    $target_uid = intval($_POST['target_user_id']);
    
    // Mencegah penghapusan akun administrator utama
    $res_check = $conn->query("SELECT username FROM users WHERE id = $target_uid");
    if ($res_check && $res_check->num_rows > 0) {
        $u_row = $res_check->fetch_assoc();
        if ($u_row['username'] !== 'administrator') {
            $conn->query("DELETE FROM profiles WHERE user_id = $target_uid");
            $conn->query("DELETE FROM users WHERE id = $target_uid");
        }
    }
    header("Location: users.php?msg=deleted");
    exit;
}

// Ambil semua daftar akun dari tabel users (pasti muncul semua walau belum isi profil)
$all_users = $conn->query("SELECT u.id, u.username, u.role, p.nama_lengkap 
                           FROM users u 
                           LEFT JOIN profiles p ON u.id = p.user_id 
                           ORDER BY u.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna — SIMPEG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #334155; }
        .navbar-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="index.php">
                <div class="bg-dark text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-network-wired fa-sm"></i>
                </div>
                <span>SIMPEG <span class="text-muted fw-normal fs-6">Enterprise Corp</span></span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="index.php" class="btn btn-sm btn-outline-dark fw-medium px-3"><i class="fas fa-home me-1"></i> Dashboard</a>
                <a href="profil.php" class="btn btn-sm btn-outline-dark fw-medium px-3"><i class="fas fa-user-circle me-1"></i> Profil Saya</a>
                <span class="badge bg-dark px-3 py-2 fw-semibold"><i class="fas fa-user-shield me-1"></i> <?= htmlspecialchars($logged_in_user); ?> (<?= htmlspecialchars($role); ?>)</span>
                <a href="logout.php" class="btn btn-sm btn-danger fw-medium px-3"><i class="fas fa-sign-out-alt me-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5 flex-grow-1" style="max-width: 900px;">
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                <i class="fas fa-check-circle me-1"></i> Akun berhasil dihapus dari sistem!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold text-dark mb-1"><i class="fas fa-users-cog me-2 text-primary"></i> Kelola Akun Pengguna</h4>
                    <p class="text-muted small mb-0">Daftar seluruh akun terdaftar dalam sistem SIMPEG Enterprise Corp.</p>
                </div>
                <span class="badge bg-primary px-3 py-2">Total: <?= $all_users ? $all_users->num_rows : 0; ?> Akun</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th class="ps-3">No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Role / Hak Akses</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($all_users && $all_users->num_rows > 0): $no = 1; while($usr = $all_users->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-3 fw-semibold"><?= $no++; ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($usr['username']); ?></td>
                            <td><?= htmlspecialchars($usr['nama_lengkap'] ?? '-'); ?></td>
                            <td>
                                <?php 
                                    $badge_color = 'bg-secondary';
                                    if ($usr['role'] == 'super_admin') $badge_color = 'bg-danger';
                                    elseif ($usr['role'] == 'admin') $badge_color = 'bg-dark';
                                    elseif ($usr['role'] == 'hr_staff') $badge_color = 'bg-info text-dark';
                                ?>
                                <span class="badge <?= $badge_color; ?>"><?= htmlspecialchars($usr['role']); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($usr['username'] !== 'administrator'): ?>
                                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun <?= htmlspecialchars($usr['username']); ?> secara permanen?');">
                                        <input type="hidden" name="target_user_id" value="<?= $usr['id']; ?>">
                                        <button type="submit" name="delete_user_account" class="btn btn-sm btn-outline-danger px-3 py-1">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus Akun
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small italic"><i class="fas fa-shield-alt text-success me-1"></i> Utama</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada akun terdaftar.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>