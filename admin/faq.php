<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// TAMBAH FAQ
if (isset($_POST['tambah'])) {
    $tanya = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
    $jawab = mysqli_real_escape_string($conn, $_POST['jawaban']);
    
    $query = "INSERT INTO faqs (pertanyaan, jawaban) VALUES ('$tanya', '$jawab')";
    if(mysqli_query($conn, $query)){
        echo "<script>alert('FAQ berhasil ditambahkan!'); window.location='faq.php';</script>";
    }
}

// UPDATE FAQ
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $tanya = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
    $jawab = mysqli_real_escape_string($conn, $_POST['jawaban']);
    
    $query = "UPDATE faqs SET pertanyaan='$tanya', jawaban='$jawab' WHERE id=$id";
    if(mysqli_query($conn, $query)){
        echo "<script>alert('FAQ berhasil diperbarui!'); window.location='faq.php';</script>";
    }
}

// HAPUS FAQ
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM faqs WHERE id=$id");
    echo "<script>alert('FAQ dihapus.'); window.location='faq.php';</script>";
}

// FETCH DATA EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM faqs WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($q);
}

$faqs = mysqli_query($conn, "SELECT * FROM faqs ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola FAQ - Admin NokenMART</title>
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
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            <!-- Menu Aktif -->
            <a href="faq.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-circle-question mr-3"></i> Kelola FAQ</a>
            
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="subscribers.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-rss mr-3"></i> Subscriber</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Bantuan (FAQ)</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Tambah/Edit -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-4">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2"><?php echo $edit_data ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru'; ?></h3>
                    <form action="" method="POST">
                        <?php if($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                <input type="text" name="pertanyaan" required value="<?php echo $edit_data ? htmlspecialchars($edit_data['pertanyaan']) : ''; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Jawaban</label>
                                <textarea name="jawaban" rows="5" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none"><?php echo $edit_data ? htmlspecialchars($edit_data['jawaban']) : ''; ?></textarea>
                            </div>
                            <div class="flex gap-2">
                                <?php if($edit_data): ?>
                                    <button type="submit" name="update" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-lg transition shadow"><i class="fa-solid fa-save mr-2"></i> Update</button>
                                    <a href="faq.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</a>
                                <?php else: ?>
                                    <button type="submit" name="tambah" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 rounded-lg transition shadow"><i class="fa-solid fa-plus mr-2"></i> Tambah</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Daftar FAQ -->
            <div class="lg:col-span-2 space-y-4">
                <?php while($row = mysqli_fetch_assoc($faqs)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <h4 class="font-bold text-gray-800 text-lg mb-2"><?php echo htmlspecialchars($row['pertanyaan']); ?></h4>
                    <p class="text-gray-600 text-sm leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100"><?php echo nl2br(htmlspecialchars($row['jawaban'])); ?></p>
                    <div class="mt-3 flex justify-end gap-3">
                        <a href="faq.php?edit=<?php echo $row['id']; ?>" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium"><i class="fa-solid fa-pen-to-square mr-1"></i> Edit</a>
                        <a href="faq.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus pertanyaan ini?')" class="text-red-500 hover:text-red-700 text-sm font-medium"><i class="fa-solid fa-trash mr-1"></i> Hapus</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>