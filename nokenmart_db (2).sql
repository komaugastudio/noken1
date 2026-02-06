-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Feb 2026 pada 22.21
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
-- Database: `nokenmart_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `subjudul` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `aktif` enum('ya','tidak') NOT NULL DEFAULT 'tidak',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `banners`
--

INSERT INTO `banners` (`id`, `judul`, `subjudul`, `gambar`, `aktif`, `created_at`) VALUES
(1, 'Pace, Mace, Cari Apa?', 'Platform jual beli aman dan terpercaya khusus wilayah Nabire.', 'assets/img/banner_698287865e62e.jpeg', 'ya', '2026-02-03 22:23:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `views` int(11) DEFAULT 0,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `blog`
--

INSERT INTO `blog` (`id`, `judul`, `slug`, `gambar`, `isi`, `penulis`, `views`, `tanggal`) VALUES
(1, 'Tips Aman Transaksi COD di Nabire', 'tips-aman-transaksi-cod-di-nabire', 'https://images.unsplash.com/photo-1556742049-0cfed4f7a07d?auto=format&fit=crop&w=800&q=80', 'COD atau Cash On Delivery adalah metode pembayaran yang paling aman saat ini. Pastikan Anda bertemu di tempat ramai...', 'Admin', 5, '2026-02-04 00:23:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `pertanyaan` varchar(255) NOT NULL,
  `jawaban` text NOT NULL,
  `urutan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `faqs`
--

INSERT INTO `faqs` (`id`, `pertanyaan`, `jawaban`, `urutan`) VALUES
(1, 'Apa itu NokenMART?', 'NokenMART adalah platform jual beli online khusus untuk wilayah Nabire dan Papua Tengah.', 1),
(2, 'Apakah pasang iklan gratis?', 'Ya, memasang iklan standar di NokenMART 100% GRATIS. Kami juga menyediakan fitur Premium berbayar untuk visibilitas lebih.', 2),
(3, 'Bagaimana cara transaksi aman?', 'Kami sangat menyarankan metode COD (Cash On Delivery). Bertemulah di tempat umum, cek barang, baru bayar.', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `galeri`
--

INSERT INTO `galeri` (`id`, `judul`, `deskripsi`, `gambar`, `tanggal`) VALUES
(1, 'Pasar Mama-Mama Papua', 'Suasana jual beli hasil bumi di pasar tradisional Nabire.', 'https://images.unsplash.com/photo-1605218427368-35b84ae9ac0e?auto=format&fit=crop&w=800&q=80', '2026-02-05 17:07:02'),
(2, 'Festival Noken', 'Pameran kerajinan tangan noken khas Papua.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Noken_Papua.jpg/640px-Noken_Papua.jpg', '2026-02-05 17:07:02'),
(3, 'Komunitas NokenMART', 'Pertemuan komunitas penjual online di Nabire.', 'https://images.unsplash.com/photo-1577415124269-fc1140a69e91?auto=format&fit=crop&w=800&q=80', '2026-02-05 17:07:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `slug`, `gambar`) VALUES
(1, 'Kendaraan', 'kendaraan', NULL),
(2, 'Elektronik', 'elektronik', NULL),
(3, 'Properti', 'properti', NULL),
(4, 'Hasil Bumi', 'hasil-bumi', NULL),
(5, 'Jasa Sewa', 'jasa-sewa', NULL),
(6, 'Lainnya', 'lainnya', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `komentar`
--

CREATE TABLE `komentar` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `isi_komentar` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `komentar`
--

INSERT INTO `komentar` (`id`, `produk_id`, `user_email`, `nama_user`, `isi_komentar`, `tanggal`) VALUES
(1, 6, 'budi@gmail.com', 'Pak Budi', 'berapa', '2026-02-03 23:08:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `pelapor_email` varchar(100) NOT NULL,
  `alasan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `aksi` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `logs`
--

INSERT INTO `logs` (`id`, `user_email`, `aksi`, `detail`, `ip_address`, `tanggal`) VALUES
(1, 'budi@gmail.com', 'Login', 'User budi@gmail.com berhasil masuk ke sistem.', '::1', '2026-02-05 21:18:41'),
(2, 'admin@nokenmart.com', 'Login', 'User admin@nokenmart.com berhasil masuk ke sistem.', '::1', '2026-02-05 21:21:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `status` enum('belum_dibaca','sudah_dibaca') NOT NULL DEFAULT 'belum_dibaca',
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `isi` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pages`
--

INSERT INTO `pages` (`id`, `judul`, `slug`, `isi`, `updated_at`) VALUES
(1, 'Tentang Kami', 'tentang-kami', '<h2>Tentang NokenMART</h2><p>NokenMART adalah platform jual beli online kebanggaan masyarakat Nabire...</p>', '2026-02-05 17:30:09'),
(2, 'Kebijakan Privasi', 'kebijakan-privasi', '<h2>Kebijakan Privasi</h2><p>Kami menjaga data Anda dengan aman...</p>', '2026-02-05 17:30:09'),
(3, 'Syarat & Ketentuan', 'syarat-ketentuan', '<h2>Syarat & Ketentuan</h2><p>Dengan menggunakan layanan ini, Anda setuju...</p>', '2026-02-05 17:30:09'),
(4, 'Tips Transaksi Aman', 'tips-keamanan', '\r\n<h3>Panduan Aman Berbelanja di NokenMART</h3>\r\n<p>Keamanan adalah prioritas utama kami. Berikut adalah beberapa tips untuk menghindari penipuan:</p>\r\n\r\n<h4>1. Utamakan COD (Cash On Delivery)</h4>\r\n<p>Metode paling aman adalah bertemu langsung dengan penjual di tempat umum yang ramai. Cek barangnya, pastikan sesuai deskripsi, baru bayar.</p>\r\n\r\n<h4>2. Jangan Transfer Uang Duluan</h4>\r\n<p>Hindari mentransfer uang muka (DP) atau pembayaran penuh sebelum barang Anda terima, kecuali Anda benar-benar mengenal penjual tersebut.</p>\r\n\r\n<h4>3. Cek Profil Penjual</h4>\r\n<p>Lihat apakah penjual memiliki lencana <strong>Centang Biru (Terverifikasi)</strong>. Cek juga ulasan dan rating dari pembeli lain di profil toko mereka.</p>\r\n\r\n<h4>4. Waspada Harga Terlalu Murah</h4>\r\n<p>Jika harga barang jauh di bawah pasaran dan penjual mendesak untuk segera transfer, harap berhati-hati.</p>\r\n\r\n<h4>5. Simpan Bukti Chat</h4>\r\n<p>Selalu berkomunikasi melalui WhatsApp agar ada riwayat percakapan yang jelas jika terjadi sengketa.</p>\r\n', '2026-02-05 18:59:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `nama_website` varchar(100) NOT NULL,
  `email_admin` varchar(100) NOT NULL,
  `wa_admin` varchar(20) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `deskripsi_footer` text NOT NULL,
  `mode_maintenance` enum('ya','tidak') NOT NULL DEFAULT 'tidak',
  `link_facebook` varchar(255) DEFAULT '#',
  `link_instagram` varchar(255) DEFAULT '#',
  `link_tiktok` varchar(255) DEFAULT '#'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_website`, `email_admin`, `wa_admin`, `alamat`, `deskripsi_footer`, `mode_maintenance`, `link_facebook`, `link_instagram`, `link_tiktok`) VALUES
(1, 'NokenMART', 'admin@nokenmart.com', '628123456789', 'Jl. Merdeka No. 45, Oyehe, Nabire, Papua Tengah', 'Membantu UMKM dan warga Nabire untuk bertransaksi dengan mudah, aman, dan cepat.', 'tidak', '#', '#', '#');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengunjung`
--

CREATE TABLE `pengunjung` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengunjung`
--

INSERT INTO `pengunjung` (`id`, `ip_address`, `tanggal`, `waktu`) VALUES
(1, '::1', '2026-02-05', '2026-02-05 18:54:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesan`
--

CREATE TABLE `pesan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subjek` varchar(200) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `gambar` text NOT NULL,
  `penjual` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('aktif','terjual') NOT NULL DEFAULT 'aktif',
  `views` int(11) NOT NULL DEFAULT 0,
  `waktu_sundul` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `diskon` int(11) NOT NULL DEFAULT 0,
  `kondisi` enum('Baru','Bekas') NOT NULL DEFAULT 'Bekas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `judul`, `deskripsi`, `harga`, `kategori`, `lokasi`, `gambar`, `penjual`, `whatsapp`, `tanggal_upload`, `status`, `views`, `waktu_sundul`, `is_premium`, `diskon`, `kondisi`) VALUES
(6, 'sewa kursi', 'jasa sewa kursih paket lengkap', 250.00, 'sewa', 'Bumi Wonorejo', 'assets/img/69827df6a71f2.jpeg', 'budi@gmail.com', '6281344556677', '2026-02-03 23:00:06', 'aktif', 20, '2026-02-05 18:21:00', 1, 0, 'Bekas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekening`
--

CREATE TABLE `rekening` (
  `id` int(11) NOT NULL,
  `nama_bank` varchar(50) NOT NULL,
  `nomor_rekening` varchar(50) NOT NULL,
  `atas_nama` varchar(100) NOT NULL,
  `logo_bank` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rekening`
--

INSERT INTO `rekening` (`id`, `nama_bank`, `nomor_rekening`, `atas_nama`, `logo_bank`) VALUES
(1, 'Bank Papua', '123-456-7890', 'NokenMART Official', NULL),
(2, 'BRI', '0000-01-000000-50-0', 'NokenMART Official', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `subscribers`
--

CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `subscribers`
--

INSERT INTO `subscribers` (`id`, `email`, `tanggal`) VALUES
(1, 'melkiasbobi@gmail.com', '2026-02-04 05:38:12'),
(2, 'komaugastudio@gmail.com', '2026-02-04 05:41:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `email_pembeli` varchar(100) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `email_penjual` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `komentar` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ulasan`
--

INSERT INTO `ulasan` (`id`, `email_pembeli`, `nama_pembeli`, `email_penjual`, `rating`, `komentar`, `tanggal`) VALUES
(1, 'komaugastudio@gmail.com', 'Melkias Bobi', 'budi@gmail.com', 4, 'sangat memuaskan', '2026-02-04 05:02:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','penjual','pembeli','user') NOT NULL DEFAULT 'pembeli',
  `whatsapp` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `foto_ktp` varchar(255) DEFAULT NULL,
  `foto_selfie` varchar(255) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `whatsapp`, `created_at`, `is_verified`, `foto_ktp`, `foto_selfie`, `foto_profil`) VALUES
(1, 'Administrator', 'admin@nokenmart.com', '$2y$10$Sy3HN8JU2nDgusRpZ7.Yv.kSdfC24A/LiiwWk7nZDqHI2EaZwcytO', 'admin', '628123456789', '2026-02-03 21:34:43', 0, NULL, NULL, NULL),
(2, 'Mama Papua', 'mama@gmail.com', '$2y$10$Z1PQ/uUo7rhitwPKbGc7nu5XYGgIVCxug7fksiaG5L.4BjjkslgyO', 'user', '6282111223344', '2026-02-03 21:34:43', 1, NULL, NULL, NULL),
(3, 'Pak Budi', 'budi@gmail.com', '$2y$10$SPrf79.ORmx64G6GqxefEeOk63vCchVc8zjmVKSpaROwXnwVtJ7Gi', 'user', '6281344556677', '2026-02-03 21:34:43', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `wilayah`
--

CREATE TABLE `wilayah` (
  `id` int(11) NOT NULL,
  `nama_wilayah` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wilayah`
--

INSERT INTO `wilayah` (`id`, `nama_wilayah`) VALUES
(1, 'Nabire (Kota)'),
(2, 'Teluk Kimi'),
(3, 'Makimi'),
(4, 'Wanggar'),
(5, 'Yaro'),
(6, 'Yaur'),
(7, 'Siriwo'),
(8, 'Teluk Umar'),
(9, 'Napan'),
(10, 'Menou'),
(11, 'Dipa'),
(12, 'Uwapa'),
(13, 'Moora'),
(14, 'Wapoga'),
(15, 'Kepulauan Moora');

-- --------------------------------------------------------

--
-- Struktur dari tabel `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_komentar_produk` (`produk_id`);

--
-- Indeks untuk tabel `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengunjung`
--
ALTER TABLE `pengunjung`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesan`
--
ALTER TABLE `pesan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjual` (`penjual`),
  ADD KEY `penjual_2` (`penjual`),
  ADD KEY `penjual_3` (`penjual`),
  ADD KEY `penjual_4` (`penjual`);

--
-- Indeks untuk tabel `rekening`
--
ALTER TABLE `rekening`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `email_2` (`email`),
  ADD KEY `email_3` (`email`),
  ADD KEY `email_4` (`email`),
  ADD KEY `email_5` (`email`);

--
-- Indeks untuk tabel `wilayah`
--
ALTER TABLE `wilayah`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_wishlist_produk` (`produk_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `komentar`
--
ALTER TABLE `komentar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengunjung`
--
ALTER TABLE `pengunjung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pesan`
--
ALTER TABLE `pesan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `rekening`
--
ALTER TABLE `rekening`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `wilayah`
--
ALTER TABLE `wilayah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `komentar`
--
ALTER TABLE `komentar`
  ADD CONSTRAINT `fk_komentar_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_user` FOREIGN KEY (`penjual`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
