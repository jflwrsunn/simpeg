CREATE DATABASE IF NOT EXISTS simpeg_db;
USE simpeg_db;

-- Tabel Pegawai
CREATE TABLE IF NOT EXISTS pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    golongan VARCHAR(10) NOT NULL,
    catatan_profil TEXT
);

-- Tabel Dokumen
CREATE TABLE IF NOT EXISTS dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_dokumen VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES pegawai(id)
);

-- Dummy Data Pegawai
INSERT INTO pegawai (id, nip, password, nama, jabatan, golongan, catatan_profil) VALUES
(1, '198001012005011001', 'admin123', 'Budi Santoso, M.Kom', 'Kepala Subbagian Kepegawaian', 'IV/a', 'Salam kenal, saya Budi.'),
(2, '198502152010012002', 'staf123', 'Siti Aminah, S.T.', 'Staf Analis Kepegawaian', 'III/c', 'Staf analis kepegawaian BSSN.'),
(3, '199203102015011003', 'user123', 'Rian Hidayat', 'Pengelola TI', 'III/a', 'Pengelola sistem informasi.');

-- Dummy Data Dokumen
INSERT INTO dokumen (id, user_id, nama_dokumen, file_path) VALUES
(1, 1, 'Laporan_Evaluasi_Kinerja_Direktur_2026.pdf', 'files/laporan_evaluasi.pdf'),
(2, 2, 'SK_Kenaikan_Pangkat_Siti.pdf', 'files/sk_siti.pdf'),
(3, 3, 'Data_Absensi_Oktober_2026.pdf', 'files/absensi_rian.pdf');