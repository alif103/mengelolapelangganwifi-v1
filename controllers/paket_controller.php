<?php
// controllers/paket_controller.php

// require_once __DIR__ . '/../koneksi.php'; // Path ke koneksi.php - Dihapus karena koneksi sudah di-require di file utama

// Fungsi untuk mendapatkan semua data paket
function getAllPaketData($conn) {
    // Menggunakan 'nama_paket' dari DB dan mengaliaskannya sebagai 'nama' untuk view
    $sql = "SELECT id, nama_paket AS nama, harga FROM paket ORDER BY nama_paket ASC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Fungsi untuk mendapatkan data paket berdasarkan ID
function getPaketById($conn, $id) {
    // Menggunakan 'nama_paket' dari DB dan mengaliaskannya sebagai 'nama' untuk view
    $stmt = $conn->prepare("SELECT id, nama_paket AS nama, harga FROM paket WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $paket = $result->fetch_assoc();
        $stmt->close();
        return $paket;
    }
    return null;
}

// Fungsi untuk menambah paket baru
function addPaket($conn, $nama, $harga) {
    // Menggunakan kolom 'nama_paket' yang benar di DB
    $stmt = $conn->prepare("INSERT INTO paket (nama_paket, harga) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param('sd', $nama, $harga); // 'sd' untuk string, double
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}

// Fungsi untuk mengupdate data paket
function updatePaket($conn, $id, $nama, $harga) {
    // Menggunakan kolom 'nama_paket' yang benar di DB
    $stmt = $conn->prepare("UPDATE paket SET nama_paket = ?, harga = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('sdi', $nama, $harga, $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}

// Fungsi untuk menghapus paket
function deletePaket($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM paket WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}
?>