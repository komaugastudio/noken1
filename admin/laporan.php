<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

// 1. LOGIKA HAPUS BARANG (Laporan Valid)
if (isset($_GET['ban_produk'])) {
    $id_produk = (int)$_GET['ban_produk'];
    
    // Hapus barang dan semua laporan terkait
    mysqli_query($conn, "DELETE FROM produk WHERE id=$id_produk");
    mysqli_query($conn, "DELETE FROM laporan WHERE produk_id=$id_produk");
    // Hapus dari wishlist juga agar bersih
    mysqli_query($conn, "DELETE FROM wishlist WHERE produk_id=$id_produk");

    echo "<script>alert('Barang berhasil diblokir/dihapus.'); window.location='laporan.php';</script>";
}

// 2. LOGIKA TOLAK LAPORAN (Laporan Palsu/Salah)
if (isset($_GET['tolak_laporan'])) {
    $id_laporan = (int)$_GET['tolak_laporan'];
    mysqli_query($conn, "DELETE FROM laporan WHERE id=$id_laporan");
    echo "<script>alert('Laporan ditolak (dihapus). Barang tetap aman.'); window.location='laporan.php';</script>";
}

// Ambil Semua Laporan
$query = "SELECT laporan.*, laporan.id as id_laporan, produk.judul, produk.penjual, produk.gambar 
          FROM laporan 
          LEFT JOIN produk ON laporan.produk_id = produk.id 
          ORDER BY laporan.id DESC";
$laporan = mysqli_query($conn, $query);
$total = mysqli_num_rows($laporan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Laporan - Admin NokenMART</title>
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
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan <?php if($total > 0): ?><span class="ml-1 bg-red-600 text-white text-[10px] px-2 rounded-full"><?php echo $total; ?></span><?php endif; ?></a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Laporan Masuk</h2>
            <div class="text-sm text-gray-500">Total: <strong><?php echo $total; ?></strong> laporan</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-red-50 text-red-800 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Barang Dilaporkan</th>
                            <th class="px-6 py-3">Detail Laporan</th>
                            <th class="px-6 py-3">Pelapor</th>
                            <th class="px-6 py-3 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if($total > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($laporan)): ?>
                            <tr class="hover:bg-red-50 transition">
                                <td class="px-6 py-4">
                                    <?php if($row['judul']): ?>
                                        <div class="flex items-center gap-3">
                                            <img src="../<?php echo htmlspecialchars($row['gambar']); ?>" class="w-12 h-12 rounded object-cover border border-gray-200" onerror="this.src='https://placehold.co/100'">
                                            <div>
                                                <a href="../detail.php?id=<?php echo $row['produk_id']; ?>" target="_blank" class="font-bold text-gray-900 hover:underline hover:text-red-600">
                                                    <?php echo htmlspecialchars($row['judul']); ?> <i class="fa-solid fa-arrow-up-right-from-square text-xs ml-1"></i>
                                                </a>
                                                <div class="text-xs text-gray-500">Penjual: <?php echo htmlspecialchars($row['penjual']); ?></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">Barang sudah dihapus</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-red-600 font-medium mb-1"><?php echo htmlspecialchars(explode(':', $row['alasan'])[0]); ?></div>
                                    <p class="text-gray-600 text-xs italic">
                                        "<?php echo htmlspecialchars($row['alasan']); ?>"
                                    </p>
                                    <div class="text-gray-400 text-[10px] mt-1"><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-xs">
                                    <?php echo htmlspecialchars($row['pelapor_email']); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <?php if($row['judul']): ?>
                                        <!-- Tombol Hapus Barang (Setujui Laporan) -->
                                        <a href="laporan.php?ban_produk=<?php echo $row['produk_id']; ?>" 
                                           onclick="return confirm('PERINGATAN: Barang ini akan dihapus permanen. Lanjutkan?')" 
                                           class="bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 text-xs font-bold shadow mr-2">
                                            <i class="fa-solid fa-gavel mr-1"></i> Blokir Barang
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Tombol Tolak Laporan (Hapus Laporan Saja) -->
                                    <a href="laporan.php?tolak_laporan=<?php echo $row['id_laporan']; ?>" 
                                       onclick="return confirm('Tolak laporan ini? Barang tidak akan dihapus.')" 
                                       class="text-gray-500 hover:text-gray-700 text-xs underline">
                                        Abaikan
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fa-regular fa-circle-check text-4xl text-green-500 mb-3"></i>
                                    <p>Tidak ada laporan masuk. Semua aman!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>