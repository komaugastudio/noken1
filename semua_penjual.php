<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Logika Pencarian Penjual
$cari_penjual = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = "role = 'penjual'";

if (!empty($cari_penjual)) {
    $where .= " AND (nama LIKE '%$cari_penjual%' OR email LIKE '%$cari_penjual%')";
}

// Ambil Data Penjual
$query = "SELECT * FROM users WHERE $where ORDER BY is_verified DESC, id DESC";
$result = mysqli_query($conn, $query);
$jumlah_penjual = mysqli_num_rows($result);
?>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Mitra Penjual NokenMART</h1>
            <p class="text-gray-600 mb-8">Temukan toko dan penjual terpercaya di sekitar Anda.</p>
            
            <!-- Pencarian Penjual -->
            <form action="" method="GET" class="max-w-md mx-auto relative">
                <input type="text" name="cari" value="<?php echo htmlspecialchars($cari_penjual); ?>" placeholder="Cari nama penjual..." class="w-full pl-5 pr-12 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-nabire-primary shadow-sm">
                <button type="submit" class="absolute right-2 top-2 bg-nabire-primary text-white w-9 h-9 rounded-full flex items-center justify-center hover:bg-teal-800 transition">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>
        </div>

        <?php if($jumlah_penjual > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($row = mysqli_fetch_assoc($result)): 
                    // Hitung jumlah produk aktif penjual ini
                    $email_penjual = $row['email'];
                    $q_prod = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk WHERE penjual = '$email_penjual' AND status = 'aktif'");
                    $d_prod = mysqli_fetch_assoc($q_prod);
                    $total_barang = $d_prod['total'];

                    // Foto Profil
                    $foto = !empty($row['foto_profil']) ? $row['foto_profil'] : "https://ui-avatars.com/api/?name=".urlencode($row['nama'])."&background=0F766E&color=fff&size=128";
                ?>
                
                <a href="penjual.php?email=<?php echo urlencode($row['email']); ?>" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-5 hover:shadow-md transition group">
                    <div class="relative flex-shrink-0">
                        <img src="<?php echo $foto; ?>" class="w-20 h-20 rounded-full object-cover border-4 border-gray-50 group-hover:border-teal-50 transition">
                        <?php if($row['is_verified']): ?>
                            <div class="absolute bottom-0 right-0 bg-blue-500 text-white text-[10px] w-6 h-6 flex items-center justify-center rounded-full border-2 border-white" title="Terverifikasi">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 truncate text-lg group-hover:text-nabire-primary transition">
                            <?php echo htmlspecialchars($row['nama']); ?>
                        </h3>
                        <p class="text-sm text-gray-500 truncate mb-2"><?php echo htmlspecialchars($row['email']); ?></p>
                        
                        <div class="flex items-center gap-3 text-xs font-medium">
                            <span class="bg-teal-50 text-teal-700 px-2 py-1 rounded-md">
                                <i class="fa-solid fa-box-open mr-1"></i> <?php echo $total_barang; ?> Barang
                            </span>
                            <span class="text-gray-400">
                                <i class="fa-regular fa-calendar mr-1"></i> <?php echo date('M Y', strtotime($row['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-gray-300">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>

                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                <i class="fa-solid fa-users-slash text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Penjual tidak ditemukan</h3>
                <p class="text-gray-500">Coba gunakan kata kunci lain.</p>
                <a href="semua_penjual.php" class="text-nabire-primary font-bold hover:underline mt-2 inline-block">Lihat Semua</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'templates/footer.php'; ?>