<?php
// UBAH DARI "localhost" MENJADI "db" (sesuai nama service di docker-compose)
$host = "db"; 
$user = "root"; 
$pass = ""; // Jika di docker-compose diatur root password, isi di sini
$db   = "simpeg";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>