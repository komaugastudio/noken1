<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

$query = "SELECT * FROM galeri ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-screen">
    
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Galeri Kegiatan</h1>
        <p class="text-gray-600">Dokumentasi kegiatan dan komunitas NokenMART</p>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="columns-1 md:columns-2 lg:columns-3 gap-8 space-y-8">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="break-inside-avoid bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300">
                <img src="<?php echo strpos($row['gambar'], 'http') === 0 ? $row['gambar'] : htmlspecialchars($row['gambar']); ?>" 
                     class="w-full h-auto object-cover" alt="<?php echo htmlspecialchars($row['judul']); ?>">
                <div class="p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-2"><?php echo htmlspecialchars($row['judul']); ?></h3>
                    <?php if(!empty($row['deskripsi'])): ?>
                        <p class="text-gray-600 text-sm leading-relaxed"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                    <?php endif; ?>
                    <div class="mt-4 pt-4 border-t border-gray-50 text-xs text-gray-400">
                        <i class="fa-regular fa-calendar mr-1"></i> <?php echo date('d F Y', strtotime($row['tanggal'])); ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20">
            <i class="fa-regular fa-images text-6xl text-gray-200 mb-4"></i>
            <p class="text-gray-500">Belum ada foto di galeri.</p>
        </div>
    <?php endif; ?>

</div>

<?php include 'templates/footer.php'; ?>