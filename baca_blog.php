<?php
session_start();
include 'config/koneksi.php';

$slug = isset($_GET['slug']) ? mysqli_real_escape_string($conn, $_GET['slug']) : '';
$query = "SELECT * FROM blog WHERE slug = '$slug'";
$result = mysqli_query($conn, $query);
$artikel = mysqli_fetch_assoc($result);

if (!$artikel) {
    header("Location: blog.php");
    exit;
}

// Update views
mysqli_query($conn, "UPDATE blog SET views = views + 1 WHERE id = " . $artikel['id']);

include 'templates/header.php';
?>

<!-- Tambahkan Style Khusus untuk Konten CKEditor -->
<style>
    .blog-content h1 { font-size: 2.25rem; font-weight: 800; margin-top: 2rem; margin-bottom: 1rem; line-height: 1.2; color: #1f2937; }
    .blog-content h2 { font-size: 1.875rem; font-weight: 700; margin-top: 1.75rem; margin-bottom: 0.75rem; line-height: 1.3; color: #1f2937; }
    .blog-content h3 { font-size: 1.5rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; line-height: 1.4; color: #1f2937; }
    .blog-content p { margin-bottom: 1.25rem; line-height: 1.8; color: #374151; }
    .blog-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; }
    .blog-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.25rem; }
    .blog-content li { margin-bottom: 0.5rem; }
    .blog-content a { color: #0F766E; text-decoration: underline; }
    .blog-content blockquote { border-left: 4px solid #0F766E; padding-left: 1rem; font-style: italic; color: #4b5563; margin: 1.5rem 0; background: #f9fafb; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0; }
    .blog-content img { max-width: 100%; height: auto; border-radius: 0.5rem; margin: 1.5rem 0; }
    .blog-content strong { font-weight: 700; color: #111827; }
    .blog-content em { font-style: italic; }
</style>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Gambar Utama -->
        <div class="h-64 md:h-96 w-full relative">
            <img src="<?php echo htmlspecialchars($artikel['gambar']); ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-6 md:p-10 text-white w-full">
                <div class="flex flex-wrap items-center gap-4 text-sm mb-3 opacity-90 font-medium">
                    <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full"><i class="fa-regular fa-user mr-1"></i> <?php echo htmlspecialchars($artikel['penulis']); ?></span>
                    <span><i class="fa-regular fa-calendar mr-1"></i> <?php echo date('d M Y', strtotime($artikel['tanggal'])); ?></span>
                    <span><i class="fa-regular fa-eye mr-1"></i> <?php echo $artikel['views']; ?> views</span>
                </div>
                <h1 class="text-2xl md:text-4xl font-bold leading-tight drop-shadow-md"><?php echo htmlspecialchars($artikel['judul']); ?></h1>
            </div>
        </div>

        <!-- Isi Konten (Render HTML Asli) -->
        <div class="p-8 md:p-12">
            <!-- Class blog-content menerapkan style di atas -->
            <div class="blog-content text-lg">
                <?php echo $artikel['isi']; // Menampilkan HTML dari CKEditor apa adanya ?>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <a href="blog.php" class="text-gray-500 hover:text-nabire-primary font-medium flex items-center gap-2 group transition">
                    <i class="fa-solid fa-arrow-left transform group-hover:-translate-x-1 transition"></i> Kembali ke Blog
                </a>
                
                <div class="flex gap-3">
                    <span class="text-gray-400 text-sm mr-1 self-center">Bagikan:</span>
                    <a href="#" class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center hover:bg-sky-600 hover:text-white transition"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'templates/footer.php'; ?>