<?php
// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'presensi_mhs';

// Membuat koneksi
try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    // Set error mode ke exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set karakter encoding ke UTF-8
    $conn->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>