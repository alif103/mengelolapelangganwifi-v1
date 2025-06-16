<?php
// manage_paket.php (di root folder 'mengelola_pelanggan_wifi')

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';
require_once 'controllers/paket_controller.php'; // Memuat controller paket

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$message = '';

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Proses Aksi (Tambah, Edit, Hapus)
if ($action === 'delete' && $id > 0) {
    if (deletePaket($conn, $id)) {
        $message = '<div class="alert alert-success">Paket berhasil dihapus.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal menghapus paket.</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $harga = (float)$_POST['harga']; // Pastikan harga adalah float

    $id_form = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id_form > 0) {
        // Update Paket
        if (updatePaket($conn, $id_form, $nama, $harga)) {
            $message = '<div class="alert alert-success">Data paket berhasil diupdate.</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal mengupdate data paket.</div>';
        }
    } else {
        // Tambah Paket Baru
        if (addPaket($conn, $nama, $harga)) {
            $message = '<div class="alert alert-success">Paket baru berhasil ditambahkan.</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal menambahkan paket baru.</div>';
        }
    }
}

$current_paket = ['id' => '', 'nama' => '', 'harga' => ''];
if ($action === 'edit' && $id > 0) {
    $paket_to_edit = getPaketById($conn, $id);
    if ($paket_to_edit) {
        $current_paket = $paket_to_edit;
    }
}

$paket_data = getAllPaketData($conn); // Menggunakan fungsi dari paket_controller
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="dashboard.php">Admin Panel (Paket)</a>
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

        <h1>Kelola Data Paket</h1>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h3><?= empty($current_paket['id']) ? 'Tambah Paket Baru' : 'Edit Paket' ?></h3>
            </div>
            <div class="card-body">
                <form action="manage_paket.php" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($current_paket['id']) ?>">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Paket:</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($current_paket['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga:</label>
                        <input type="number" class="form-control" id="harga" name="harga" step="0.01" value="<?= htmlspecialchars($current_paket['harga']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                    <?php if (!empty($current_paket['id'])): ?>
                        <a href="manage_paket.php" class="btn btn-secondary">Batal Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Daftar Paket</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Paket</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($paket_data)): ?>
                                <tr><td colspan="4" class="text-center">Belum ada data paket.</td></tr>
                            <?php else: ?>
                                <?php foreach ($paket_data as $paket): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($paket['id']) ?></td>
                                        <td><?= htmlspecialchars($paket['nama']) ?></td>
                                        <td>Rp. <?= number_format($paket['harga'], 2, ',', '.') ?></td>
                                        <td>
                                            <a href="manage_paket.php?action=edit&id=<?= htmlspecialchars($paket['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="manage_paket.php?action=delete&id=<?= htmlspecialchars($paket['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus paket ini? Ini akan memengaruhi pelanggan yang menggunakan paket ini!');">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>