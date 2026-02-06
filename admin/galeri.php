<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// TAMBAH FOTO
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/galeri/";
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $name_file = "galeri_" . uniqid() . "." . $ext;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $name_file)) {
            $gambar = "assets/img/galeri/" . $name_file;
        }
    }

    $query = "INSERT INTO galeri (judul, deskripsi, gambar) VALUES ('$judul', '$deskripsi', '$gambar')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Foto berhasil ditambahkan!'); window.location='galeri.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah foto.');</script>";
    }
}

// HAPUS FOTO
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $q = mysqli_query($conn, "SELECT gambar FROM galeri WHERE id=$id");
    $data = mysqli_fetch_assoc($q);
    
    // Hapus file fisik jika ada di server lokal
    if (strpos($data['gambar'], 'assets/img/') !== false && file_exists("../" . $data['gambar'])) {
        unlink("../" . $data['gambar']);
    }
    
    mysqli_query($conn, "DELETE FROM galeri WHERE id=$id");
    echo "<script>alert('Foto dihapus.'); window.location='galeri.php';</script>";
}

$galeri = mysqli_query($conn, "SELECT * FROM galeri ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - Admin NokenMART</title>
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
            <!-- Menu Aktif -->
            <a href="galeri.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-images mr-3"></i> Kelola Galeri</a>
            
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
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
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Galeri Foto</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Upload -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-4">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Upload Foto Baru</h3>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Judul Foto</label>
                                <input type="text" name="judul" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Deskripsi Singkat</label>
                                <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">File Foto</label>
                                <input type="file" name="gambar" required accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            </div>
                            <button type="submit" name="tambah" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 rounded-lg transition shadow">
                                <i class="fa-solid fa-upload mr-2"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Grid Foto -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php while($row = mysqli_fetch_assoc($galeri)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group relative">
                        <!-- Cek apakah gambar URL eksternal atau lokal -->
                        <img src="<?php echo strpos($row['gambar'], 'http') === 0 ? $row['gambar'] : '../' . $row['gambar']; ?>" class="w-full h-40 object-cover">
                        
                        <div class="p-3">
                            <h4 class="font-bold text-gray-800 text-sm truncate"><?php echo htmlspecialchars($row['judul']); ?></h4>
                            <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                        </div>
                        
                        <!-- Tombol Hapus -->
                        <a href="galeri.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus foto ini?')" class="absolute top-2 right-2 bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center shadow hover:bg-red-700 transition opacity-0 group-hover:opacity-100">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>