<?php
// Ambil data pengaturan (jika belum ada/belum di-load di header)
if (!isset($web)) {
    if(isset($conn)) {
        $q_web = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
        $web = mysqli_fetch_assoc($q_web);
    }
    // Default fallback jika database kosong
    if(empty($web)) {
        $web = [
            'nama_website' => 'NokenMART',
            'deskripsi_footer' => 'Membantu UMKM dan warga Nabire untuk bertransaksi dengan mudah, aman, dan cepat.',
            'wa_admin' => '628123456789',
            'alamat' => 'Nabire, Papua Tengah'
        ];
    }
}

// Ambil Kategori untuk List Footer dan Modal (Pastikan $conn tersedia)
$q_kat_footer = false;
if(isset($conn)) {
    $q_kat_footer = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
}
?>

    <!-- Footer Visual -->
    <footer class="bg-nabire-dark text-white pt-16 pb-8 mt-auto mb-16 md:mb-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                <div>
                    <h4 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-bag-shopping text-nabire-secondary"></i> <?php echo htmlspecialchars($web['nama_website']); ?>
                    </h4>
                    <p class="text-gray-400 text-sm mb-6 leading-relaxed"><?php echo htmlspecialchars($web['deskripsi_footer']); ?></p>
                    
                    <div class="flex gap-4 mb-6">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-nabire-primary transition text-gray-400 hover:text-white"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-nabire-primary transition text-gray-400 hover:text-white"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-nabire-primary transition text-gray-400 hover:text-white"><i class="fa-brands fa-tiktok"></i></a>
                    </div>

                    <div class="flex gap-4 text-xs text-gray-500 font-medium">
                        <a href="syarat.php" class="hover:text-white transition">Syarat & Ketentuan</a>
                        <span>•</span>
                        <a href="privasi.php" class="hover:text-white transition">Kebijakan Privasi</a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-6 text-gray-200">Kategori Populer</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <?php 
                        if ($q_kat_footer):
                            $counter = 0;
                            mysqli_data_seek($q_kat_footer, 0); 
                            while($kf = mysqli_fetch_assoc($q_kat_footer)): 
                                if($counter >= 5) break; 
                                $counter++;
                        ?>
                            <li><a href="index.php?kategori=<?php echo $kf['slug']; ?>" class="hover:text-nabire-secondary transition flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs text-gray-600"></i> <?php echo htmlspecialchars($kf['nama_kategori']); ?></a></li>
                        <?php endwhile; else: ?>
                            <li><a href="index.php?kategori=kendaraan" class="hover:text-nabire-secondary">Kendaraan</a></li>
                            <li><a href="index.php?kategori=properti" class="hover:text-nabire-secondary">Properti</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-6 text-gray-200">Hubungi Kami</h4>
                    <ul class="space-y-4 text-gray-400 text-sm">
                        <li class="flex items-start gap-3">
                            <div class="mt-1 w-6 h-6 rounded-full bg-gray-800 flex items-center justify-center text-green-500"><i class="fa-brands fa-whatsapp text-sm"></i></div>
                            <span><?php echo htmlspecialchars($web['wa_admin']); ?></span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="mt-1 w-6 h-6 rounded-full bg-gray-800 flex items-center justify-center text-red-500"><i class="fa-solid fa-location-dot text-sm"></i></div>
                            <span><?php echo htmlspecialchars($web['alamat']); ?></span>
                        </li>
                        <li class="pt-4 border-t border-gray-800">
                            <a href="bantuan.php" class="inline-flex items-center gap-2 text-nabire-secondary hover:text-white transition font-medium">
                                <i class="fa-regular fa-circle-question"></i> Pusat Bantuan / FAQ
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-600">
                &copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($web['nama_website']); ?></strong>. Dibuat dengan ❤️ untuk Papua.
            </div>
        </div>
    </footer>

    <!-- TOMBOL SCROLL TO TOP -->
    <button id="scrollToTopBtn" onclick="window.scrollTo({top: 0, behavior: 'smooth'});" 
        class="fixed bottom-20 right-4 md:bottom-8 md:right-8 bg-nabire-secondary hover:bg-yellow-500 text-white w-12 h-12 rounded-full shadow-lg items-center justify-center transition transform hover:-translate-y-1 hidden z-40">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- TOMBOL WA BANTUAN MELAYANG -->
    <a href="https://wa.me/<?php echo $web['wa_admin']; ?>?text=Halo Admin NokenMART, saya butuh bantuan..." target="_blank"
       class="fixed bottom-20 left-4 md:bottom-8 md:left-8 bg-green-500 hover:bg-green-600 text-white px-3 py-2 md:px-4 md:py-3 rounded-full shadow-lg flex items-center gap-2 transition transform hover:scale-105 z-40 group">
        <i class="fa-brands fa-whatsapp text-xl md:text-2xl"></i>
        <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-500 ease-in-out whitespace-nowrap text-xs md:text-sm font-bold hidden md:block">Chat Bantuan</span>
    </a>

    <!-- MOBILE BOTTOM NAVIGATION (Akses Cepat) -->
    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.1)] flex justify-around items-end pb-2 pt-2 z-50 md:hidden">
        <a href="index.php" class="flex flex-col items-center w-1/5 text-gray-400 hover:text-nabire-primary <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-nabire-primary' : ''; ?>">
            <i class="fa-solid fa-house text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="semua_kategori.php" class="flex flex-col items-center w-1/5 text-gray-400 hover:text-nabire-primary <?php echo basename($_SERVER['PHP_SELF']) == 'semua_kategori.php' ? 'text-nabire-primary' : ''; ?>">
            <i class="fa-solid fa-layer-group text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Kategori</span>
        </a>
        <div class="w-1/5 flex justify-center relative">
            <button onclick="<?php echo isset($_SESSION['user']) ? 'openSellModal()' : 'openLoginModal()'; ?>" class="absolute -top-8 bg-nabire-secondary text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg border-4 border-white transform active:scale-95 transition">
                <i class="fa-solid fa-camera text-2xl"></i>
            </button>
            <span class="text-[10px] font-medium text-gray-500 mt-7">Jual</span>
        </div>
        <a href="wishlist.php" class="flex flex-col items-center w-1/5 text-gray-400 hover:text-nabire-primary <?php echo basename($_SERVER['PHP_SELF']) == 'wishlist.php' ? 'text-nabire-primary' : ''; ?>">
            <i class="fa-solid fa-heart text-xl mb-1"></i>
            <span class="text-[10px] font-medium">Favorit</span>
        </a>
        <a href="<?php echo isset($_SESSION['user']) ? 'akun.php' : 'javascript:openLoginModal()'; ?>" class="flex flex-col items-center w-1/5 text-gray-400 hover:text-nabire-primary <?php echo basename($_SERVER['PHP_SELF']) == 'akun.php' ? 'text-nabire-primary' : ''; ?>">
            <i class="fa-solid fa-user text-xl mb-1"></i>
            <span class="text-[10px] font-medium"><?php echo isset($_SESSION['user']) ? 'Akun' : 'Masuk'; ?></span>
        </a>
    </div>

    <!-- 1. LOGIN MODAL -->
    <div id="loginModal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform scale-100 transition-transform">
            <div class="p-8">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-nabire-primary">Selamat Datang</h3>
                    <p class="text-gray-500 text-sm mt-1">Masuk ke <?php echo htmlspecialchars($web['nama_website']); ?></p>
                </div>
                <!-- Form Login Manual -->
                <form action="auth.php" method="POST">
                    <div class="space-y-5">
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                            <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-nabire-primary focus:border-transparent outline-none transition" placeholder="Alamat Email">
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                            <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-nabire-primary focus:border-transparent outline-none transition" placeholder="Password">
                        </div>
                        <button type="submit" name="login" class="w-full bg-nabire-primary hover:bg-teal-800 text-white font-bold py-3 rounded-lg shadow-md transition transform active:scale-95">
                            Masuk Sekarang
                        </button>
                        <div class="text-center">
                            <a href="lupa_password.php" class="text-xs text-gray-500 hover:text-nabire-secondary transition">Lupa Password?</a>
                        </div>
                    </div>
                </form>
                <button onclick="closeLoginModal()" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="bg-gray-50 px-8 py-4 text-center text-sm text-gray-600">
                Belum punya akun? <button onclick="openRegisterModal()" class="text-nabire-primary font-bold hover:underline">Daftar disini</button>
            </div>
        </div>
    </div>

    <!-- 2. REGISTER MODAL -->
    <div id="registerModal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden my-8">
            <div class="p-8">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-nabire-primary">Buat Akun Baru</h3>
                    <p class="text-gray-500 text-sm">Bergabung dengan komunitas NokenMART</p>
                </div>
                
                <form action="auth.php" method="POST" enctype="multipart/form-data" id="registerForm">
                    <div class="space-y-4">
                        
                        <!-- PILIHAN TIPE AKUN -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-bold text-gray-700 mb-3 text-center">Saya ingin mendaftar sebagai:</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="pembeli" class="peer sr-only" checked onchange="toggleRegisterType()">
                                    <div class="text-center p-3 border-2 border-gray-200 rounded-lg peer-checked:border-nabire-primary peer-checked:bg-teal-50 peer-checked:text-nabire-primary transition hover:bg-gray-100">
                                        <i class="fa-solid fa-cart-shopping text-xl mb-1 block"></i>
                                        <span class="text-sm font-bold">Pembeli</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="penjual" class="peer sr-only" onchange="toggleRegisterType()">
                                    <div class="text-center p-3 border-2 border-gray-200 rounded-lg peer-checked:border-nabire-primary peer-checked:bg-teal-50 peer-checked:text-nabire-primary transition hover:bg-gray-100">
                                        <i class="fa-solid fa-store text-xl mb-1 block"></i>
                                        <span class="text-sm font-bold">Penjual</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- INPUT UMUM -->
                        <div class="relative"><i class="fa-regular fa-user absolute left-3 top-3.5 text-gray-400"></i><input type="text" name="nama" required class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="Nama Lengkap"></div>
                        <div class="relative"><i class="fa-regular fa-envelope absolute left-3 top-3.5 text-gray-400"></i><input type="email" name="email" required class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="Email Aktif"></div>
                        <div class="relative"><i class="fa-brands fa-whatsapp absolute left-3 top-3.5 text-gray-400"></i><input type="text" name="whatsapp" required class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="No WhatsApp (628...)"></div>
                        <div class="relative"><i class="fa-solid fa-lock absolute left-3 top-3.5 text-gray-400"></i><input type="password" name="password" required class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="Buat Password"></div>

                        <!-- INPUT KHUSUS PENJUAL (Hidden by Default) -->
                        <div id="syarat-penjual" class="hidden space-y-4 pt-2 border-t border-gray-100">
                            <div class="bg-yellow-50 text-yellow-800 text-xs p-3 rounded-lg flex items-start gap-2">
                                <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                                <p>Untuk keamanan, Penjual <strong>wajib</strong> mengunggah foto KTP dan mengambil Swafoto langsung.</p>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Foto KTP / Identitas (Upload File)</label>
                                <input type="file" name="foto_ktp" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-nabire-light file:text-nabire-primary hover:file:bg-gray-200 border border-gray-300 rounded-lg">
                            </div>
                            
                            <!-- KAMERA SWAFOTO -->
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Ambil Swafoto (Selfie)</label>
                                <div class="bg-black rounded-lg overflow-hidden relative" style="height: 200px;">
                                    <video id="webcam" autoplay playsinline class="w-full h-full object-cover hidden"></video>
                                    <canvas id="canvas" class="hidden"></canvas>
                                    <img id="hasil_foto" class="w-full h-full object-cover hidden">
                                    
                                    <!-- Tombol Start Kamera -->
                                    <div id="camera_overlay" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 text-gray-500">
                                        <i class="fa-solid fa-camera text-3xl mb-2"></i>
                                        <button type="button" onclick="startCamera()" class="text-xs bg-nabire-primary text-white px-3 py-1 rounded">Buka Kamera</button>
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-2">
                                    <button type="button" id="btn_snap" onclick="takeSnapshot()" class="flex-1 bg-nabire-secondary text-white py-1 rounded text-xs hidden"><i class="fa-solid fa-camera"></i> Ambil Foto</button>
                                    <button type="button" id="btn_retake" onclick="resetCamera()" class="flex-1 bg-gray-500 text-white py-1 rounded text-xs hidden"><i class="fa-solid fa-rotate-right"></i> Ulang</button>
                                </div>
                                <!-- Input Hidden untuk menyimpan Base64 Image -->
                                <input type="hidden" name="foto_selfie_base64" id="foto_selfie_base64">
                            </div>
                        </div>

                        <!-- Checkbox Syarat & Ketentuan -->
                        <div class="flex items-start gap-2 pt-2">
                            <input type="checkbox" id="agreeTerms" class="mt-1 accent-nabire-primary w-4 h-4 cursor-pointer" onchange="toggleRegisterButton()">
                            <label for="agreeTerms" class="text-xs text-gray-500 cursor-pointer select-none">
                                Saya telah membaca dan menyetujui <a href="syarat.php" target="_blank" class="text-nabire-primary hover:underline">Syarat & Ketentuan</a> serta <a href="privasi.php" target="_blank" class="text-nabire-primary hover:underline">Kebijakan Privasi</a>.
                            </label>
                        </div>

                        <!-- Tombol Default Disabled -->
                        <button type="submit" name="register" id="btnRegisterSubmit" disabled class="w-full bg-nabire-secondary hover:bg-yellow-600 text-white font-bold py-3 rounded-lg shadow-md transition transform active:scale-95 opacity-50 cursor-not-allowed">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>

                <button onclick="closeRegisterModal()" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div class="bg-gray-50 px-8 py-4 text-center text-sm text-gray-600">Sudah punya akun? <button onclick="openLoginModal()" class="text-nabire-primary font-bold hover:underline">Masuk disini</button></div>
        </div>
    </div>

    <!-- 3. SELL MODAL (PASANG IKLAN - UPDATE: KONDISI BARANG & PILIHAN WILAYAH) -->
    <div id="sellModal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl transform transition-all scale-100 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-nabire-primary px-6 py-4 flex justify-between items-center shrink-0">
                <h3 class="text-white font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-camera"></i> Pasang Iklan Baru</h3>
                <button onclick="closeSellModal()" class="text-white hover:bg-white/20 rounded-full p-1 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div class="p-6 overflow-y-auto">
                <form action="index.php" method="POST" enctype="multipart/form-data">
                    <div class="space-y-5">
                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">Nama Barang</label><input type="text" name="judul" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none"></div>
                        
                        <!-- UPDATE: Kondisi Barang -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Barang</label>
                            <div class="flex gap-6">
                                <label class="flex items-center cursor-pointer gap-2">
                                    <input type="radio" name="kondisi" value="Baru" class="accent-nabire-primary w-4 h-4" checked>
                                    <span class="text-sm text-gray-600">Baru</span>
                                </label>
                                <label class="flex items-center cursor-pointer gap-2">
                                    <input type="radio" name="kondisi" value="Bekas" class="accent-nabire-primary w-4 h-4">
                                    <span class="text-sm text-gray-600">Bekas</span>
                                </label>
                            </div>
                        </div>

                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Lengkap</label><textarea name="deskripsi" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="Jelaskan kondisi barang, minus (jika ada), kelengkapan, dll..."></textarea></div>
                        <div class="grid grid-cols-2 gap-5">
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Harga (Rp)</label><input type="number" name="harga" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none"></div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                                <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none bg-white">
                                    <option value="">Pilih...</option>
                                    <?php if ($q_kat_footer): mysqli_data_seek($q_kat_footer, 0); while($kat_opt = mysqli_fetch_assoc($q_kat_footer)): ?>
                                        <option value="<?php echo $kat_opt['slug']; ?>"><?php echo htmlspecialchars($kat_opt['nama_kategori']); ?></option>
                                    <?php endwhile; else: ?><option value="kendaraan">Kendaraan</option><?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-5">
                            <!-- UPDATE: LOKASI DARI DATABASE -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Lokasi (Distrik)</label>
                                <select name="lokasi" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none bg-white">
                                    <option value="">Pilih Distrik...</option>
                                    <?php 
                                    if(!isset($q_wilayah) || !$q_wilayah) {
                                        if(isset($conn)) {
                                             $q_wilayah = mysqli_query($conn, "SELECT * FROM wilayah ORDER BY nama_wilayah ASC");
                                        }
                                    }
                                    
                                    if(isset($q_wilayah) && $q_wilayah):
                                        mysqli_data_seek($q_wilayah, 0); 
                                        while($wil = mysqli_fetch_assoc($q_wilayah)): 
                                    ?>
                                        <option value="<?php echo htmlspecialchars($wil['nama_wilayah']); ?>"><?php echo htmlspecialchars($wil['nama_wilayah']); ?></option>
                                    <?php endwhile; else: ?>
                                        <option value="Nabire Kota">Nabire Kota</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">Diskon (%)</label><input type="number" name="diskon" min="0" max="99" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none"></div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-center justify-between">
                            <div><h5 class="text-sm font-bold text-yellow-800"><i class="fa-solid fa-crown"></i> Iklan Premium (Berbayar)</h5><p class="text-xs text-yellow-600">Muncul paling atas. Biaya Rp 50.000/minggu.</p></div>
                            <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="is_premium" value="1" class="sr-only peer" onchange="checkPremium(this)"><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div></label>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Utama</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition cursor-pointer relative overflow-hidden" id="dropzone">
                                <input type="file" name="gambar" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImage(this)">
                                <div id="uploadPlaceholder"><i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i><p class="text-sm text-gray-500">Klik untuk upload foto</p></div>
                                <img id="imgPreview" class="hidden h-32 mx-auto rounded-lg shadow-sm object-cover">
                            </div>
                        </div>
                        <div class="pt-2"><button type="submit" name="tambah_barang" class="w-full bg-nabire-secondary hover:bg-yellow-600 text-white font-bold py-3 rounded-lg shadow-md transition transform active:scale-95">Tayangkan Iklan Sekarang</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // UPDATE: Toggle Register Button
        function toggleRegisterButton() {
            const checkbox = document.getElementById('agreeTerms');
            const btn = document.getElementById('btnRegisterSubmit');
            if (checkbox.checked) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // --- LOGIKA KAMERA SWAFOTO ---
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const hasilFoto = document.getElementById('hasil_foto');
        const overlay = document.getElementById('camera_overlay');
        const btnSnap = document.getElementById('btn_snap');
        const btnRetake = document.getElementById('btn_retake');
        const inputBase64 = document.getElementById('foto_selfie_base64');
        let stream = null;

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                video.classList.remove('hidden');
                overlay.classList.add('hidden');
                btnSnap.classList.remove('hidden');
                hasilFoto.classList.add('hidden');
            } catch (err) {
                alert("Tidak dapat mengakses kamera. Pastikan izin diberikan.");
            }
        }

        function takeSnapshot() {
            if (!stream) return;
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Konversi ke Base64
            const dataURL = canvas.toDataURL('image/jpeg');
            hasilFoto.src = dataURL;
            inputBase64.value = dataURL; // Simpan ke input hidden

            video.classList.add('hidden');
            hasilFoto.classList.remove('hidden');
            btnSnap.classList.add('hidden');
            btnRetake.classList.remove('hidden');
            
            // Matikan stream sementara
            stopStream(); 
        }

        function resetCamera() {
            hasilFoto.classList.add('hidden');
            inputBase64.value = "";
            btnRetake.classList.add('hidden');
            startCamera();
        }

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }
        // --- END LOGIKA KAMERA ---


        // Logic Toggle Penjual/Pembeli
        function toggleRegisterType() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const syaratPenjual = document.getElementById('syarat-penjual');
            
            if (role === 'penjual') {
                syaratPenjual.classList.remove('hidden');
                document.querySelector('input[name="foto_ktp"]').required = true;
            } else {
                syaratPenjual.classList.add('hidden');
                document.querySelector('input[name="foto_ktp"]').required = false;
                stopStream();
            }
        }

        // Check Premium Logic
        function checkPremium(checkbox) {
            if(checkbox.checked) {
                Swal.fire({
                    title: 'Upgrade ke Premium?',
                    text: "Biaya Rp 50.000/minggu. Iklan akan muncul paling atas. Lanjutkan?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#F59E0B',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        checkbox.checked = false;
                    }
                });
            }
        }

        // Image Preview Logic
        function previewImage(input) {
            const placeholder = document.getElementById('uploadPlaceholder');
            const preview = document.getElementById('imgPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openLoginModal() { document.getElementById('loginModal').classList.remove('hidden'); document.getElementById('registerModal').classList.add('hidden'); }
        function closeLoginModal() { document.getElementById('loginModal').classList.add('hidden'); }
        function openRegisterModal() { 
            document.getElementById('registerModal').classList.remove('hidden'); 
            document.getElementById('loginModal').classList.add('hidden'); 
            const radioPembeli = document.querySelector('input[name="role"][value="pembeli"]');
            if(radioPembeli) radioPembeli.checked = true;
            toggleRegisterType();
        }
        function closeRegisterModal() { document.getElementById('registerModal').classList.add('hidden'); stopStream(); }
        function openSellModal() { document.getElementById('sellModal').classList.remove('hidden'); }
        function closeSellModal() { document.getElementById('sellModal').classList.add('hidden'); }
        
        const scrollBtn = document.getElementById("scrollToTopBtn");
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) { scrollBtn.classList.remove("hidden"); scrollBtn.classList.add("flex"); } 
            else { scrollBtn.classList.add("hidden"); scrollBtn.classList.remove("flex"); }
        };
    </script>