<?php
$host = 'db';
$user = 'simpeg_user';
$pass = 'simpeg_password';
$dbname = 'simpeg_db';

$max_attempts = 5;
$attempts = 0;
$conn = false;

// Loop untuk mencoba koneksi berulang kali jika database sedang booting
while ($attempts < $max_attempts) {
    // Menggunakan tanda @ untuk menyembunyikan warning bawaan PHP saat mencoba koneksi
    $conn = @new mysqli($host, $user, $pass, $dbname);
    
    if (!$conn->connect_error) {
        break; // Jika sukses terhubung, keluar dari loop
    }
    
    $attempts++;
    sleep(2); // Tunggu 2 detik sebelum mencoba kembali
}

if ($conn->connect_error) {
    die("Koneksi database gagal setelah beberapa kali percobaan: " . $conn->connect_error);
}
?>
