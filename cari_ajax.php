<?php
include 'config/koneksi.php';

if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    
    // Cari produk aktif yang mirip
    $query = "SELECT id, judul, gambar, harga, kategori FROM produk 
              WHERE status='aktif' 
              AND (judul LIKE '%$keyword%' OR kategori LIKE '%$keyword%') 
              LIMIT 5";
    
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Format Rupiah
            $harga = "Rp " . number_format($row['harga'], 0, ',', '.');
            
            // Output HTML untuk setiap item saran
            echo '
            <a href="detail.php?id='.$row['id'].'" class="flex items-center gap-3 p-3 hover:bg-gray-50 border-b border-gray-100 transition last:border-0">
                <img src="'.$row['gambar'].'" class="w-10 h-10 object-cover rounded-md border border-gray-200" onerror="this.src=\'https://placehold.co/100\'">
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-800 truncate">'.htmlspecialchars($row['judul']).'</h4>
                    <div class="flex justify-between items-center mt-0.5">
                        <span class="text-xs text-nabire-secondary font-bold">'.$harga.'</span>
                        <span class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded capitalize">'.htmlspecialchars($row['kategori']).'</span>
                    </div>
                </div>
            </a>
            ';
        }
        // Link lihat semua hasil
        echo '
        <a href="pencarian.php?keyword='.urlencode($keyword).'" class="block text-center text-xs font-bold text-nabire-primary bg-gray-50 p-2 hover:bg-gray-100 transition">
            Lihat semua hasil untuk "'.$keyword.'"
        </a>
        ';
    } else {
        echo '<div class="p-4 text-center text-sm text-gray-500">Tidak ada barang ditemukan.</div>';
    }
}
?>