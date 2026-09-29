<?php
$host = 'db';
$user = 'simpeg_user';
$pass = 'simpeg_password';
$db   = 'simpeg_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}
?>