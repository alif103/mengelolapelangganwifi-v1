<?php
// manage_pelanggan.php (di root folder 'mengelola_pelanggan_wifi')

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';
require_once 'controllers/pelanggan_controller.php';
require_once 'controllers/paket_controller.php'; // Make sure this is included for getAllPaket

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Mengarahkan ke login.php di root folder
    header('Location: login.php');
    exit;
}

$message = '';

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- Logika untuk DELETE data pelanggan ---
if ($action === 'delete' && $id > 0) {
    if (deletePelanggan($conn, $id)) {
        $message = '<div class="alert alert-success">Pelanggan berhasil dihapus.</div>';
    } else {
        $message = '<div class="alert alert-danger">Gagal menghapus pelanggan.</div>';
    }
}

// --- Logika untuk CREATE (Tambah) atau UPDATE (Edit) data pelanggan ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = htmlspecialchars(trim($_POST['nama_pelanggan']));
    $alamat = htmlspecialchars(trim($_POST['alamat']));
    $paket_id = (int)$_POST['paket_id'];
    $tanggal_pasang = htmlspecialchars(trim($_POST['tanggal_pasang']));
    $tanggal_jatuh_tempo = htmlspecialchars(trim($_POST['tanggal_jatuh_tempo']));

    $id_form = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id_form > 0) {
        // Jika ada ID, lakukan UPDATE (menggunakan fungsi yang sudah diperbaiki)
        if (updatePelanggan($conn, $id_form, $nama_pelanggan, $alamat, $paket_id, $tanggal_pasang, $tanggal_jatuh_tempo)) {
            $message = '<div class="alert alert-success">Data pelanggan berhasil diupdate.</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal mengupdate data pelanggan.</div>';
        }
    } else {
        // Jika tidak ada ID, lakukan INSERT (menggunakan fungsi yang sudah diperbaiki)
        if (addPelanggan($conn, $nama_pelanggan, $alamat, $paket_id, $tanggal_pasang, $tanggal_jatuh_tempo)) {
            $message = '<div class="alert alert-success">Pelanggan baru berhasil ditambahkan.</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal menambahkan pelanggan baru.</div>';
        }
    }
}

// Inisialisasi variabel untuk form
$current_pelanggan = [
    'id' => '',
    'nama_pelanggan' => '',
    'alamat' => '',
    'paket_id' => '',
    'tanggal_pasang' => '',
    'tanggal_jatuh_tempo' => ''
];

if ($action === 'edit' && $id > 0) {
    $pelanggan_to_edit = getPelangganById($conn, $id);
    if ($pelanggan_to_edit) {
        $current_pelanggan = $pelanggan_to_edit;
    }
}

$pelanggan_data = getAllPelanggan($conn);
$paket_list = getAllPaket($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="dashboard.php">Admin Panel (Pelanggan)</a>
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

        <h1>Kelola Data Pelanggan</h1>
        <?php echo $message; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h3><?= empty($current_pelanggan['id']) ? 'Tambah Pelanggan Baru' : 'Edit Pelanggan' ?></h3>
            </div>
            <div class="card-body">
                <form action="manage_pelanggan.php" method="POST">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($current_pelanggan['id']) ?>">
                    <div class="mb-3">
                        <label for="nama_pelanggan" class="form-label">Nama Pelanggan:</label>
                        <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" value="<?= htmlspecialchars($current_pelanggan['nama_pelanggan']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat:</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($current_pelanggan['alamat']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="paket_id" class="form-label">Paket WiFi:</label>
                        <select class="form-select" id="paket_id" name="paket_id" required>
                            <option value="">Pilih Paket</option>
                            <?php foreach ($paket_list as $paket): ?>
                                <option value="<?= htmlspecialchars($paket['id']) ?>"
                                    <?= ($current_pelanggan['paket_id'] == $paket['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($paket['nama_paket']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_pasang" class="form-label">Tanggal Pasang:</label>
                        <input type="date" class="form-control" id="tanggal_pasang" name="tanggal_pasang" value="<?= htmlspecialchars($current_pelanggan['tanggal_pasang']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_jatuh_tempo" class="form-label">Tanggal Jatuh Tempo:</label>
                        <input type="date" class="form-control" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" value="<?= htmlspecialchars($current_pelanggan['tanggal_jatuh_tempo']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                    <?php if (!empty($current_pelanggan['id'])): ?>
                        <a href="manage_pelanggan.php" class="btn btn-secondary">Batal Edit</a>
                    <?php endif; ?>
                    <a href="export_all_pelanggan.php?type=excel" class="btn btn-success">Export ke Excel</a>
                    <a href="export_all_pelanggan.php?type=print" class="btn btn-info" target="_blank">Cetak Semua Data</a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Daftar Pelanggan</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Pelanggan</th>
                                <th>Alamat</th>
                                <th>Paket WiFi</th>
                                <th>Harga Paket</th>
                                <th>Tanggal Pasang</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pelanggan_data)): ?>
                                <tr><td colspan="8" class="text-center">Belum ada data pelanggan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pelanggan_data as $pelanggan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pelanggan['id']) ?></td>
                                        <td><?= htmlspecialchars($pelanggan['nama_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($pelanggan['alamat']) ?></td>
                                        <td><?= htmlspecialchars($pelanggan['nama_paket']) ?></td>
                                        <td>Rp. <?= number_format($pelanggan['harga'], 2, ',', '.') ?></td>
                                        <td><?= htmlspecialchars(date('d M Y', strtotime($pelanggan['tanggal_pasang']))) ?></td>
                                        <td><?= htmlspecialchars(date('d M Y', strtotime($pelanggan['tanggal_jatuh_tempo']))) ?></td>
                                        <td>
                                            <a href="manage_pelanggan.php?action=edit&id=<?= htmlspecialchars($pelanggan['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="manage_pelanggan.php?action=delete&id=<?= htmlspecialchars($pelanggan['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pelanggan ini?');">Hapus</a>
                                            <a href="generate_invoice.php?id=<?= htmlspecialchars($pelanggan['id']) ?>&type=pdf" class="btn btn-info btn-sm" target="_blank">Nota PDF</a>
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