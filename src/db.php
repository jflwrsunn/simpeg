<?php
$host = "localhost";
$user = "root"; 
$pass = ""; // Kosongkan atau sesuaikan dengan password MySQL di VM target
$db   = "simpeg";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke server database gagal: " . mysqli_connect_error());
}
?>