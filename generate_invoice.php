<?php
// generate_invoice.php (di root folder 'mengelola_pelanggan_wifi')

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';
require_once 'controllers/pelanggan_controller.php';
// require_once 'controllers/paket_controller.php'; // Tidak perlu jika getPelangganById sudah join

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$pelanggan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($pelanggan_id === 0) {
    die("ID Pelanggan tidak ditemukan.");
}

$pelanggan = getPelangganById($conn, $pelanggan_id);

if (!$pelanggan) {
    die("Data pelanggan tidak ditemukan atau tidak lengkap. Pastikan pelanggan_controller.php mengambil data paket.");
}

// Format tanggal untuk tampilan
$tanggal_cetak = date('d F Y'); // Contoh: 16 Juni 2025
$tanggal_jatuh_tempo = date('d F Y', strtotime($pelanggan['tanggal_jatuh_tempo']));

// Harga dan total
$harga_paket = $pelanggan['harga'] ?? 0; // Pastikan harga ada, default 0
$total_tagihan = $harga_paket; // Jika ada biaya lain, tambahkan di sini

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Tagihan Pelanggan - <?= htmlspecialchars($pelanggan['nama_pelanggan']) ?></title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa; /* Light background for screen view */
        }

        .invoice-container {
            max-width: 850px; /* Slightly wider for more content space */
            margin: 40px auto;
            background: #fff;
            padding: 50px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05); /* Subtle shadow for screen view */
            font-size: 13px; /* Slightly smaller for density */
            line-height: 1.6;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-size: 32px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 18px;
            color: #555;
            margin: 5px 0;
        }
        .header p {
            font-size: 13px;
            color: #777;
            margin: 2px 0;
        }

        /* Information Section (Flexbox for alignment) */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            line-height: 1.8;
        }
        .info-block {
            flex: 1; /* Take equal space */
            padding: 0 15px; /* Add some padding */
        }
        .info-block.company-info {
            text-align: left;
        }
        .info-block.customer-info {
            text-align: left;
        }
        .info-block.invoice-meta {
            text-align: right;
        }
        .info-block strong {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-size: 14px;
            border-bottom: 1px dashed #eee;
            padding-bottom: 5px;
        }

        /* Table Styles */
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .invoice-details th, .invoice-details td {
            border: 1px solid #e9ecef;
            padding: 12px 15px;
            text-align: left;
        }
        .invoice-details th {
            background-color: #f2f4f6;
            color: #495057;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }
        .invoice-details td {
            background-color: #fff;
            color: #343a40;
            vertical-align: top;
        }
        .invoice-details td.text-right {
            text-align: right;
            font-weight: bold;
            color: #212529;
        }

        /* Total Section */
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        .total-section .total-amount {
            display: inline-block;
            background-color: #e6f7ff; /* Light blue background for emphasis */
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 22px;
            font-weight: bold;
            color: #007bff; /* Blue text */
            border: 1px solid #b3d9ff;
        }
        .total-amount span {
            font-size: 16px; /* Smaller label for "Total Tagihan" */
            display: block;
            margin-bottom: 5px;
            color: #0056b3;
        }


        /* Footer Notes */
        .footer-notes {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #888;
            line-height: 1.8;
            border-top: 1px dashed #eee;
            padding-top: 20px;
        }
        .footer-notes p {
            margin: 5px 0;
        }

        /* Print Button Container */
        .print-button-container {
            text-align: center;
            margin: 30px auto;
            max-width: 850px;
            padding-bottom: 20px; /* Add some space below buttons */
        }
        .print-button-container .btn {
            padding: 10px 25px;
            font-size: 16px;
            margin: 0 10px;
            cursor: pointer;
            border-radius: 5px;
            border: none;
        }
        .print-button-container .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .print-button-container .btn-primary:hover {
            background-color: #0056b3;
        }
        .print-button-container .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .print-button-container .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Print Specific Styles */
        @media print {
            @page {
                size: A4; /* Set page size to A4 */
                margin: 15mm; /* Set consistent margins for the entire page */
            }

            body {
                background-color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
                font-size: 12px; /* Slightly smaller font for print */
            }
            .invoice-container {
                box-shadow: none;
                border: none;
                margin: 0 auto; /* Center the container on the A4 page */
                padding: 0; /* Remove internal padding, controlled by @page margin */
                width: 100%;
                max-width: 100%; /* Ensure it doesn't exceed A4 width */
            }
            .print-button-container, .no-print {
                display: none !important;
            }
            .header {
                margin-bottom: 20px;
                padding-bottom: 10px;
            }
            .header h1 {
                font-size: 24px;
            }
            .header h2 {
                font-size: 16px;
            }
            .info-section {
                margin-bottom: 20px;
                flex-direction: row; /* Ensure they stay side by side */
            }
            .info-block {
                padding: 0 5mm; /* Smaller padding for print */
            }
            .invoice-details table {
                margin-top: 15px;
            }
            .invoice-details th, .invoice-details td {
                padding: 8px 10px;
            }
            .total-section {
                margin-top: 20px;
            }
            .total-section .total-amount {
                padding: 10px 20px;
                font-size: 18px;
            }
            .footer-notes {
                margin-top: 30px;
                padding-top: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>Nota Tagihan WiFi</h1>
            <h2>BUMDES WIFI</h2>
            <p>Brawah, Tamansari, Kec. Mranggen, Kabupaten Demak, Jawa Tengah 59567</p>
            <p>Email: jsit@gmail.com | Telp: 0812-3456-7899</p>
        </div>

        <div class="info-section">
            <div class="info-block customer-info">
                <strong>Pelanggan:</strong>
                <p>
                    <b><?= htmlspecialchars($pelanggan['nama_pelanggan']) ?></b><br>
                    <?= htmlspecialchars($pelanggan['alamat']) ?>
                </p>
            </div>
            <div class="info-block invoice-meta">
                <strong>Detail Nota:</strong>
                <p>
                    Nomor Nota: <b>INV-<?= date('Ymd') . '-' . sprintf('%04d', $pelanggan['id']) ?></b><br>
                    Tanggal Cetak: <?= $tanggal_cetak ?><br>
                    Jatuh Tempo: <?= $tanggal_jatuh_tempo ?>
                </p>
            </div>
        </div>

        <div class="invoice-details">
            <table>
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Langganan Paket WiFi: <?= htmlspecialchars($pelanggan['nama_paket'] ?? 'N/A') ?></td>
                        <td class="text-right">Rp. <?= number_format($harga_paket, 2, ',', '.') ?></td>
                    </tr>
                    </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-amount">
                <span>Total Tagihan:</span>
                Rp. <?= number_format($total_tagihan, 2, ',', '.') ?>
            </div>
        </div>

        <div class="footer-notes">
            <p>Terima kasih telah menggunakan layanan kami!</p>
            <p>Pembayaran dapat dilakukan melalui transfer bank atau ke kantor kami.</p>
            <p>Harap bayar sebelum tanggal jatuh tempo untuk menghindari suspend.</p>
        </div>
    </div>

    <div class="print-button-container no-print">
        <button class="btn btn-primary" onclick="window.print()">Cetak Nota Ini</button>
        <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
    </div>

    </body>
</html>
