<?php
// login.php (di root folder 'mengelola_pelanggan_wifi')

// PENTING: Memulai sesi di awal file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Memuat file konfigurasi koneksi database
require_once 'koneksi.php'; // Path langsung ke koneksi.php karena ada di folder yang sama

$error = ''; // Variabel untuk menyimpan pesan error
if (isset($_POST['login'])) {
    $username = trim($_POST['username']); // Gunakan trim untuk membersihkan spasi
    $password = $_POST['password'];

    // Menggunakan prepared statement untuk keamanan
    $sql = $conn->prepare("SELECT id, password_hash, role FROM users WHERE username = ?");
    $sql->bind_param('s', $username);
    $sql->execute();
    $res = $sql->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id']       = $row['id'];
            $_SESSION['role']          = $row['role'];
            $_SESSION['LAST_ACTIVITY'] = time(); // Set waktu aktivitas terakhir

            // Arahkan berdasarkan peran
            if ($row['role'] === 'admin') {
                header('Location: dashboard.php'); // Arahkan ke dashboard.php (di root)
            } else {
                // Jika ada dashboard user, arahkan ke sana
                // header('Location: user/dashboard_user.php');
                // Untuk saat ini, jika tidak ada dashboard user, arahkan ke pesan error atau keluar
                $error = "Akses ditolak: Anda bukan admin.";
                session_unset();
                session_destroy();
            }
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="container w-25">
        <h3 class="mb-3 text-center">Login</h3>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-2">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control rounded-md" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control rounded-md" required>
            </div>
            <button name="login" class="btn btn-primary w-100 rounded-md">Login</button>
            <div class="mt-3 text-center">
                Belum punya akun? <a href="regist.php">Daftar di sini</a>
            </div>
        </form>
    </div>
</body>
</html>