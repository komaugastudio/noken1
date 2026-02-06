<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - Admin NokenMART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white hidden md:block">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-teal-400">Noken<span class="text-yellow-500">ADMIN</span></h1>
        </div>
        <nav class="mt-6">
            <a href="dashboard.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gauge mr-3"></i> Dashboard</a>
            <a href="produk.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-box mr-3"></i> Kelola Produk</a>
            <a href="kategori.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-tags mr-3"></i> Kelola Kategori</a>
            <a href="banner.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-image mr-3"></i> Kelola Banner</a>
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <!-- Menu Aktif -->
            <a href="cetak.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Pusat Cetak Laporan</h2>
            <p class="text-gray-500">Pilih data yang ingin dicetak atau diekspor ke PDF</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Card 1: Produk -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
                <div class="w-12 h-12 bg-teal-100 text-teal-600 rounded-lg flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-box"></i>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-2">Data Produk</h3>
                <p class="text-gray-500 text-sm mb-6">Cetak daftar semua barang dagangan yang terdaftar.</p>
                
                <div class="space-y-2">
                    <a href="print.php?tipe=produk&status=semua" target="_blank" class="block w-full text-center py-2 border border-teal-500 text-teal-600 rounded-lg hover:bg-teal-50 font-medium transition">
                        Semua Produk
                    </a>
                    <a href="print.php?tipe=produk&status=terjual" target="_blank" class="block w-full text-center py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium transition">
                        Laporan Barang Terjual
                    </a>
                </div>
            </div>

            <!-- Card 2: Pengguna -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-2">Data Pengguna</h3>
                <p class="text-gray-500 text-sm mb-6">Cetak daftar pengguna dan penjual yang terdaftar di sistem.</p>
                
                <a href="print.php?tipe=users" target="_blank" class="block w-full text-center py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition">
                    Cetak Daftar User
                </a>
            </div>

            <!-- Card 3: Laporan Masuk -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-lg flex items-center justify-center text-2xl mb-4">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-2">Laporan Masuk</h3>
                <p class="text-gray-500 text-sm mb-6">Arsip laporan keluhan dari pengguna untuk evaluasi keamanan.</p>
                
                <a href="print.php?tipe=laporan" target="_blank" class="block w-full text-center py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">
                    Cetak Arsip Laporan
                </a>
            </div>

        </div>
    </main>
</div>
</body>
</html>