<?php
// 1. Ambil Banner Aktif
// Pastikan koneksi ($conn) sudah tersedia dari file induk (index.php)
if (isset($conn)) {
    $q_banner = mysqli_query($conn, "SELECT * FROM banners WHERE aktif='ya' ORDER BY id DESC LIMIT 1");
    $banner = mysqli_fetch_assoc($q_banner);
}

// Default Banner (Fallback jika database kosong)
$default_img = 'https://images.unsplash.com/photo-1596422846543-75c6a1966470?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80';
$judul = 'Pace, Mace, Cari Apa?';
$subjudul = 'Platform jual beli aman dan terpercaya khusus wilayah Nabire dan sekitarnya.';
$gambar_tampil = $default_img;

if ($banner) {
    $judul = $banner['judul'];
    $subjudul = $banner['subjudul'];
    
    // Logika Sederhana untuk Gambar
    if (!empty($banner['gambar'])) {
        // Jika link eksternal (http/https), pakai langsung
        if (strpos($banner['gambar'], 'http') === 0) {
            $gambar_tampil = $banner['gambar'];
        } 
        // Jika file lokal, langsung gunakan path dari database
        else {
            // Tambahkan parameter waktu (?v=...) agar browser tidak menyimpan cache gambar lama
            $gambar_tampil = $banner['gambar'] . "?v=" . time();
        }
    }
}

// 2. Ambil Kategori Dinamis dari Database
// Kita ambil 7 data untuk mengecek apakah perlu tombol "Lainnya" (jika > 6)
$q_kategori = null;
if (isset($conn)) {
    $q_kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC LIMIT 7");
}
?>

<!-- Hero Section -->
<div class="relative bg-nabire-primary overflow-hidden min-h-[500px] flex items-center">
    <div class="absolute inset-0 bg-black/40 z-0"></div> <!-- Overlay Gelap -->
    <!-- Gambar Background -->
    <div class="absolute inset-0 bg-cover bg-center z-[-1]" style="background-image: url('<?php echo htmlspecialchars($gambar_tampil); ?>');"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10 text-center w-full">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-4 drop-shadow-lg animate-fade-in-down"><?php echo htmlspecialchars($judul); ?></h2>
        <p class="text-white text-lg md:text-xl mb-8 opacity-90 drop-shadow-md max-w-2xl mx-auto"><?php echo htmlspecialchars($subjudul); ?></p>
        
        <!-- PENCARIAN BESAR (Dengan Live Search) -->
        <div class="max-w-2xl mx-auto mb-10 relative">
            <form action="pencarian.php" method="GET" class="relative group" autocomplete="off">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-lg group-focus-within:text-nabire-primary transition"></i>
                </div>
                <input type="text" id="searchInputHero" name="keyword" onkeyup="liveSearchHero(this.value)" placeholder="Mau cari barang apa hari ini?" 
                       class="w-full pl-12 pr-14 py-4 rounded-full shadow-2xl border-none focus:ring-4 focus:ring-nabire-secondary/50 text-gray-800 text-lg placeholder-gray-400 transition transform hover:scale-[1.01] focus:scale-100">
                <button type="submit" class="absolute right-2 top-2 bg-nabire-secondary text-white p-2.5 rounded-full w-10 h-10 hover:bg-yellow-500 transition shadow-md flex items-center justify-center">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
            
            <!-- HASIL LIVE SEARCH HERO -->
            <div id="searchResultHero" class="absolute top-full mt-2 w-full bg-white shadow-2xl rounded-xl border border-gray-100 hidden z-50 text-left overflow-hidden max-h-80 overflow-y-auto"></div>
        </div>

        <!-- Quick Categories -->
        <div class="flex flex-wrap justify-center gap-3">
            <a href="index.php?kategori=all" class="bg-white text-nabire-primary px-5 py-2.5 rounded-full text-sm font-bold hover:bg-gray-100 transition shadow-lg border border-white">Semua</a>
            
            <?php 
            if ($q_kategori && mysqli_num_rows($q_kategori) > 0):
                $count = 0;
                while($kat = mysqli_fetch_assoc($q_kategori)): 
                    $count++;
                    // Jika sudah 6, dan masih ada data ke-7, tampilkan tombol Lainnya
                    if ($count > 6) {
                        echo '<a href="semua_kategori.php" class="bg-white/20 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-white hover:text-nabire-primary transition backdrop-blur-sm border border-white/30 capitalize"><i class="fa-solid fa-list mr-1"></i> Lainnya...</a>';
                        break; 
                    }
            ?>
                <a href="index.php?kategori=<?php echo $kat['slug']; ?>" class="bg-white/20 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-white hover:text-nabire-primary transition backdrop-blur-sm border border-white/30 capitalize flex items-center gap-2">
                    <!-- Logika Ikon: Cek DB dulu, kalau kosong pakai Emoji Fallback -->
                    <?php 
                        if (!empty($kat['gambar'])) {
                            // Tampilkan gambar dari database
                            echo '<img src="'.$kat['gambar'].'" class="w-5 h-5 object-contain">';
                        } else {
                            // Fallback Emoji jika tidak ada gambar
                            $icon = '📦'; 
                            $nama_kat = strtolower($kat['nama_kategori']);
                            if(strpos($nama_kat, 'kendaraan')!==false || strpos($nama_kat, 'motor')!==false) $icon = '🚗';
                            elseif(strpos($nama_kat, 'rumah')!==false || strpos($nama_kat, 'properti')!==false) $icon = '🏠';
                            elseif(strpos($nama_kat, 'elektronik')!==false || strpos($nama_kat, 'hp')!==false) $icon = '📱';
                            elseif(strpos($nama_kat, 'sewa')!==false || strpos($nama_kat, 'jasa')!==false) $icon = '🛠️';
                            elseif(strpos($nama_kat, 'makan')!==false || strpos($nama_kat, 'bumi')!==false) $icon = '🥥';
                            echo $icon;
                        }
                        echo " " . htmlspecialchars($kat['nama_kategori']); 
                    ?>
                </a>
            <?php 
                endwhile;
            else: 
            ?>
                <!-- Fallback jika kategori kosong -->
                <a href="index.php?kategori=kendaraan" class="bg-white/20 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-white hover:text-nabire-primary transition backdrop-blur-sm border border-white/30">🚗 Kendaraan</a>
                <a href="index.php?kategori=properti" class="bg-white/20 text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-white hover:text-nabire-primary transition backdrop-blur-sm border border-white/30">🏠 Properti</a>
            <?php endif; ?>
        </div>
        
        <!-- Trust Badges (Tambahan Kecil untuk Kepercayaan) -->
        <div class="mt-10 flex justify-center gap-6 text-white/80 text-xs md:text-sm font-medium">
            <span class="flex items-center gap-2"><i class="fa-solid fa-check-circle text-green-400"></i> Terverifikasi</span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-blue-400"></i> Transaksi Aman</span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-users text-yellow-400"></i> Komunitas Lokal</span>
        </div>
    </div>
</div>

<script>
    function liveSearchHero(keyword) {
        const resultBox = document.getElementById('searchResultHero');
        
        if (keyword.length < 2) {
            resultBox.classList.add('hidden');
            resultBox.innerHTML = '';
            return;
        }

        fetch('cari_ajax.php?keyword=' + keyword)
            .then(response => response.text())
            .then(data => {
                resultBox.innerHTML = data;
                resultBox.classList.remove('hidden');
            })
            .catch(err => console.error('Error:', err));
    }

    // Sembunyikan hasil saat klik di luar
    document.addEventListener('click', function(e) {
        const searchInput = document.getElementById('searchInputHero');
        const resultBox = document.getElementById('searchResultHero');
        
        if (searchInput && resultBox && !searchInput.contains(e.target) && !resultBox.contains(e.target)) {
            resultBox.classList.add('hidden');
        }
    });
</script>