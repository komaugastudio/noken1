<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Ambil Semua Kategori dari Database
$query = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result = mysqli_query($conn, $query);
$jumlah = mysqli_num_rows($result);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-screen">
    
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Jelajahi Kategori</h1>
        <p class="text-gray-600">Temukan barang impian Anda berdasarkan kategori</p>
    </div>

    <?php if($jumlah > 0): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php while($kat = mysqli_fetch_assoc($result)): ?>
                
                <a href="index.php?kategori=<?php echo $kat['slug']; ?>" class="group block bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md hover:border-nabire-primary transition transform hover:-translate-y-1 h-full flex flex-col items-center justify-center">
                    
                    <!-- Ikon Gambar / Emoji -->
                    <div class="mb-4 group-hover:scale-110 transition duration-300">
                        <?php if(!empty($kat['gambar'])): ?>
                            <!-- Tampilkan Gambar dari Database -->
                            <?php 
                                $imgSrc = $kat['gambar'];
                                // Tambahkan timestamp agar cache refresh jika gambar diganti dengan nama sama
                                if (strpos($imgSrc, 'http') !== 0) {
                                    $imgSrc = $imgSrc . "?v=" . time();
                                }
                            ?>
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="w-16 h-16 object-contain mx-auto" alt="<?php echo htmlspecialchars($kat['nama_kategori']); ?>">
                        <?php else: ?>
                            <!-- Fallback Emoji jika tidak ada gambar -->
                            <div class="text-4xl">
                                <?php 
                                    $icon = '📦'; 
                                    $nama_kat = strtolower($kat['nama_kategori']);
                                    if(strpos($nama_kat, 'kendaraan')!==false || strpos($nama_kat, 'motor')!==false) $icon = '🚗';
                                    elseif(strpos($nama_kat, 'rumah')!==false || strpos($nama_kat, 'properti')!==false) $icon = '🏠';
                                    elseif(strpos($nama_kat, 'elektronik')!==false || strpos($nama_kat, 'hp')!==false) $icon = '📱';
                                    elseif(strpos($nama_kat, 'sewa')!==false || strpos($nama_kat, 'jasa')!==false) $icon = '🛠️';
                                    elseif(strpos($nama_kat, 'makan')!==false || strpos($nama_kat, 'bumi')!==false) $icon = '🥥';
                                    elseif(strpos($nama_kat, 'fashion')!==false || strpos($nama_kat, 'baju')!==false) $icon = '👕';
                                    elseif(strpos($nama_kat, 'hobi')!==false || strpos($nama_kat, 'olahraga')!==false) $icon = '⚽';
                                    echo $icon;
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h3 class="font-bold text-gray-800 group-hover:text-nabire-primary transition"><?php echo htmlspecialchars($kat['nama_kategori']); ?></h3>
                    
                    <!-- Hitung Jumlah Barang per Kategori -->
                    <?php
                        $slug = $kat['slug'];
                        $q_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk WHERE kategori='$slug' AND status='aktif'");
                        $d_count = mysqli_fetch_assoc($q_count);
                    ?>
                    <p class="text-xs text-gray-400 mt-2"><?php echo $d_count['total']; ?> Iklan</p>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20">
            <p class="text-gray-500">Belum ada kategori yang tersedia.</p>
        </div>
    <?php endif; ?>

</div>

<?php include 'templates/footer.php'; ?>