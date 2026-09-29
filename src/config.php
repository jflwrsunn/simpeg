<?php
$host = 'db';
$user = 'simpeg_user';
$pass = 'simpeg_password';
$dbname = 'simpeg_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>