<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// VULNERABILITY: IDOR
// Parameter doc_id diambil dari URL dan langsung dipakai query tanpa mengecek kepemilikan user
if (isset($_GET['doc_id'])) {
    $doc_id = $_GET['doc_id'];

    $query = "SELECT * FROM dokumen WHERE id = $doc_id";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $doc = $result->fetch_assoc();
        
        // Simulasi pengunduhan dokumen rahasia
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $doc['nama_dokumen'] . '"');
        echo "=== CONFIDENTIAL BSSN DOCUMENT ===\n";
        echo "Nama Dokumen : " . $doc['nama_dokumen'] . "\n";
        echo "Pemilik ID   : " . $doc['user_id'] . "\n";
        echo "Isi Dokumen  : Ini adalah isi dokumen rahasia yang berhasil diakses melalui kerentanan IDOR.\n";
        exit();
    } else {
        echo "Dokumen tidak ditemukan.";
    }
} else {
    echo "Parameter doc_id tidak diberikan.";
}
?>