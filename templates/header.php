<?php
// Pastikan koneksi sudah ada (biasanya di-include di index.php)
if (isset($conn)) {
    // Ambil Pengaturan Website
    $q_web = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
    $web = mysqli_fetch_assoc($q_web);
    
    // Default jika data kosong (baru install)
    $nama_web = $web ? $web['nama_website'] : "NokenMART";

    // --- LOGIKA CEK MAINTENANCE ---
    if (isset($web['mode_maintenance']) && $web['mode_maintenance'] == 'ya') {
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
        if (!$isAdmin) {
            $current_script = basename($_SERVER['PHP_SELF']);
            if ($current_script != 'maintenance.php' && $current_script != 'auth.php') {
                echo "<script>window.location='maintenance.php';</script>";
                exit;
            }
        }
    }
} else {
    $nama_web = "NokenMART";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nama_web); ?> - Jual Beli & Jasa Nabire</title>
    
    <!-- PWA Manifest & Meta -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0F766E">
    <link rel="apple-touch-icon" href="https://img.icons8.com/color/192/shopping-bag--v1.png">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        nabire: {
                            primary: '#0F766E',
                            secondary: '#F59E0B',
                            dark: '#111827',
                            light: '#F3F4F6'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Registrasi Service Worker PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(reg => console.log('PWA Service Worker registered!', reg))
                    .catch(err => console.log('PWA Service Worker failed:', err));
            });
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="index.php" class="flex items-center cursor-pointer">
                    <div class="bg-nabire-primary text-white p-2 rounded-lg mr-2">
                        <i class="fa-solid fa-bag-shopping text-xl"></i>
                    </div>
                    <div>
                        <!-- NAMA WEBSITE DINAMIS -->
                        <h1 class="text-xl font-bold text-nabire-primary tracking-tight">
                            <?php 
                                $parts = explode(' ', $nama_web);
                                echo htmlspecialchars($parts[0]); 
                                if(isset($parts[1])) echo '<span class="text-nabire-secondary">' . htmlspecialchars(substr($nama_web, strlen($parts[0]))) . '</span>';
                            ?>
                        </h1>
                        <p class="text-xs text-gray-500 hidden sm:block">Wadah Jual Beli Anak Negeri</p>
                    </div>
                </a>

                <!-- Menu Tambahan (UPDATE: Tambah Toko) -->
                <div class="hidden md:flex ml-6 space-x-6 items-center">
                    <a href="tentang.php" class="text-gray-600 hover:text-nabire-primary font-medium text-sm transition">Tentang Kami</a>
                    <a href="blog.php" class="text-gray-600 hover:text-nabire-primary font-medium text-sm transition">Blog</a>
                    
                    <!-- MENU BARU: TOKO -->
                    <a href="semua_penjual.php" class="text-gray-600 hover:text-nabire-primary font-medium text-sm transition">Toko</a>
                    
                    <a href="galeri.php" class="text-gray-600 hover:text-nabire-primary font-medium text-sm transition">Galeri</a>
                    <a href="promo.php" class="text-red-500 hover:text-red-700 font-bold text-sm transition flex items-center gap-1">
                        <i class="fa-solid fa-tags"></i> Promo
                    </a>
                    
                    <?php if(isset($_SESSION['user'])): ?>
                    <a href="wishlist.php" class="text-gray-600 hover:text-red-500 font-medium text-sm transition flex items-center gap-1">
                        <i class="fa-regular fa-heart"></i> Favorit
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Search Bar (Desktop) -->
                <?php
                // Cek nama file saat ini untuk menyembunyikan search bar di home
                $current_page = basename($_SERVER['PHP_SELF']);
                ?>
                
                <?php if ($current_page != 'index.php'): ?>
                    <div class="hidden md:flex flex-1 mx-8 relative">
                        <form action="pencarian.php" method="GET" class="w-full relative" autocomplete="off">
                            <input type="text" id="searchInputHeader" name="keyword" onkeyup="liveSearchHeader(this.value)" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" placeholder="Cari barang atau jasa di Nabire..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-nabire-primary transition">
                            <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-nabire-primary">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </form>
                        <div id="searchResultHeader" class="absolute top-full mt-2 w-full bg-white shadow-xl rounded-xl border border-gray-100 hidden z-50 overflow-hidden max-h-80 overflow-y-auto"></div>
                    </div>
                <?php else: ?>
                    <div class="hidden md:flex flex-1 mx-8"></div>
                <?php endif; ?>

                <!-- Auth Buttons (PHP Logic) -->
                <div class="flex items-center space-x-3">
                    <?php if(isset($_SESSION['user'])): ?>
                        <!-- JIKA SUDAH LOGIN -->
                        
                        <!-- ICON NOTIFIKASI -->
                        <?php
                            $jml_notif = 0;
                            // Cek koneksi untuk mencegah error jika file ini di-include tanpa koneksi
                            if(isset($conn)) {
                                $my_email = $_SESSION['user'];
                                $q_notif = mysqli_query($conn, "SELECT COUNT(*) as total FROM notifikasi WHERE user_email='$my_email' AND status='belum_dibaca'");
                                if($q_notif) {
                                    $d_notif = mysqli_fetch_assoc($q_notif);
                                    $jml_notif = $d_notif['total'];
                                }
                            }
                            
                            // LOGIKA FOTO PROFIL
                            $foto_user = !empty($_SESSION['foto']) ? $_SESSION['foto'] : "https://ui-avatars.com/api/?name=".urlencode(isset($_SESSION['nama']) ? $_SESSION['nama'] : $_SESSION['user'])."&background=0F766E&color=fff";
                        ?>
                        <a href="notifikasi.php" class="relative p-2 text-gray-600 hover:text-nabire-primary transition">
                            <i class="fa-regular fa-bell text-xl"></i>
                            <?php if($jml_notif > 0): ?>
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-100 transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full border-2 border-white"><?php echo $jml_notif; ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="flex items-center space-x-3 ml-2">
                            <button onclick="openSellModal()" class="bg-nabire-secondary hover:bg-yellow-600 text-white px-4 py-2 rounded-full font-semibold text-sm transition shadow-lg flex items-center gap-2">
                                <i class="fa-solid fa-plus"></i> <span class="hidden sm:inline">Pasang Iklan</span>
                            </button>
                            
                            <!-- Profile Dropdown -->
                            <div class="relative group">
                                <button class="flex items-center space-x-2 focus:outline-none">
                                    <img src="<?php echo $foto_user; ?>" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 hidden group-hover:block border border-gray-100 z-50">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-800 truncate">
                                            <?php echo htmlspecialchars(isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User'); ?>
                                        </p>
                                        <p class="text-xs text-gray-500">Member</p>
                                    </div>
                                    <a href="profil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-nabire-primary"><i class="fa-solid fa-list-ul mr-2 text-gray-400"></i> Iklan Saya</a>
                                    <a href="wishlist.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-500"><i class="fa-regular fa-heart mr-2 text-gray-400"></i> Favorit Saya</a>
                                    <a href="pengaturan.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-nabire-primary"><i class="fa-solid fa-gear mr-2 text-gray-400"></i> Pengaturan</a>
                                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <a href="admin/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-nabire-primary"><i class="fa-solid fa-gauge mr-2 text-gray-400"></i> Dashboard Admin</a>
                                    <?php endif; ?>
                                    <a href="auth.php?logout=true" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- JIKA BELUM LOGIN (GUEST) -->
                        <div class="flex items-center space-x-2">
                            <button onclick="openLoginModal()" class="text-gray-600 hover:text-nabire-primary font-medium px-3 py-2 text-sm">Masuk</button>
                            <button onclick="openRegisterModal()" class="hidden sm:block bg-nabire-primary hover:bg-teal-800 text-white px-4 py-2 rounded-full font-medium transition shadow-md text-sm">Daftar</button>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button (Hamburger) -->
                <div class="md:hidden flex items-center gap-3">
                     <?php if(isset($_SESSION['user'])): ?>
                        <!-- Show Notification Icon on Mobile too -->
                         <a href="notifikasi.php" class="relative text-gray-600">
                            <i class="fa-regular fa-bell text-xl"></i>
                            <?php if(isset($jml_notif) && $jml_notif > 0): ?>
                                <span class="absolute -top-1 -right-1 flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    
                    <button onclick="toggleMobileMenu()" class="text-gray-600 hover:text-nabire-primary focus:outline-none p-2">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100 absolute w-full left-0 z-40 shadow-lg animate-fade-in-down">
            <div class="px-4 pt-4 pb-6 space-y-3">
                
                <!-- Mobile Search (Jika bukan home) -->
                <?php if ($current_page != 'index.php'): ?>
                <form action="pencarian.php" method="GET" class="relative w-full mb-4">
                    <input type="text" name="keyword" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" placeholder="Cari barang..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-nabire-primary">
                    <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </form>
                <?php endif; ?>

                <a href="index.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Beranda</a>
                
                <!-- LINK PROMO MOBILE -->
                <a href="promo.php" class="block text-red-500 hover:text-red-700 font-bold py-2 border-b border-gray-50 flex items-center gap-2">
                    <i class="fa-solid fa-tags"></i> Promo Spesial
                </a>

                <a href="semua_kategori.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Semua Kategori</a>
                <a href="tentang.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Tentang Kami</a>
                <a href="blog.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Blog</a>
                <!-- MENU TOKO MOBILE -->
                <a href="semua_penjual.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Toko</a>
                <a href="galeri.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Galeri</a>
                
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="profil.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Iklan Saya</a>
                    <a href="wishlist.php" class="block text-gray-600 hover:text-red-500 font-medium py-2 border-b border-gray-50">Favorit Saya</a>
                    <a href="pengaturan.php" class="block text-gray-600 hover:text-nabire-primary font-medium py-2 border-b border-gray-50">Pengaturan Akun</a>
                    
                    <button onclick="openSellModal(); toggleMobileMenu()" class="w-full text-center bg-nabire-secondary text-white px-4 py-2 rounded-lg font-bold shadow mt-2">
                        <i class="fa-solid fa-plus mr-1"></i> Pasang Iklan
                    </button>
                    
                    <a href="auth.php?logout=true" class="block text-red-600 font-medium py-2 mt-2">Keluar</a>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <button onclick="openLoginModal(); toggleMobileMenu()" class="text-center border border-nabire-primary text-nabire-primary py-2 rounded-lg font-medium hover:bg-nabire-primary hover:text-white transition">Masuk</button>
                        <button onclick="openRegisterModal(); toggleMobileMenu()" class="text-center bg-nabire-primary text-white py-2 rounded-lg font-medium hover:bg-teal-800 transition">Daftar</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Script Live Search Header -->
    <script>
        function liveSearchHeader(keyword) {
            const resultBox = document.getElementById('searchResultHeader');
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
            const searchInput = document.getElementById('searchInputHeader');
            const resultBox = document.getElementById('searchResultHeader');
            if (searchInput && resultBox && !searchInput.contains(e.target) && !resultBox.contains(e.target)) {
                resultBox.classList.add('hidden');
            }
        });
        
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if(menu) menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>