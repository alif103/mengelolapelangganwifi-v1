<?php
// controllers/pelanggan_controller.php

// Path ke koneksi.php - Dihapus karena koneksi sudah di-require di file utama

// Fungsi untuk mendapatkan semua data pelanggan
function getAllPelanggan($conn) {
    // Menyesuaikan query dengan kolom yang benar: p.nama_pelanggan, pk.nama_paket, dan p.tanggal_jatuh_tempo
    $sql = "SELECT p.id, p.nama_pelanggan, p.alamat, pk.nama_paket, pk.harga, p.tanggal_pasang, p.tanggal_jatuh_tempo
            FROM pelanggan p
            JOIN paket pk ON p.paket_id = pk.id
            ORDER BY p.id DESC";
    $result = $conn->query($sql);
    if (!$result) {
        error_log("Query error in getAllPelanggan: " . $conn->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi untuk mendapatkan data pelanggan berdasarkan ID
function getPelangganById($conn, $id) {
    // REVISI DI SINI: Lakukan JOIN dengan tabel 'paket' untuk mendapatkan nama_paket dan harga
    $stmt = $conn->prepare("SELECT p.id, p.nama_pelanggan, p.alamat, p.paket_id, p.tanggal_pasang, p.tanggal_jatuh_tempo,
                                   pk.nama_paket, pk.harga
                            FROM pelanggan p
                            JOIN paket pk ON p.paket_id = pk.id
                            WHERE p.id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pelanggan = $result->fetch_assoc();
        $stmt->close();
        return $pelanggan;
    }
    return null;
}

// Fungsi untuk menambah pelanggan baru
// Menambahkan parameter dan kolom tanggal_jatuh_tempo
function addPelanggan($conn, $nama_pelanggan, $alamat, $paket_id, $tanggal_pasang, $tanggal_jatuh_tempo) {
    $stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, alamat, paket_id, tanggal_pasang, tanggal_jatuh_tempo) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        // Tipe data: string, string, integer, string, string -> 'ssiss'
        $stmt->bind_param('ssiss', $nama_pelanggan, $alamat, $paket_id, $tanggal_pasang, $tanggal_jatuh_tempo);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}

// Fungsi untuk mengupdate data pelanggan
// Menambahkan parameter dan kolom tanggal_jatuh_tempo
function updatePelanggan($conn, $id, $nama_pelanggan, $alamat, $paket_id, $tanggal_pasang, $tanggal_jatuh_tempo) {
    $stmt = $conn->prepare("UPDATE pelanggan SET nama_pelanggan = ?, alamat = ?, paket_id = ?, tanggal_pasang = ?, tanggal_jatuh_tempo = ? WHERE id = ?");
    if ($stmt) {
        // Tipe data: string, string, integer, string, string, integer -> 'ssissi'
        $stmt->bind_param('ssissi', $nama_pelanggan, $alamat, $paket_id, $tanggal_pasang, $tanggal_jatuh_tempo, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}

// Fungsi untuk menghapus pelanggan
function deletePelanggan($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM pelanggan WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}

// Fungsi untuk mendapatkan semua paket (untuk dropdown)
function getAllPaket($conn) {
    // Menggunakan kolom 'nama_paket' yang benar
    $sql = "SELECT id, nama_paket, harga FROM paket ORDER BY nama_paket ASC"; // Tambahkan 'harga' juga jika mungkin diperlukan di tempat lain
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>