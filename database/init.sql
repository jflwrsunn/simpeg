CREATE DATABASE IF NOT EXISTS simpeg_db;
USE simpeg_db;

-- Tabel Users untuk Autentikasi
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pegawai / Karyawan
CREATE TABLE IF NOT EXISTS pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nip VARCHAR(50) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Log Aktivitas (Audit Trail)
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    activity TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Data Pengguna (Username & Password netral korporat)
INSERT INTO users (username, password, role) VALUES 
('administrator', 'admin123', 'Super Administrator'),
('hendra_hr', 'password123', 'HR Staff'),
('siti_finance', 'password123', 'Finance Operator');

-- Seed Data Pegawai Awal
INSERT INTO pegawai (nama, nip, jabatan, foto) VALUES 
('Budi Santoso, S.Kom.', '198501012010121001', 'Senior Infrastructure Analyst', 'sample_1.pdf'),
('Dewi Lestari, M.M.', '199005122015032002', 'Corporate HR Manager', 'sample_2.pdf'),
('Ahmad Fauzi, S.T.', '199208202018011003', 'Database Administrator', 'sample_3.pdf');

-- Seed Log Aktivitas Awal
INSERT INTO activity_logs (username, activity) VALUES 
('administrator', 'Inisialisasi sistem database korporat berhasil.'),
('hendra_hr', 'Memperbarui data profil kepegawaian divisi IT.');