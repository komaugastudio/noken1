<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Ambil Konten dari Database
$query = "SELECT * FROM pages WHERE slug = 'tips-keamanan'";
$result = mysqli_query($conn, $query);
$page = mysqli_fetch_assoc($result);

// Default jika belum ada
$konten = $page ? $page['isi'] : "<div class='text-center py-20'><h2>Sedang Dalam Perbaikan</h2><p>Konten akan segera tersedia.</p></div>";
?>

<style>
    .page-content h3 { font-size: 1.5em; font-weight: 700; color: #0F766E; margin-top: 1.5em; margin-bottom: 0.5em; }
    .page-content h4 { font-size: 1.25em; font-weight: 600; color: #1f2937; margin-top: 1.2em; margin-bottom: 0.5em; display: flex; align-items: center; }
    .page-content h4::before { content: '🛡️'; margin-right: 8px; font-size: 0.9em; }
    .page-content p { margin-bottom: 1em; line-height: 1.7; color: #4b5563; }
    .page-content ul { list-style: disc; padding-left: 1.5em; margin-bottom: 1em; color: #4b5563; }
</style>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-2xl shadow-lg border border-teal-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-teal-600 to-green-600 px-8 py-10 text-center relative overflow-hidden">
                <i class="fa-solid fa-shield-halved text-white/10 text-9xl absolute -bottom-4 -right-4"></i>
                <h1 class="text-3xl font-bold text-white relative z-10">
                    <?php echo $page ? htmlspecialchars($page['judul']) : 'Pusat Keamanan'; ?>
                </h1>
                <p class="text-teal-100 mt-2 relative z-10">Panduan bertransaksi aman dan nyaman di NokenMART</p>
            </div>
            
            <!-- Konten -->
            <div class="p-8 md:p-12 text-gray-700 leading-relaxed page-content">
                <?php echo $konten; ?>
            </div>

            <!-- Footer Card -->
            <div class="bg-yellow-50 p-6 text-center border-t border-yellow-100">
                <p class="text-yellow-800 text-sm mb-3">Menemukan aktivitas mencurigakan?</p>
                <a href="kontak.php" class="inline-flex items-center gap-2 text-yellow-700 font-bold hover:underline">
                    <i class="fa-solid fa-triangle-exclamation"></i> Laporkan ke Admin
                </a>
            </div>
        </div>

    </div>
</div>

<?php include 'templates/footer.php'; ?>