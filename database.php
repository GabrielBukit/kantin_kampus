<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kantin_kampus";

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset biar aman (WAJIB)
$conn->set_charset("utf8mb4");
?>