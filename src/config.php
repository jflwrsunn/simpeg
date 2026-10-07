<?php
$host = 'db';
$user = 'simpeg_user';
$pass = 'simpeg_password';
$dbname = 'simpeg_db';

$max_attempts = 10; // Mencoba hingga 10 kali
$attempts = 0;
$conn = false;

// Melakukan perulangan jika database masih sibuk booting di latar belakang
while ($attempts < $max_attempts) {
    // Tanda @ berfungsi menyembunyikan warning bawaan PHP di layar browser
    $conn = @new mysqli($host, $user, $pass, $dbname);
    
    if (!$conn->connect_error) {
        break; // Jika sukses terhubung, keluar dari perulangan
    }
    
    $attempts++;
    sleep(2); // Tunggu 2 detik sebelum mencoba menyambung kembali
}

if ($conn->connect_error) {
    die("Koneksi database gagal setelah beberapa kali percobaan: " . $conn->connect_error);
}
?>