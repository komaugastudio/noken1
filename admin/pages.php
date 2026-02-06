<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// LOGIKA UPDATE HALAMAN
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    
    $query = "UPDATE pages SET judul='$judul', isi='$isi' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Halaman berhasil diperbarui!'); window.location='pages.php';</script>";
    } else {
        echo "<script>alert('Gagal update.');</script>";
    }
}

// LOGIKA FETCH DATA EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM pages WHERE id=$id_edit");
    if(mysqli_num_rows($q_edit) > 0){
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

$pages = mysqli_query($conn, "SELECT * FROM pages ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Halaman - Admin NokenMART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .ck-editor__editable_inline { min-height: 400px; }
    </style>
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
            <a href="pages.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-file-lines mr-3"></i> Kelola Halaman</a>
            
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="subscribers.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-rss mr-3"></i> Subscriber</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            <a href="faq.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-circle-question mr-3"></i> Kelola FAQ</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Halaman Statis</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Daftar Halaman -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-700">Daftar Halaman</div>
                    <ul class="divide-y divide-gray-100">
                        <?php while($row = mysqli_fetch_assoc($pages)): ?>
                        <li>
                            <a href="pages.php?edit=<?php echo $row['id']; ?>" class="block p-4 hover:bg-teal-50 transition flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($row['judul']); ?></h4>
                                    <p class="text-xs text-gray-500">Slug: /<?php echo htmlspecialchars($row['slug']); ?>.php</p>
                                </div>
                                <i class="fa-solid fa-chevron-right text-gray-300"></i>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Form Edit -->
            <div class="lg:col-span-2">
                <?php if($edit_data): ?>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Edit Halaman: <?php echo htmlspecialchars($edit_data['judul']); ?></h3>
                    <form action="" method="POST">
                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Judul Halaman</label>
                                <input type="text" name="judul" required value="<?php echo htmlspecialchars($edit_data['judul']); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Konten Halaman</label>
                                <textarea name="isi" id="editor"><?php echo $edit_data['isi']; ?></textarea>
                            </div>
                            
                            <div class="flex gap-2 pt-2">
                                <button type="submit" name="update" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 rounded-lg transition shadow">
                                    <i class="fa-solid fa-save mr-2"></i> Simpan Perubahan
                                </button>
                                <a href="pages.php" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="bg-white p-12 rounded-xl shadow-sm border border-gray-200 text-center">
                    <i class="fa-regular fa-file-lines text-6xl text-gray-200 mb-4"></i>
                    <p class="text-gray-500">Pilih halaman di sebelah kiri untuk mulai mengedit.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
    if(document.querySelector('#editor')) {
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                placeholder: 'Tulis konten halaman di sini...'
            })
            .catch(error => { console.error(error); });
    }
</script>

</body>
</html>