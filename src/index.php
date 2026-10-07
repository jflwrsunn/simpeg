<?php
// Mengaktifkan sesi session internal PHP
session_start();

// Pengalihan instan: Melempar pengguna langsung menuju halaman gerbang login utama
header("Location: login.php");
exit();
?>
