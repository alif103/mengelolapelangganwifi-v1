<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$database = "mengelola_pelanggan_wifi"; 

$conn = new mysqli($host, $user, $pass, $database);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>