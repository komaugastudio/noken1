<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// LOGIKA SIMPAN PERUBAHAN
if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_website']);
    $email = mysqli_real_escape_string($conn, $_POST['email_admin']);
    $wa = mysqli_real_escape_string($conn, $_POST['wa_admin']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $desc = mysqli_real_escape_string($conn, $_POST['deskripsi_footer']);
    
    // Update Mode Maintenance
    $maintenance = isset($_POST['mode_maintenance']) ? 'ya' : 'tidak';

    // Update Sosmed (Baru)
    $fb = mysqli_real_escape_string($conn, $_POST['link_facebook']);
    $ig = mysqli_real_escape_string($conn, $_POST['link_instagram']);
    $tt = mysqli_real_escape_string($conn, $_POST['link_tiktok']);

    $query = "UPDATE pengaturan SET 
              nama_website='$nama', 
              email_admin='$email', 
              wa_admin='$wa', 
              alamat='$alamat', 
              deskripsi_footer='$desc',
              mode_maintenance='$maintenance',
              link_facebook='$fb',
              link_instagram='$ig',
              link_tiktok='$tt'
              WHERE id=1";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pengaturan berhasil disimpan!'); window.location='pengaturan_website.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan.');</script>";
    }
}

// AMBIL DATA
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Website - Admin NokenMART</title>
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
            <a href="rekening.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-wallet mr-3"></i> Kelola Rekening</a>
            <a href="komentar.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-comments mr-3"></i> Kelola Komentar</a>
            <a href="ulasan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-star mr-3"></i> Kelola Ulasan</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="subscribers.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-rss mr-3"></i> Subscriber</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="backup.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-database mr-3"></i> Backup Data</a>
            <a href="logs.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-clock-rotate-left mr-3"></i> Riwayat Aktivitas</a>
            <a href="pages.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-file-lines mr-3"></i> Kelola Halaman</a>
            
            <!-- Menu Aktif -->
            <a href="pengaturan_website.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            <a href="faq.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-circle-question mr-3"></i> Kelola FAQ</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Pengaturan Website</h2>
        </div>

        <div class="max-w-2xl">
            <!-- Alert Status Maintenance -->
            <?php if($data['mode_maintenance'] == 'ya'): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    <div>
                        <p class="font-bold">Mode Perbaikan Sedang AKTIF!</p>
                        <p class="text-sm">Website saat ini tertutup untuk pengunjung umum.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <form action="" method="POST">
                    <div class="space-y-6">
                        
                        <!-- Toggle Maintenance Mode -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center justify-between">
                            <div>
                                <label class="font-bold text-gray-700 block">Mode Perbaikan (Maintenance)</label>
                                <p class="text-xs text-gray-500 mt-1">Aktifkan jika website sedang dalam perbaikan. Hanya Admin yang bisa mengakses.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="mode_maintenance" value="ya" class="sr-only peer" <?php echo $data['mode_maintenance'] == 'ya' ? 'checked' : ''; ?>>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Website / Brand</label>
                            <input type="text" name="nama_website" value="<?php echo htmlspecialchars($data['nama_website']); ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Admin</label>
                                <input type="text" name="wa_admin" value="<?php echo htmlspecialchars($data['wa_admin']); ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                                <p class="text-xs text-gray-500 mt-1">Format: 628xxx (tanpa + atau 0)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Bantuan</label>
                                <input type="email" name="email_admin" value="<?php echo htmlspecialchars($data['email_admin']); ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                        </div>

                        <!-- INPUT SOSMED BARU -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-brands fa-facebook text-blue-600"></i> Facebook URL</label>
                                <input type="text" name="link_facebook" value="<?php echo isset($data['link_facebook']) ? htmlspecialchars($data['link_facebook']) : '#'; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none text-xs" placeholder="https://facebook.com/...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-brands fa-instagram text-pink-600"></i> Instagram URL</label>
                                <input type="text" name="link_instagram" value="<?php echo isset($data['link_instagram']) ? htmlspecialchars($data['link_instagram']) : '#'; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none text-xs" placeholder="https://instagram.com/...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fa-brands fa-tiktok text-black"></i> TikTok URL</label>
                                <input type="text" name="link_tiktok" value="<?php echo isset($data['link_tiktok']) ? htmlspecialchars($data['link_tiktok']) : '#'; ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none text-xs" placeholder="https://tiktok.com/...">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor / Toko</label>
                            <input type="text" name="alamat" value="<?php echo htmlspecialchars($data['alamat']); ?>" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat (Footer)</label>
                            <textarea name="deskripsi_footer" rows="3" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none"><?php echo htmlspecialchars($data['deskripsi_footer']); ?></textarea>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <button type="submit" name="simpan" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition w-full md:w-auto flex items-center justify-center gap-2">
                                <i class="fa-solid fa-save"></i> Simpan Pengaturan
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>