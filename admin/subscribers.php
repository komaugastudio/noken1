<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// HAPUS SUBSCRIBER
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM subscribers WHERE id=$id");
    echo "<script>alert('Email dihapus dari daftar berlangganan.'); window.location='subscribers.php';</script>";
}

// AMBIL DATA
$subs = mysqli_query($conn, "SELECT * FROM subscribers ORDER BY id DESC");
$total = mysqli_num_rows($subs);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Subscriber - Admin NokenMART</title>
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
            <a href="subscribers.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-rss mr-3"></i> Subscriber</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Daftar Pelanggan (Subscriber)</h2>
            <a href="kirim_broadcast.php" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-bold shadow transition">
                <i class="fa-regular fa-paper-plane mr-2"></i> Kirim Broadcast
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <span class="text-gray-600 font-medium">Total Email: <strong><?php echo $total; ?></strong></span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Alamat Email</th>
                            <th class="px-6 py-3">Tanggal Berlangganan</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        $no = 1;
                        if($total > 0):
                            while($row = mysqli_fetch_assoc($subs)): 
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500"><?php echo $no++; ?></td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                <a href="mailto:<?php echo $row['email']; ?>" class="hover:text-teal-600 hover:underline">
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?></td>
                            <td class="px-6 py-4 text-right">
                                <a href="subscribers.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus email ini?')" class="text-red-500 hover:text-red-700 px-3 py-1 rounded-lg border border-red-100 bg-red-50 text-xs">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pelanggan newsletter.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>