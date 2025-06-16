<?php
// regist.php (Untuk pendaftaran admin pertama kali)
require_once 'koneksi.php';

$error = '';
$success = '';

// Cek apakah sudah ada admin terdaftar
$stmt_check_admin = $conn->prepare("SELECT id FROM users WHERE role = 'admin'");
$stmt_check_admin->execute();
$stmt_check_admin->store_result();
$admin_exists = $stmt_check_admin->num_rows > 0;
$stmt_check_admin->close();

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validasi input
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $error = "Semua field harus diisi.";
    } elseif ($password !== $password_confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        // Cek apakah username sudah ada
        $stmt_check_user = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check_user->bind_param('s', $username);
        $stmt_check_user->execute();
        $stmt_check_user->store_result();

        if ($stmt_check_user->num_rows > 0) {
            $error = "Username sudah digunakan.";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'admin'; // Hanya bisa mendaftar sebagai admin di sini

            // Insert user baru
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt_insert->bind_param('sss', $username, $password_hash, $role);

            if ($stmt_insert->execute()) {
                $success = "Pendaftaran admin berhasil! Silakan <a href='login.php'>Login</a>.";
            } else {
                $error = "Gagal mendaftar. Silakan coba lagi. Error: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt_check_user->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Registrasi Admin</h2>
        <?php if ($admin_exists && empty($success)): ?>
            <div class="alert alert-warning">Sudah ada admin terdaftar. Halaman ini sebaiknya hanya diakses untuk setup awal.</div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Konfirmasi Password:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Daftar Admin</button>
        </form>
        <p class="text-center" style="margin-top: 15px;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>