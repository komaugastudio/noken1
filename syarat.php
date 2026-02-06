<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Ambil Konten dari Database
$query = "SELECT * FROM pages WHERE slug = 'syarat-ketentuan'";
$result = mysqli_query($conn, $query);
$page = mysqli_fetch_assoc($result);

// Default jika belum ada di database
$konten = $page ? $page['isi'] : "<div class='text-center py-20'><h2>Belum Ada Konten</h2><p>Silakan isi 'Syarat & Ketentuan' di Admin Panel.</p></div>";
?>

<style>
    /* Styling standar untuk output CKEditor */
    .page-content h1 { font-size: 2em; font-weight: 800; margin: 1em 0 0.5em; color: #111827; }
    .page-content h2 { font-size: 1.5em; font-weight: 700; margin: 1em 0 0.5em; color: #1f2937; }
    .page-content h3 { font-size: 1.25em; font-weight: 600; margin: 1em 0 0.5em; color: #374151; }
    .page-content p { margin-bottom: 1em; line-height: 1.6; color: #4b5563; }
    .page-content ul { list-style: disc; padding-left: 1.5em; margin-bottom: 1em; color: #4b5563; }
    .page-content ol { list-style: decimal; padding-left: 1.5em; margin-bottom: 1em; color: #4b5563; }
    .page-content li { margin-bottom: 0.5em; }
    .page-content strong { font-weight: 700; color: #111827; }
</style>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-nabire-primary px-8 py-6">
                <h1 class="text-2xl font-bold text-white">
                    <?php echo $page ? htmlspecialchars($page['judul']) : 'Syarat & Ketentuan'; ?>
                </h1>
                <p class="text-teal-100 text-sm mt-1">
                    Terakhir diperbarui: <?php echo $page ? date('d F Y', strtotime($page['updated_at'])) : date('d F Y'); ?>
                </p>
            </div>
            
            <div class="p-8 text-gray-700 leading-relaxed text-sm md:text-base page-content">
                <?php echo $konten; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>