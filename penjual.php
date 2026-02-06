<?php
session_start();
include 'config/koneksi.php';

// Ambil Identitas Penjual dari URL
$email_penjual = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';
$nama_penjual = isset($_GET['nama']) ? mysqli_real_escape_string($conn, $_GET['nama']) : '';

// Logika query user
if (!empty($email_penjual)) {
    $where_user = "email = '$email_penjual'";
} else {
    $where_user = "nama = '$nama_penjual'";
}

// 1. Ambil Data Penjual
$query_user = "SELECT * FROM users WHERE $where_user";
$result_user = mysqli_query($conn, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

$display_name = $user_data ? $user_data['nama'] : ($nama_penjual ? $nama_penjual : $email_penjual);
$join_date = $user_data ? date('d F Y', strtotime($user_data['created_at'])) : 'Tidak diketahui';
$verified = ($user_data && $user_data['is_verified'] == 1);
$foto_profil = ($user_data && !empty($user_data['foto_profil'])) ? $user_data['foto_profil'] : "https://ui-avatars.com/api/?name=".urlencode($display_name)."&background=0F766E&color=fff&size=128";

// Email Fix untuk query produk & ulasan
$email_fix = $user_data ? $user_data['email'] : $email_penjual;

// 2. Ambil Barang (Cari berdasarkan email jika ada, fallback nama)
if ($user_data) {
    $query_produk = "SELECT * FROM produk WHERE penjual = '$email_fix' AND status = 'aktif' ORDER BY id DESC";
} else {
    $query_produk = "SELECT * FROM produk WHERE (penjual = '$email_penjual' OR penjual = '$nama_penjual') AND status = 'aktif' ORDER BY id DESC";
}
$result_produk = mysqli_query($conn, $query_produk);
$jumlah_barang = mysqli_num_rows($result_produk);

// 3. LOGIKA KIRIM ULASAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_ulasan'])) {
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Login dulu untuk memberi ulasan!'); window.location='penjual.php?email=$email_fix';</script>";
        exit;
    }
    
    $email_saya = $_SESSION['user'];
    $nama_saya = $_SESSION['nama'];
    $rating = (int)$_POST['rating'];
    $komentar = mysqli_real_escape_string($conn, $_POST['komentar']);
    
    $q_review = "INSERT INTO ulasan (email_pembeli, nama_pembeli, email_penjual, rating, komentar) 
                 VALUES ('$email_saya', '$nama_saya', '$email_fix', '$rating', '$komentar')";
    
    if (mysqli_query($conn, $q_review)) {
        echo "<script>alert('Terima kasih atas ulasan Anda!'); window.location='penjual.php?email=$email_fix';</script>";
    }
}

// 4. AMBIL DATA ULASAN
$q_ulasan = mysqli_query($conn, "SELECT * FROM ulasan WHERE email_penjual = '$email_fix' ORDER BY id DESC");
$jml_ulasan = mysqli_num_rows($q_ulasan);

// Hitung Rata-rata Rating
$avg_rating = 0;
if ($jml_ulasan > 0) {
    $q_avg = mysqli_query($conn, "SELECT AVG(rating) as rata FROM ulasan WHERE email_penjual = '$email_fix'");
    $d_avg = mysqli_fetch_assoc($q_avg);
    $avg_rating = round($d_avg['rata'], 1);
}

function formatRupiah($angka){ return "Rp " . number_format($angka,0,',','.'); }

include 'templates/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Profil -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-8 flex flex-col md:flex-row items-center md:items-start gap-8">
        <div class="relative">
            <img src="<?php echo htmlspecialchars($foto_profil); ?>" 
                 class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
            <?php if($verified): ?>
                <div class="absolute bottom-1 right-1 bg-blue-500 text-white rounded-full p-1.5 border-2 border-white shadow-sm" title="Terverifikasi">
                    <i class="fa-solid fa-check text-sm"></i>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center md:text-left flex-1">
            <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($display_name); ?></h1>
            
            <!-- Rating Star Display -->
            <div class="flex items-center justify-center md:justify-start gap-2 mb-4 text-yellow-400 text-lg">
                <?php
                for($i=1; $i<=5; $i++) {
                    if($i <= $avg_rating) echo '<i class="fa-solid fa-star"></i>';
                    elseif($i - 0.5 <= $avg_rating) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                    else echo '<i class="fa-regular fa-star text-gray-300"></i>';
                }
                ?>
                <span class="text-gray-600 text-sm font-medium ml-1">(<?php echo $avg_rating; ?> / 5.0)</span>
                <span class="text-gray-400 text-sm">• <?php echo $jml_ulasan; ?> Ulasan</span>
            </div>

            <div class="flex flex-wrap justify-center md:justify-start gap-4 text-sm text-gray-600 mb-6">
                <span class="flex items-center gap-1"><i class="fa-solid fa-calendar-days text-nabire-primary"></i> Bergabung: <?php echo $join_date; ?></span>
                <span class="flex items-center gap-1"><i class="fa-solid fa-box-open text-nabire-primary"></i> <?php echo $jumlah_barang; ?> Iklan Aktif</span>
            </div>

            <?php 
                $wa_penjual = "628123456789"; 
                if ($user_data) $wa_penjual = $user_data['whatsapp'];
            ?>
            <a href="https://wa.me/<?php echo $wa_penjual; ?>?text=Halo..." target="_blank" 
               class="inline-flex items-center px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-full transition shadow-md">
                <i class="fa-brands fa-whatsapp mr-2 text-lg"></i> Chat Penjual
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: ETALASE BARANG -->
        <div class="lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-800 border-l-4 border-nabire-secondary pl-3 mb-6">Etalase Barang</h2>
            
            <?php if($jumlah_barang > 0): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php while($row = mysqli_fetch_assoc($result_produk)): ?>
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full relative">
                        <!-- Badge Diskon -->
                        <?php if($row['diskon'] > 0): ?>
                            <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg z-10">
                                -<?php echo $row['diskon']; ?>%
                            </div>
                        <?php endif; ?>

                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="block relative h-40 bg-gray-200 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                        </a>
                        <div class="p-3 flex flex-col flex-grow">
                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="block">
                                <h3 class="font-semibold text-gray-800 line-clamp-2 mb-1 text-sm"><?php echo htmlspecialchars($row['judul']); ?></h3>
                            </a>
                            
                            <?php if($row['diskon'] > 0): 
                                $harga_diskon = $row['harga'] - ($row['harga'] * $row['diskon'] / 100);
                            ?>
                                <p class="text-gray-400 text-xs line-through"><?php echo formatRupiah($row['harga']); ?></p>
                                <p class="text-red-500 font-bold text-base"><?php echo formatRupiah($harga_diskon); ?></p>
                            <?php else: ?>
                                <p class="text-nabire-secondary font-bold text-base"><?php echo formatRupiah($row['harga']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                    <i class="fa-solid fa-store-slash text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Etalase Kosong</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- KOLOM KANAN: ULASAN -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-regular fa-star"></i> Ulasan Pembeli
                </h2>

                <!-- Form Ulasan (Hanya jika login dan bukan profil sendiri) -->
                <?php if(isset($_SESSION['user']) && $_SESSION['user'] != $email_fix): ?>
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <form action="" method="POST">
                        <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Beri Nilai Penjual Ini</label>
                        <div class="mb-3">
                            <select name="rating" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-nabire-primary outline-none">
                                <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                <option value="3">⭐⭐⭐ (Cukup)</option>
                                <option value="2">⭐⭐ (Kurang)</option>
                                <option value="1">⭐ (Kecewa)</option>
                            </select>
                        </div>
                        <textarea name="komentar" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-nabire-primary outline-none mb-2" placeholder="Bagaimana pengalaman Anda?"></textarea>
                        <button type="submit" name="kirim_ulasan" class="w-full bg-nabire-primary text-white py-2 rounded-lg font-bold text-sm hover:bg-teal-800 transition">Kirim Ulasan</button>
                    </form>
                </div>
                <?php endif; ?>

                <!-- List Ulasan -->
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1 custom-scrollbar">
                    <?php if($jml_ulasan > 0): ?>
                        <?php while($rev = mysqli_fetch_assoc($q_ulasan)): ?>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-bold text-sm text-gray-800"><?php echo htmlspecialchars($rev['nama_pembeli']); ?></span>
                                <span class="text-yellow-400 text-xs flex">
                                    <?php for($r=0; $r<$rev['rating']; $r++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                </span>
                            </div>
                            <p class="text-gray-600 text-xs italic mb-1">"<?php echo htmlspecialchars($rev['komentar']); ?>"</p>
                            <span class="text-[10px] text-gray-400"><?php echo date('d M Y', strtotime($rev['tanggal'])); ?></span>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-400 text-sm py-4">Belum ada ulasan.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    /* Scrollbar Tipis untuk Ulasan */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #bbb; }
</style>

<?php include 'templates/footer.php'; ?>