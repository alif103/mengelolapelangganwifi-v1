<?php
// export_all_pelanggan.php (di root folder 'mengelola_pelanggan_wifi')

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';
require_once 'controllers/pelanggan_controller.php';
require_once 'controllers/paket_controller.php'; // Ensure this is included if needed for paket details

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$type = $_GET['type'] ?? 'print'; // Default to print if no type is specified

$pelanggan_data = getAllPelanggan($conn); // This function should join with paket to get package details

if ($type === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="data_pelanggan_' . date('Ymd_His') . '.xls"');
    header('Cache-Control: max-age=0');

    $output = fopen('php://output', 'w');

    // Kolom Header
    fputcsv($output, ['ID', 'Nama Pelanggan', 'Alamat', 'Paket WiFi', 'Harga Paket', 'Tanggal Pasang', 'Tanggal Jatuh Tempo'], "\t"); // Using tab for Excel compatibility

    // Data Pelanggan
    foreach ($pelanggan_data as $pelanggan) {
        fputcsv($output, [
            $pelanggan['id'],
            $pelanggan['nama_pelanggan'],
            $pelanggan['alamat'],
            $pelanggan['nama_paket'],
            $pelanggan['harga'],
            date('d M Y', strtotime($pelanggan['tanggal_pasang'])),
            date('d M Y', strtotime($pelanggan['tanggal_jatuh_tempo']))
        ], "\t");
    }

    fclose($output);
    exit;

} elseif ($type === 'print') {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cetak Data Pelanggan</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 100%;
                    margin: 0;
                    padding: 0;
                }
                .no-print {
                    display: none !important;
                }
            }
            body {
                font-size: 14px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                border: 1px solid #dee2e6;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f8f9fa;
            }
            h1 {
                text-align: center;
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="container mt-4">
            <h1 class="mb-4">Data Pelanggan WiFi</h1>
            <p class="text-end no-print">Tanggal Cetak: <?= date('d M Y H:i:s') ?></p>
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
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pelanggan_data)): ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data pelanggan untuk dicetak.</td></tr>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 no-print">
                <button class="btn btn-primary" onclick="window.print()">Cetak Halaman Ini</button>
                <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
} else {
    // Handle invalid type if necessary
    echo "Invalid export type.";
}
?>