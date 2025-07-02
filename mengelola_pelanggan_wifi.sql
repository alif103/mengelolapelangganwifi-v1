-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jul 2025 pada 16.57
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mengelola_pelanggan_wifi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `paket`
--

CREATE TABLE `paket` (
  `id` int(11) NOT NULL,
  `nama_paket` varchar(100) NOT NULL,
  `harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `paket`
--

INSERT INTO `paket` (`id`, `nama_paket`, `harga`) VALUES
(1, 'Basic (10 Mbps)', 150000.00),
(2, 'Standard (25 Mbps)', 200000.00),
(3, 'Premium (50 Mbps)', 300000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `paket_id` int(11) NOT NULL,
  `tanggal_pasang` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama_pelanggan`, `alamat`, `paket_id`, `tanggal_pasang`, `tanggal_jatuh_tempo`) VALUES
(1, 'alip', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 1, '2025-06-01', '2025-07-01'),
(2, 'budi', 'Dusun Brawah, Tamansari, Mranggen, Demak\r\n', 2, '2025-06-08', '2025-07-08'),
(3, 'prio', 'Dusun Jetis, Tamansari, Mranggen, Demak', 1, '2025-06-01', '2025-07-01'),
(4, 'zidan', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 3, '2025-06-05', '2025-07-05'),
(5, 'Fajar Kurniawan', 'Dusun Jetis, Tamansari, Mranggen, Demak', 1, '2025-05-01', '2025-06-01'),
(6, 'Santi Wijayanti', 'Dusun Jetis, Tamansari, Mranggen, Demak', 2, '2025-05-02', '2025-06-02'),
(7, 'Bambang Susanto', 'Dusun Brawah, Tamansari, Mranggen, Demak', 3, '2025-05-03', '2025-06-03'),
(8, 'Nurul Hidayah', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 1, '2025-05-04', '2025-06-04'),
(9, 'Bayu Pratama', 'Dusun Jetis, Tamansari, Mranggen, Demak', 2, '2025-05-05', '2025-06-05'),
(10, 'Mega Anggraini', 'Dusun Brawah, Tamansari, Mranggen, Demak', 3, '2025-05-06', '2025-06-06'),
(11, 'Rizky Ramadhan', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 1, '2025-05-07', '2025-06-07'),
(12, 'Putri Rahayu', 'Dusun Jetis, Tamansari, Mranggen, Demak', 2, '2025-05-08', '2025-06-08'),
(13, 'Candra Kirana', 'Dusun Brawah, Tamansari, Mranggen, Demak', 3, '2025-05-09', '2025-06-09'),
(14, 'Dwi Santoso', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 1, '2025-05-10', '2025-06-10'),
(15, 'Eka Lestari', 'Dusun Jetis, Tamansari, Mranggen, Demak', 2, '2025-05-11', '2025-06-11'),
(16, 'Gita Permata', 'Dusun Brawah, Tamansari, Mranggen, Demak', 3, '2025-05-12', '2025-06-12'),
(17, 'Hari Setiawan', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 1, '2025-05-13', '2025-06-13'),
(18, 'Indah Sari', 'Dusun Jetis, Tamansari, Mranggen, Demak', 2, '2025-05-14', '2025-06-14'),
(19, 'Joko Widodo', 'Dusun Brawah, Tamansari, Mranggen, Demak', 3, '2025-05-15', '2025-06-15'),
(20, 'Kartika Putri', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 1, '2025-05-16', '2025-06-16'),
(21, 'Lukman Hakim', 'Dusun Jetis, Tamansari, Mranggen, Demak', 2, '2025-05-17', '2025-06-17'),
(22, 'Mita Wijaya', 'Dusun Brawah, Tamansari, Mranggen, Demak', 3, '2025-05-18', '2025-06-18'),
(23, 'Nugroho Adi', 'Dusun Sukorejo, Tamansari, Mranggen, Demak', 1, '2025-05-19', '2025-06-19'),
(24, 'Olivia Chandra', 'Dusun Jetis, Tamansari, Mranggen, Demak', 2, '2025-05-20', '2025-06-20'),
(25, 'Yoga Prasetyo', 'Dusun Brawah, Tamansari, Mranggen, Demak', 3, '2025-05-21', '2025-06-21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`) VALUES
(1, 'admin', '$2y$10$b5WnAmGI3N8.rxiJgQzbNeQj9wbpoLkfXT9WdAxRCuv7STA/LM7N2', 'admin'),
(2, 'admin1', '$2y$10$omo3O1UF9ALwv.RsDz.zzey3NtRTz9C61XeZkQFEfoZlb7ocgIWYS', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paket_id` (`paket_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `paket`
--
ALTER TABLE `paket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
