<?php
session_start();
include 'config/koneksi.php';

// 1. TANGKAP DATA PENCARIAN & FILTER
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$kategori = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$lokasi = isset($_GET['lokasi']) ? mysqli_real_escape_string($conn, $_GET['lokasi']) : '';
// FILTER BARU: Kondisi
$kondisi = isset($_GET['kondisi']) ? mysqli_real_escape_string($conn, $_GET['kondisi']) : '';

$min_harga = isset($_GET['min']) ? (int)$_GET['min'] : 0;
$max_harga = isset($_GET['max']) ? (int)$_GET['max'] : 0;
$urutan = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';

// Params URL untuk pagination
$url_params = "";
if(!empty($keyword)) $url_params .= "&keyword=" . $keyword;
if(!empty($kategori)) $url_params .= "&kategori=" . $kategori;
if(!empty($lokasi)) $url_params .= "&lokasi=" . $lokasi;
if(!empty($kondisi)) $url_params .= "&kondisi=" . $kondisi; // Tambahkan ke URL
if($min_harga > 0) $url_params .= "&min=" . $min_harga;
if($max_harga > 0) $url_params .= "&max=" . $max_harga;
if($urutan != 'terbaru') $url_params .= "&sort=" . $urutan;

// 2. LOGIKA QUERY FILTER (WHERE)
$where_clause = "WHERE status = 'aktif'"; 

if (!empty($keyword)) {
    $where_clause .= " AND (judul LIKE '%$keyword%' OR lokasi LIKE '%$keyword%' OR penjual LIKE '%$keyword%')";
}
if (!empty($kategori) && $kategori != 'all') {
    $where_clause .= " AND kategori = '$kategori'";
}
if (!empty($lokasi) && $lokasi != 'all') {
    $where_clause .= " AND lokasi = '$lokasi'";
}
// Logika Filter Kondisi
if (!empty($kondisi) && $kondisi != 'all') {
    $where_clause .= " AND kondisi = '$kondisi'";
}

if ($min_harga > 0) {
    $where_clause .= " AND harga >= $min_harga";
}
if ($max_harga > 0) {
    $where_clause .= " AND harga <= $max_harga";
}

// 3. LOGIKA URUTAN
$order_clause = "ORDER BY waktu_sundul DESC"; 

if ($urutan == 'termurah') {
    $order_clause = "ORDER BY harga ASC";
} elseif ($urutan == 'termahal') {
    $order_clause = "ORDER BY harga DESC";
} elseif ($urutan == 'lama') {
    $order_clause = "ORDER BY id ASC";
} elseif ($urutan == 'terbaru') {
    $order_clause = "ORDER BY waktu_sundul DESC"; 
}

// --- LOGIKA HALAMAN (PAGINATION) ---
$batas = 12; 
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

$data = mysqli_query($conn, "SELECT * FROM produk $where_clause");
$jumlah_data = mysqli_num_rows($data);
$total_halaman = ceil($jumlah_data / $batas);

$query = "SELECT * FROM produk $where_clause $order_clause LIMIT $halaman_awal, $batas";
$result = mysqli_query($conn, $query);
$count_tampil = mysqli_num_rows($result);

function formatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

// Ambil Data Wilayah untuk Dropdown
$wilayah_opt = [];
$cek_wil = mysqli_query($conn, "SHOW TABLES LIKE 'wilayah'");
if(mysqli_num_rows($cek_wil) > 0) {
    $q_wil = mysqli_query($conn, "SELECT * FROM wilayah ORDER BY nama_wilayah ASC");
    while($w = mysqli_fetch_assoc($q_wil)) {
        $wilayah_opt[] = $w['nama_wilayah'];
    }
}

include 'templates/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            <?php if(!empty($keyword)): ?>
                Hasil Pencarian: "<span class="text-nabire-primary"><?php echo htmlspecialchars($keyword); ?></span>"
            <?php else: ?>
                Menelusuri Semua Produk
            <?php endif; ?>
        </h1>
        <p class="text-gray-500 text-sm">
            Menampilkan <strong><?php echo $count_tampil; ?></strong> dari <strong><?php echo $jumlah_data; ?></strong> barang ditemukan.
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- SIDEBAR FILTER -->
        <div class="lg:w-1/4">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-filter text-nabire-secondary"></i> Filter & Urutkan
                </h3>
                
                <form action="pencarian.php" method="GET">
                    <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                    
                    <!-- Filter Kategori -->
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kategori</label>
                        <select name="kategori" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-nabire-primary outline-none">
                            <option value="">Semua Kategori</option>
                            <?php 
                                $q_kat = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                while($kat_row = mysqli_fetch_assoc($q_kat)):
                            ?>
                                <option value="<?php echo $kat_row['slug']; ?>" <?php if($kategori == $kat_row['slug']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($kat_row['nama_kategori']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Filter Kondisi (BARU) -->
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kondisi</label>
                        <div class="space-y-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="kondisi" value="" class="accent-nabire-primary" <?php if($kondisi == '') echo 'checked'; ?>>
                                <span class="text-sm text-gray-600">Semua</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="kondisi" value="Baru" class="accent-nabire-primary" <?php if($kondisi == 'Baru') echo 'checked'; ?>>
                                <span class="text-sm text-gray-600">Baru</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="kondisi" value="Bekas" class="accent-nabire-primary" <?php if($kondisi == 'Bekas') echo 'checked'; ?>>
                                <span class="text-sm text-gray-600">Bekas</span>
                            </label>
                        </div>
                    </div>

                    <!-- Filter Lokasi -->
                    <?php if(!empty($wilayah_opt)): ?>
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Lokasi</label>
                        <select name="lokasi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-nabire-primary outline-none">
                            <option value="">Semua Wilayah</option>
                            <?php foreach($wilayah_opt as $w): ?>
                                <option value="<?php echo htmlspecialchars($w); ?>" <?php if($lokasi == $w) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($w); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <!-- Filter Harga -->
                    <div class="mb-5">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Rentang Harga (Rp)</label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="number" name="min" value="<?php echo $min_harga > 0 ? $min_harga : ''; ?>" placeholder="Min" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-nabire-primary outline-none">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max" value="<?php echo $max_harga > 0 ? $max_harga : ''; ?>" placeholder="Max" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-nabire-primary outline-none">
                        </div>
                    </div>

                    <!-- Urutkan -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Urutkan</label>
                        <select name="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-nabire-primary outline-none">
                            <option value="terbaru" <?php if($urutan == 'terbaru') echo 'selected'; ?>>Paling Baru (Sundul)</option>
                            <option value="termurah" <?php if($urutan == 'termurah') echo 'selected'; ?>>Harga Terendah</option>
                            <option value="termahal" <?php if($urutan == 'termahal') echo 'selected'; ?>>Harga Tertinggi</option>
                            <option value="lama" <?php if($urutan == 'lama') echo 'selected'; ?>>Paling Lama</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-nabire-primary hover:bg-teal-800 text-white font-bold py-2.5 rounded-lg shadow-sm transition text-sm">
                        Terapkan Filter
                    </button>
                    
                    <?php if(!empty($keyword) || !empty($kategori) || !empty($lokasi) || !empty($kondisi) || $min_harga > 0 || $max_harga > 0): ?>
                        <a href="pencarian.php" class="block w-full text-center mt-3 text-sm text-red-500 hover:underline">Reset Filter</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- HASIL PENCARIAN -->
        <div class="lg:w-3/4">
            <?php if ($count_tampil > 0): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-10">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden flex flex-col h-full group">
                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="block relative h-48 bg-gray-200 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                            
                            <!-- Label Kondisi (BARU) -->
                            <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded-md capitalize">
                                <?php echo htmlspecialchars($row['kondisi']); ?>
                            </div>

                            <div class="absolute top-2 right-2 bg-nabire-primary/80 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded-md capitalize">
                                <?php echo $row['kategori']; ?>
                            </div>
                        </a>
                        <div class="p-4 flex flex-col flex-grow">
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="block">
                                <h3 class="font-semibold text-gray-800 line-clamp-2 mb-1 text-sm md:text-base h-10 group-hover:text-nabire-primary transition"><?php echo htmlspecialchars($row['judul']); ?></h3>
                            </a>
                            <p class="text-nabire-secondary font-bold text-lg mb-2"><?php echo formatRupiah($row['harga']); ?></p>
                            <div class="flex items-center text-gray-500 text-xs mb-4">
                                <i class="fa-solid fa-location-dot mr-1"></i> <?php echo htmlspecialchars($row['lokasi']); ?>
                            </div>
                            <div class="mt-auto">
                                <a href="detail.php?id=<?php echo $row['id']; ?>" class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-600 py-2 rounded-lg font-medium transition text-sm border border-gray-200">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination Controls -->
                <?php if($total_halaman > 1): ?>
                <div class="flex justify-center mt-8">
                    <nav class="flex items-center gap-2">
                        <?php if($halaman > 1): ?><a href="?halaman=<?php echo $previous . $url_params; ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600"><i class="fa-solid fa-chevron-left"></i></a><?php endif; ?>
                        <?php for($x = 1; $x <= $total_halaman; $x++): ?><a href="?halaman=<?php echo $x . $url_params; ?>" class="px-4 py-2 border rounded-lg <?php echo ($x == $halaman) ? 'bg-nabire-primary text-white border-nabire-primary' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50'; ?>"><?php echo $x; ?></a><?php endfor; ?>
                        <?php if($halaman < $total_halaman): ?><a href="?halaman=<?php echo $next . $url_params; ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600"><i class="fa-solid fa-chevron-right"></i></a><?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                    <i class="fa-solid fa-filter-circle-xmark text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">Tidak ada barang yang cocok</h3>
                    <p class="text-gray-500 mb-6">Coba kurangi filter atau gunakan kata kunci lain.</p>
                    <a href="pencarian.php" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition font-medium">Reset Filter</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>