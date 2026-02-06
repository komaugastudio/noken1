<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Ambil Konten dari Database
$query = "SELECT * FROM pages WHERE slug = 'tentang-kami'";
$result = mysqli_query($conn, $query);
$page = mysqli_fetch_assoc($result);

// Default jika belum ada di database
$konten = $page ? $page['isi'] : "<div class='text-center py-20'><h2>Halaman Belum Diisi</h2><p class='text-gray-500'>Silakan isi konten di Admin Panel > Kelola Halaman.</p></div>";
?>

<!-- Tambahkan CSS untuk merapikan hasil CKEditor -->
<style>
    /* Reset style CKEditor agar tampil rapi di Tailwind */
    .page-content h1 { font-size: 2.25em; font-weight: 800; color: #111827; margin-top: 1.5em; margin-bottom: 0.5em; line-height: 1.2; }
    .page-content h2 { font-size: 1.875em; font-weight: 700; color: #1f2937; margin-top: 1.5em; margin-bottom: 0.75em; line-height: 1.3; }
    .page-content h3 { font-size: 1.5em; font-weight: 600; color: #374151; margin-top: 1.25em; margin-bottom: 0.5em; }
    .page-content p { margin-bottom: 1.25em; line-height: 1.8; color: #4b5563; }
    .page-content ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1.25em; color: #4b5563; }
    .page-content ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1.25em; color: #4b5563; }
    .page-content li { margin-bottom: 0.5em; }
    .page-content a { color: #0F766E; text-decoration: underline; font-weight: 500; }
    .page-content blockquote { border-left: 4px solid #0F766E; padding-left: 1rem; font-style: italic; color: #6b7280; margin: 1.5rem 0; background: #f9fafb; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0; }
    .page-content img { max-width: 100%; height: auto; border-radius: 0.5rem; margin: 1.5rem 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .page-content strong { font-weight: 700; color: #111827; }
</style>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Header Banner Halaman -->
            <div class="bg-nabire-primary py-16 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10">
                    <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-3 tracking-tight">
                        <?php echo $page ? htmlspecialchars($page['judul']) : 'Tentang Kami'; ?>
                    </h1>
                    <p class="text-teal-100 text-lg font-medium">Mengenal Lebih Dekat NokenMART</p>
                </div>
            </div>
            
            <!-- Isi Konten -->
            <div class="p-8 md:p-12 page-content">
                <?php echo $konten; ?>
            </div>

            <!-- Footer Kontak Cepat -->
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 text-center">
                <p class="text-gray-500 text-sm mb-4">Punya pertanyaan lebih lanjut?</p>
                <div class="flex justify-center gap-4">
                    <a href="kontak.php" class="text-nabire-primary font-bold hover:underline text-sm">Hubungi Kami</a>
                    <span class="text-gray-300">|</span>
                    <a href="bantuan.php" class="text-nabire-primary font-bold hover:underline text-sm">Pusat Bantuan</a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>