<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

// LOGIKA HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Hapus gambar fisik
    $q_img = mysqli_query($conn, "SELECT gambar FROM produk WHERE id=$id");
    $img = mysqli_fetch_assoc($q_img);
    if ($img && strpos($img['gambar'], 'assets/img/') !== false && file_exists("../" . $img['gambar'])) {
        unlink("../" . $img['gambar']);
    }

    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    mysqli_query($conn, "DELETE FROM laporan WHERE produk_id=$id");
    echo "<script>alert('Produk berhasil dihapus permanen.'); window.location='produk.php';</script>";
}

// LOGIKA SET PREMIUM (BARU)
if (isset($_GET['premium'])) {
    $id = (int)$_GET['premium'];
    $status = (int)$_GET['status']; // 1 = jadikan premium, 0 = jadikan biasa
    
    mysqli_query($conn, "UPDATE produk SET is_premium=$status WHERE id=$id");
    echo "<script>window.location='produk.php';</script>";
}

// Logika Pencarian & Pagination
$keyword = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = "WHERE 1=1";
if (!empty($keyword)) {
    $where .= " AND (judul LIKE '%$keyword%' OR penjual LIKE '%$keyword%' OR lokasi LIKE '%$keyword%')";
}

// Pagination
$batas = 10;
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

$data = mysqli_query($conn, "SELECT * FROM produk $where");
$jumlah_data = mysqli_num_rows($data);
$total_halaman = ceil($jumlah_data / $batas);

// Urutkan Premium di atas, baru ID terbaru
$products = mysqli_query($conn, "SELECT * FROM produk $where ORDER BY is_premium DESC, id DESC LIMIT $halaman_awal, $batas");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin NokenMART</title>
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
            <!-- Menu Aktif -->
            <a href="produk.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-box mr-3"></i> Kelola Produk</a>
            <a href="kategori.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-tags mr-3"></i> Kelola Kategori</a>
            <a href="banner.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-image mr-3"></i> Kelola Banner</a>
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h2 class="text-3xl font-bold text-gray-800">Daftar Semua Produk</h2>
            
            <!-- Form Cari -->
            <form action="" method="GET" class="relative">
                <input type="text" name="cari" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari barang atau penjual..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none w-64">
                <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Foto</th>
                            <th class="px-6 py-3">Nama Barang</th>
                            <th class="px-6 py-3">Harga</th>
                            <th class="px-6 py-3">Tipe</th> <!-- Kolom Baru -->
                            <th class="px-6 py-3">Penjual</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if($jumlah_data > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($products)): ?>
                            <tr class="hover:bg-gray-50 <?php echo $row['is_premium'] ? 'bg-yellow-50' : ''; ?>">
                                <td class="px-6 py-4">
                                    <img src="../<?php echo htmlspecialchars($row['gambar']); ?>" class="w-12 h-12 object-cover rounded border" onerror="this.src='https://placehold.co/100'">
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['judul']); ?>
                                    <div class="text-xs text-gray-500"><?php echo ucfirst($row['kategori']); ?></div>
                                </td>
                                <td class="px-6 py-4">Rp <?php echo number_format($row['harga']); ?></td>
                                <td class="px-6 py-4">
                                    <?php if($row['is_premium']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fa-solid fa-crown mr-1"></i> Premium
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">Reguler</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($row['penjual']); ?></td>
                                <td class="px-6 py-4 text-right">
                                    <!-- Tombol Premium -->
                                    <?php if($row['is_premium']): ?>
                                        <a href="produk.php?premium=<?php echo $row['id']; ?>&status=0" class="text-gray-400 hover:text-gray-600 mr-2" title="Hapus Premium"><i class="fa-regular fa-star"></i></a>
                                    <?php else: ?>
                                        <a href="produk.php?premium=<?php echo $row['id']; ?>&status=1" class="text-yellow-500 hover:text-yellow-600 mr-2" title="Jadikan Premium"><i class="fa-solid fa-star"></i></a>
                                    <?php endif; ?>

                                    <a href="../detail.php?id=<?php echo $row['id']; ?>" target="_blank" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fa-regular fa-eye"></i></a>
                                    <a href="produk.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus produk ini secara permanen?')" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                <span class="text-xs text-gray-500">Halaman <?php echo $halaman; ?> dari <?php echo $total_halaman; ?></span>
                <div class="flex gap-1">
                    <?php if($halaman > 1): ?><a href="?hal=<?php echo $previous; ?>&cari=<?php echo $keyword; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">Prev</a><?php endif; ?>
                    <?php if($halaman < $total_halaman): ?><a href="?hal=<?php echo $next; ?>&cari=<?php echo $keyword; ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-100">Next</a><?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>