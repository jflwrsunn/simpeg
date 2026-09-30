CREATE DATABASE IF NOT EXISTS simpeg_db;
USE simpeg_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin'
);

CREATE TABLE IF NOT EXISTS pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nip VARCHAR(50) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    foto VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    activity TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data Dummy User untuk latihan Pentest (SQLi & IDOR)
INSERT INTO users (id, username, password, role) VALUES 
(1, 'admin', 'admin123', 'Administrator Pusat'),
(2, 'mora', 'sandi123', 'Analis Sandi Madya'),
(3, 'operator', 'op2026', 'Staff Operator');