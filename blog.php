<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

$query = "SELECT * FROM blog ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-screen">
    
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Blog & Tips NokenMART</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">Berita terbaru, tips transaksi aman, dan informasi menarik seputar jual beli di Nabire.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300 group flex flex-col h-full">
            <a href="baca_blog.php?slug=<?php echo $row['slug']; ?>" class="block h-52 overflow-hidden relative">
                <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500" onerror="this.src='https://placehold.co/600x400?text=No+Image'">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition"></div>
            </a>
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex items-center text-xs text-gray-500 mb-3 space-x-2">
                    <span class="bg-teal-50 text-teal-700 px-2 py-1 rounded-md font-medium">Artikel</span>
                    <span>&bull;</span>
                    <span><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-3 leading-snug group-hover:text-nabire-primary transition">
                    <a href="baca_blog.php?slug=<?php echo $row['slug']; ?>">
                        <?php echo htmlspecialchars($row['judul']); ?>
                    </a>
                </h3>
                <p class="text-gray-600 text-sm line-clamp-3 mb-4 flex-grow">
                    <?php 
                        // PENTING: Gunakan strip_tags untuk membersihkan HTML dari CKEditor
                        // agar tampilan preview rapi (hanya teks polos)
                        $isi_bersih = strip_tags($row['isi']);
                        echo htmlspecialchars(substr($isi_bersih, 0, 120)); 
                    ?>...
                </p>
                <div class="pt-4 border-t border-gray-50 mt-auto">
                    <a href="baca_blog.php?slug=<?php echo $row['slug']; ?>" class="text-nabire-secondary font-bold text-sm hover:text-yellow-600 inline-flex items-center gap-1 transition">
                        Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>