<?php
session_start(); // Pastikan session dimulai untuk fitur komentar
include 'config/koneksi.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- LOGIKA VIEW COUNTER ---
mysqli_query($conn, "UPDATE produk SET views = views + 1 WHERE id = $id");

// UPDATE QUERY: Ambil data produk DAN data user penjual (Join) untuk cek verifikasi
$query = "SELECT produk.*, users.nama as nama_penjual_asli, users.is_verified, users.whatsapp as wa_user 
          FROM produk 
          LEFT JOIN users ON produk.penjual = users.email 
          WHERE produk.id = $id";

$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    header("Location: 404.php");
    exit;
}

// Tentukan Nama Tampil & WA (Prioritas dari tabel users jika ada)
$nama_tampil = !empty($item['nama_penjual_asli']) ? $item['nama_penjual_asli'] : $item['penjual'];
$wa_tampil = !empty($item['wa_user']) ? $item['wa_user'] : $item['whatsapp'];

// --- LOGIKA KIRIM KOMENTAR ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_komentar'])) {
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Anda harus login untuk bertanya!'); window.location='detail.php?id=$id';</script>";
        exit;
    }
    
    $user_email = $_SESSION['user'];
    $nama_user = $_SESSION['nama']; 
    $isi = mysqli_real_escape_string($conn, $_POST['isi_komentar']);
    
    $q_komen = "INSERT INTO komentar (produk_id, user_email, nama_user, isi_komentar) VALUES ($id, '$user_email', '$nama_user', '$isi')";
    mysqli_query($conn, $q_komen);
    
    // Kirim Notifikasi ke Penjual
    $email_penjual = $item['penjual']; 
    if ($email_penjual != $user_email) {
        $pesan_notif = "Ada pertanyaan baru dari <strong>$nama_user</strong> di produk: " . $item['judul'];
        $link_notif = "detail.php?id=$id#diskusi";
        $q_notif = "INSERT INTO notifikasi (user_email, pesan, link) VALUES ('$email_penjual', '$pesan_notif', '$link_notif')";
        mysqli_query($conn, $q_notif);
    }

    header("Location: detail.php?id=$id#diskusi");
    exit;
}

// --- LOGIKA HAPUS KOMENTAR ---
if (isset($_GET['hapus_komen'])) {
    $id_komentar = (int)$_GET['hapus_komen'];
    $q_cek = mysqli_query($conn, "SELECT user_email FROM komentar WHERE id=$id_komentar");
    $d_cek = mysqli_fetch_assoc($q_cek);
    
    if (isset($_SESSION['user']) && ($d_cek['user_email'] == $_SESSION['user'] || (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'))) {
        mysqli_query($conn, "DELETE FROM komentar WHERE id=$id_komentar");
        header("Location: detail.php?id=$id#diskusi");
        exit;
    }
}

$komentar_list = mysqli_query($conn, "SELECT * FROM komentar WHERE produk_id = $id ORDER BY id DESC");
$jumlah_komentar = mysqli_num_rows($komentar_list);

function formatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

$kategori_terkait = $item['kategori'];
$query_terkait = "SELECT * FROM produk WHERE kategori = '$kategori_terkait' AND id != $id AND status='aktif' ORDER BY RAND() LIMIT 4";
$result_terkait = mysqli_query($conn, $query_terkait);

include 'templates/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <?php if($item['status'] == 'terjual'): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex items-center gap-3" role="alert">
        <i class="fa-solid fa-circle-exclamation text-xl"></i>
        <div>
            <p class="font-bold">Barang Ini Sudah Terjual</p>
            <p class="text-sm">Anda tidak bisa lagi menghubungi penjual untuk barang ini.</p>
        </div>
    </div>
    <?php endif; ?>

    <nav class="text-sm text-gray-500 mb-6">
        <a href="index.php" class="hover:text-nabire-primary">Beranda</a> <span class="mx-2">/</span>
        <a href="index.php?kategori=<?php echo $item['kategori']; ?>" class="hover:text-nabire-primary capitalize"><?php echo $item['kategori']; ?></a> <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium truncate"><?php echo htmlspecialchars($item['judul']); ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="md:col-span-2">
            <!-- Foto Produk -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden p-2 relative">
                <img src="<?php echo htmlspecialchars($item['gambar']); ?>" class="w-full h-auto object-cover rounded-xl <?php echo ($item['status'] == 'terjual') ? 'grayscale opacity-75' : ''; ?>" onerror="this.src='https://placehold.co/800x600?text=Gambar+Rusak'">
                
                <?php if($item['status'] == 'terjual'): ?>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="bg-red-600 text-white text-3xl font-bold px-10 py-4 rounded-lg transform -rotate-12 border-4 border-white shadow-2xl tracking-widest">SOLD OUT</span>
                    </div>
                <?php endif; ?>

                <!-- Badge Diskon Besar di Detail -->
                <?php if(isset($item['diskon']) && $item['diskon'] > 0 && $item['status'] == 'aktif'): ?>
                    <div class="absolute top-4 left-4 bg-red-600 text-white font-bold px-4 py-2 rounded-lg shadow-lg text-lg">
                        Diskon <?php echo $item['diskon']; ?>%
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Deskripsi -->
            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                
                <!-- NEW: KONDISI BARANG -->
                <div class="mb-4 pb-4 border-b border-gray-100 flex items-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wide mr-2">Kondisi:</span>
                    <?php 
                        $kondisi = isset($item['kondisi']) ? $item['kondisi'] : 'Bekas'; 
                        $badge_color = ($kondisi == 'Baru') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800';
                    ?>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold <?php echo $badge_color; ?>">
                        <?php echo htmlspecialchars($kondisi); ?>
                    </span>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-4">Deskripsi Barang</h3>
                <div class="text-gray-600 leading-relaxed whitespace-pre-wrap"><?php echo !empty($item['deskripsi']) ? htmlspecialchars($item['deskripsi']) : "Penjual belum menambahkan deskripsi rinci untuk barang ini. Silakan hubungi penjual melalui WhatsApp untuk menanyakan kondisi barang, kelengkapan, dan negosiasi harga."; ?></div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                    <span><i class="fa-regular fa-clock mr-1"></i> Diupload: <?php echo date('d M Y', strtotime($item['tanggal_upload'])); ?></span>
                    <span class="flex items-center gap-1 text-nabire-primary bg-teal-50 px-3 py-1 rounded-full">
                        <i class="fa-regular fa-eye"></i> <strong><?php echo number_format($item['views']); ?></strong> x dilihat
                    </span>
                </div>
            </div>

            <!-- DISKUSI -->
            <div id="diskusi" class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-regular fa-comments"></i> Diskusi Produk (<?php echo $jumlah_komentar; ?>)
                </h3>

                <?php if(isset($_SESSION['user'])): ?>
                    <form action="" method="POST" class="mb-8 flex gap-3">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nama']); ?>&background=0F766E&color=fff" class="w-10 h-10 rounded-full hidden sm:block">
                        <div class="flex-1">
                            <textarea name="isi_komentar" required rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none resize-none" placeholder="Tanya sesuatu tentang barang ini..."></textarea>
                            <div class="text-right mt-2">
                                <button type="submit" name="kirim_komentar" class="bg-nabire-primary text-white px-5 py-2 rounded-full font-bold text-sm hover:bg-teal-800 transition">Kirim</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center mb-8">
                        <p class="text-gray-600 text-sm">Ingin bertanya? <button onclick="openLoginModal()" class="text-nabire-primary font-bold hover:underline">Masuk</button> atau <button onclick="openRegisterModal()" class="text-nabire-primary font-bold hover:underline">Daftar</button> dulu.</p>
                    </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <?php if($jumlah_komentar > 0): ?>
                        <?php while($komen = mysqli_fetch_assoc($komentar_list)): ?>
                        <div class="flex gap-3 animate-fade-in">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($komen['nama_user']); ?>&background=random&color=fff" class="w-10 h-10 rounded-full bg-gray-200">
                            <div class="flex-1 bg-gray-50 rounded-2xl rounded-tl-none px-4 py-3 relative group">
                                <div class="flex justify-between items-center mb-1">
                                    <h5 class="font-bold text-sm text-gray-800">
                                        <?php echo htmlspecialchars($komen['nama_user']); ?>
                                        <?php if($komen['user_email'] == $item['penjual']) echo '<span class="bg-nabire-secondary text-white text-[10px] px-2 py-0.5 rounded-full ml-1">Penjual</span>'; ?>
                                    </h5>
                                    <span class="text-xs text-gray-400"><?php echo date('d M, H:i', strtotime($komen['tanggal'])); ?></span>
                                </div>
                                <p class="text-gray-700 text-sm"><?php echo nl2br(htmlspecialchars($komen['isi_komentar'])); ?></p>
                                <!-- Hapus Komentar -->
                                <?php if(isset($_SESSION['user']) && ($_SESSION['user'] == $komen['user_email'] || (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'))): ?>
                                    <a href="detail.php?id=<?php echo $id; ?>&hapus_komen=<?php echo $komen['id']; ?>" onclick="return confirm('Hapus komentar ini?')" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-xs opacity-0 group-hover:opacity-100 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-400 text-sm italic py-4">Belum ada diskusi. Jadilah yang pertama bertanya!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                
                <h1 class="text-2xl font-bold text-gray-900 mb-2 leading-tight flex items-start gap-2">
                    <?php echo htmlspecialchars($item['judul']); ?>
                    <?php if(isset($item['is_premium']) && $item['is_premium']): ?>
                        <i class="fa-solid fa-crown text-yellow-500 text-lg mt-1" title="Iklan Premium"></i>
                    <?php endif; ?>
                </h1>

                <!-- HARGA DETAIL (Dengan Coret jika Diskon) -->
                <?php if(isset($item['diskon']) && $item['diskon'] > 0): 
                    $harga_asli = $item['harga'];
                    $harga_diskon = $harga_asli - ($harga_asli * $item['diskon'] / 100);
                ?>
                    <div class="mb-6">
                        <span class="text-gray-400 line-through text-lg block"><?php echo formatRupiah($harga_asli); ?></span>
                        <div class="flex items-center gap-2">
                            <span class="text-3xl font-bold text-red-600"><?php echo formatRupiah($harga_diskon); ?></span>
                            <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full">Hemat <?php echo $item['diskon']; ?>%</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-3xl font-bold text-nabire-secondary mb-6"><?php echo formatRupiah($item['harga']); ?></div>
                <?php endif; ?>

                <div class="space-y-4 mb-8">
                    <div class="flex items-start gap-3">
                        <div class="bg-gray-100 p-2 rounded-full text-nabire-primary"><i class="fa-solid fa-location-dot w-5 h-5 flex items-center justify-center"></i></div>
                        <div><p class="text-xs text-gray-500 uppercase font-semibold">Lokasi</p><p class="text-gray-800 font-medium"><?php echo htmlspecialchars($item['lokasi']); ?></p></div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="bg-gray-100 p-2 rounded-full text-nabire-primary"><i class="fa-solid fa-user w-5 h-5 flex items-center justify-center"></i></div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Penjual</p>
                            <a href="penjual.php?email=<?php echo urlencode($item['penjual']); ?>" class="text-gray-800 font-medium hover:text-nabire-primary hover:underline transition flex items-center gap-1">
                                <?php echo htmlspecialchars($nama_tampil); ?>
                                <?php if(isset($item['is_verified']) && $item['is_verified'] == 1): ?>
                                    <i class="fa-solid fa-circle-check text-blue-500 text-sm" title="Penjual Terverifikasi"></i>
                                <?php endif; ?>
                                <i class="fa-solid fa-arrow-right text-xs ml-1 text-gray-400"></i>
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="bg-gray-100 p-2 rounded-full text-nabire-primary"><i class="fa-solid fa-tag w-5 h-5 flex items-center justify-center"></i></div>
                        <div><p class="text-xs text-gray-500 uppercase font-semibold">Kategori</p><p class="text-gray-800 font-medium capitalize"><?php echo htmlspecialchars($item['kategori']); ?></p></div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="space-y-3">
                    <?php if($item['status'] == 'aktif'): ?>
                        <div class="flex gap-2">
                             <!-- Tombol WA -->
                            <a href="https://wa.me/<?php echo $wa_tampil; ?>?text=Halo, saya tertarik dengan iklan Anda '<?php echo urlencode($item['judul']); ?>' di NokenMART." 
                               target="_blank"
                               class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center font-bold py-3 rounded-xl transition shadow-lg flex items-center justify-center gap-2">
                                <i class="fa-brands fa-whatsapp text-xl"></i> Hubungi
                            </a>
                            <!-- Tombol Nego (Trigger Modal) -->
                            <button onclick="openNegoModal()" class="flex-1 bg-white border-2 border-nabire-primary text-nabire-primary hover:bg-teal-50 font-bold py-3 rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                                <i class="fa-solid fa-hand-holding-dollar text-xl"></i> Nego
                            </button>
                        </div>
                    <?php else: ?>
                        <button disabled class="block w-full bg-gray-300 text-gray-500 font-bold py-3 rounded-xl cursor-not-allowed text-center border border-gray-300">
                            Barang Sudah Laku
                        </button>
                    <?php endif; ?>
                    
                    <a href="wishlist.php?add=<?php echo $item['id']; ?>" class="block w-full text-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-3 rounded-xl transition"><i class="fa-regular fa-heart mr-2"></i> Simpan ke Favorit</a>

                    <a href="lapor.php?id=<?php echo $item['id']; ?>" class="block w-full text-center text-red-500 text-sm hover:underline mt-4 font-medium transition-colors hover:text-red-700">
                        <i class="fa-solid fa-flag mr-1"></i> Laporkan barang ini
                    </a>
                </div>

                <div class="mt-8 bg-blue-50 rounded-xl p-4 border border-blue-100">
                    <h4 class="text-sm font-bold text-blue-800 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i> Tips Transaksi Aman
                    </h4>
                    <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                        <li>Jangan transfer uang sebelum barang diterima.</li>
                        <li>Utamakan COD (Ketemuan) di tempat aman.</li>
                        <li>Cek kondisi barang dengan teliti.</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- Produk Serupa -->
    <?php if(mysqli_num_rows($result_terkait) > 0): ?>
    <div class="border-t border-gray-200 pt-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Mungkin Anda Juga Suka</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php while($row = mysqli_fetch_assoc($result_terkait)): ?>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition border border-gray-100 overflow-hidden group">
                <a href="detail.php?id=<?php echo $row['id']; ?>" class="block relative h-40 bg-gray-200 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                    <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded-md capitalize"><?php echo $row['kategori']; ?></div>
                </a>
                <div class="p-3 flex flex-col flex-grow">
                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="block">
                        <h3 class="font-semibold text-gray-800 line-clamp-2 mb-1 text-sm group-hover:text-nabire-primary transition"><?php echo htmlspecialchars($row['judul']); ?></h3>
                    </a>
                    <p class="text-nabire-secondary font-bold text-base mb-2"><?php echo formatRupiah($row['harga']); ?></p>
                    <div class="flex items-center text-gray-500 text-xs mt-auto"><i class="fa-solid fa-location-dot mr-1"></i> <?php echo htmlspecialchars($row['lokasi']); ?></div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- MODAL NEGO (Penting untuk fitur Nego) -->
<div id="negoModal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl overflow-hidden transform scale-100 transition-transform">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Tawar Harga</h3>
            <p class="text-sm text-gray-500 mb-6">Masukkan harga tawaran Anda untuk barang ini.</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Harga Awal</label>
                    <div class="text-lg font-bold text-nabire-secondary"><?php echo formatRupiah($item['harga']); ?></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Harga Tawaran (Rp)</label>
                    <input type="number" id="inputNego" class="w-full border-2 border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-nabire-primary focus:border-nabire-primary outline-none font-bold text-gray-800" placeholder="Contoh: 1000000">
                </div>
                <button onclick="kirimNego()" class="w-full bg-nabire-primary hover:bg-teal-800 text-white font-bold py-3 rounded-xl shadow-md transition transform active:scale-95">
                    Kirim Tawaran via WA
                </button>
                <button onclick="closeNegoModal()" class="w-full text-gray-500 font-medium py-2 hover:text-gray-700 transition">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT UNTUK NEGO & SHARE -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Variables
    const modalNego = document.getElementById('negoModal');
    const inputNego = document.getElementById('inputNego');
    const waPenjual = "<?php echo $wa_tampil; ?>";
    const namaBarang = "<?php echo $item['judul']; ?>";
    const linkProduk = window.location.href; // Get current URL

    // Open Modal
    function openNegoModal() {
        modalNego.classList.remove('hidden');
        inputNego.focus();
    }

    // Close Modal
    function closeNegoModal() {
        modalNego.classList.add('hidden');
    }

    // Kirim Nego
    function kirimNego() {
        const hargaTawar = inputNego.value;
        if (!hargaTawar) {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Masukkan harga tawaran dulu!', confirmButtonColor: '#0F766E' });
            return;
        }

        const formatTawar = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(hargaTawar);
        
        const pesan = `Halo, saya tertarik dengan barang *${namaBarang}*.\nSaya mau menawar seharga *${formatTawar}*.\n\nLink: ${linkProduk}\n\nApakah boleh?`;
        
        const linkWA = `https://wa.me/${waPenjual}?text=${encodeURIComponent(pesan)}`;
        window.open(linkWA, '_blank');
        closeNegoModal();
    }
</script>

<?php include 'templates/footer.php'; ?>