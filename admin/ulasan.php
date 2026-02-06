<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// HAPUS ULASAN
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM ulasan WHERE id=$id");
    echo "<script>alert('Ulasan dihapus.'); window.location='ulasan.php';</script>";
}

// AMBIL DATA ULASAN
// Di sini kita tidak perlu join ke produk, tapi ke user jika ingin detail lebih (opsional)
// Karena tabel ulasan sudah menyimpan nama_pembeli dan email_penjual
$query = "SELECT * FROM ulasan ORDER BY id DESC";
$ulasan = mysqli_query($conn, $query);
$total = mysqli_num_rows($ulasan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ulasan - Admin NokenMART</title>
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
            <a href="wilayah.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-map-location-dot mr-3"></i> Kelola Wilayah</a>
            <a href="banner.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-image mr-3"></i> Kelola Banner</a>
            <a href="galeri.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-images mr-3"></i> Kelola Galeri</a>
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            
            <a href="komentar.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-comments mr-3"></i> Kelola Diskusi</a>
            
            <!-- Menu Aktif -->
            <a href="ulasan.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-star mr-3"></i> Kelola Ulasan</a>
            
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="subscribers.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-rss mr-3"></i> Subscriber</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="backup.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-database mr-3"></i> Backup Data</a>
            <a href="logs.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-clock-rotate-left mr-3"></i> Riwayat Aktivitas</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            <a href="faq.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-circle-question mr-3"></i> Kelola FAQ</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Ulasan & Rating</h2>
            <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-sm font-semibold"><?php echo $total; ?> Ulasan</span>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Pembeli</th>
                            <th class="px-6 py-3">Rating</th>
                            <th class="px-6 py-3">Ulasan</th>
                            <th class="px-6 py-3">Penjual (Tujuan)</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if($total > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($ulasan)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-900">
                                    <?php echo htmlspecialchars($row['nama_pembeli']); ?>
                                    <div class="text-xs text-gray-500 font-normal"><?php echo htmlspecialchars($row['email_pembeli']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex text-yellow-400 text-xs">
                                        <?php for($i=0; $i<$row['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                    </div>
                                    <span class="text-xs text-gray-400">(<?php echo $row['rating']; ?>/5)</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 italic">
                                    "<?php echo htmlspecialchars($row['komentar']); ?>"
                                    <div class="text-[10px] text-gray-400 mt-1"><?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?></div>
                                </td>
                                <td class="px-6 py-4 text-xs text-blue-600">
                                    <?php echo htmlspecialchars($row['email_penjual']); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="ulasan.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus ulasan ini?')" class="text-red-500 hover:text-red-700 px-3 py-1 rounded-lg border border-red-100 bg-red-50 text-xs">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada ulasan masuk.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>