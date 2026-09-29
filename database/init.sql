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

-- Insert default admin account
INSERT INTO users (username, password, role) VALUES ('admin', 'admin123', 'administrator');