<?php
// dashboard.php (di root folder 'mengelola_pelanggan_wifi')

// Memastikan sesi sudah dimulai sebelum mengakses $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Memuat koneksi database
require_once 'koneksi.php'; // Path langsung ke koneksi.php

// Logika otentikasi dan pengecekan peran pengguna
// Jika 'user_id' tidak diatur dalam sesi atau peran bukan 'admin', arahkan ke halaman login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Logika pengelolaan timeout sesi (misal 15 menit)
$timeout = 900; // 15 menit dalam detik
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Perbarui waktu aktivitas terakhir

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_pelanggan.php">Kelola Pelanggan</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_paket.php">Kelola Paket</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <h1>Selamat Datang, Admin!</h1>
        <p>Anda telah login sebagai administrator. Berikut adalah ringkasan data Anda:</p>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <h4>Jumlah Pelanggan:</h4>
                    <?php
                    try {
                        $stmt = $conn->prepare("SELECT COUNT(*) AS total_pelanggan FROM pelanggan");
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();
                        echo htmlspecialchars($result['total_pelanggan']);
                    } catch (mysqli_sql_exception $e) {
                        echo "Error: " . htmlspecialchars($e->getMessage());
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <h4>Jumlah Paket:</h4>
                    <?php
                    try {
                        $stmt = $conn->prepare("SELECT COUNT(*) AS total_paket FROM paket");
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();
                        echo htmlspecialchars($result['total_paket']);
                    } catch (mysqli_sql_exception $e) {
                        echo "Error: " . htmlspecialchars($e->getMessage());
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <h4>Tanggal Saat Ini:</h4>
                    <?= date('d M Y') ?>
                </div>
            </div>
        </div>

        <p class="mt-4">Gunakan menu navigasi di atas untuk mengelola data pelanggan dan paket.</p>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>