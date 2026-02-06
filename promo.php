<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Ambil Barang yang Diskon > 0
$query = "SELECT * FROM produk WHERE status='aktif' AND diskon > 0 ORDER BY diskon DESC, id DESC";
$result = mysqli_query($conn, $query);
$jumlah = mysqli_num_rows($result);

function formatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-screen">
    
    <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-red-600 mb-2 flex items-center justify-center gap-3">
            <i class="fa-solid fa-tags animate-bounce"></i> Promo Spesial
        </h1>
        <p class="text-gray-600">Dapatkan barang impian dengan harga miring!</p>
    </div>

    <?php if($jumlah > 0): ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php while($row = mysqli_fetch_assoc($result)): 
                $harga_asli = $row['harga'];
                $harga_diskon = $harga_asli - ($harga_asli * $row['diskon'] / 100);
            ?>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition duration-300 border border-red-100 overflow-hidden flex flex-col h-full group relative">
                
                <!-- Badge Diskon Besar -->
                <div class="absolute top-0 right-0 z-20 bg-red-600 text-white font-bold px-3 py-1.5 rounded-bl-xl shadow-md">
                    Hemat <?php echo $row['diskon']; ?>%
                </div>

                <a href="detail.php?id=<?php echo $row['id']; ?>" class="block relative h-48 bg-gray-200 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x300'">
                </a>
                
                <div class="p-4 flex flex-col flex-grow">
                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="block">
                        <h3 class="font-semibold text-gray-800 line-clamp-2 mb-2 text-sm group-hover:text-red-600 transition"><?php echo htmlspecialchars($row['judul']); ?></h3>
                    </a>
                    
                    <div class="mt-auto">
                        <p class="text-xs text-gray-400 line-through mb-1"><?php echo formatRupiah($harga_asli); ?></p>
                        <div class="flex items-center justify-between">
                            <p class="text-red-600 font-bold text-lg"><?php echo formatRupiah($harga_diskon); ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center text-xs text-gray-500">
                        <i class="fa-solid fa-location-dot mr-1 text-red-400"></i> <?php echo htmlspecialchars($row['lokasi']); ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
            <i class="fa-solid fa-tag text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Belum ada promo saat ini</h3>
            <p class="text-gray-500">Cek lagi nanti ya!</p>
            <a href="index.php" class="inline-block mt-4 text-nabire-primary font-bold hover:underline">Lihat Semua Barang</a>
        </div>
    <?php endif; ?>

</div>

<?php include 'templates/footer.php'; ?>