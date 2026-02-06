<?php
session_start();
include 'config/koneksi.php';

// Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$email = $_SESSION['user'];

// Ambil Data User
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Hitung Statistik Ringkas
$q_iklan = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk WHERE penjual='$email'");
$d_iklan = mysqli_fetch_assoc($q_iklan);

$q_wish = mysqli_query($conn, "SELECT COUNT(*) as total FROM wishlist WHERE user_email='$email'");
$d_wish = mysqli_fetch_assoc($q_wish);

// Foto Profil
$foto = !empty($user['foto_profil']) ? $user['foto_profil'] : "https://ui-avatars.com/api/?name=".urlencode($user['nama'])."&background=0F766E&color=fff";

include 'templates/header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-8 min-h-screen">
    
    <!-- Kartu Profil -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6 flex items-center gap-5">
        <div class="relative">
            <img src="<?php echo $foto; ?>" class="w-20 h-20 rounded-full object-cover border-2 border-gray-100">
            <?php if($user['is_verified']): ?>
                <div class="absolute bottom-0 right-0 bg-blue-500 text-white rounded-full p-1 border-2 border-white text-xs" title="Terverifikasi">
                    <i class="fa-solid fa-check"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-gray-900 truncate"><?php echo htmlspecialchars($user['nama']); ?></h2>
            <p class="text-gray-500 text-sm truncate"><?php echo htmlspecialchars($user['email']); ?></p>
            <div class="mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo ($user['role'] == 'penjual') ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </div>
        </div>
        <a href="pengaturan.php" class="text-gray-400 hover:text-nabire-primary transition">
            <i class="fa-solid fa-pen-to-square text-xl"></i>
        </a>
    </div>

    <!-- Statistik Singkat -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <a href="profil.php" class="bg-teal-50 p-4 rounded-xl border border-teal-100 flex flex-col items-center justify-center hover:bg-teal-100 transition">
            <span class="text-2xl font-bold text-nabire-primary"><?php echo $d_iklan['total']; ?></span>
            <span class="text-xs text-teal-700 font-medium">Iklan Saya</span>
        </a>
        <a href="wishlist.php" class="bg-red-50 p-4 rounded-xl border border-red-100 flex flex-col items-center justify-center hover:bg-red-100 transition">
            <span class="text-2xl font-bold text-red-600"><?php echo $d_wish['total']; ?></span>
            <span class="text-xs text-red-700 font-medium">Favorit</span>
        </a>
    </div>

    <!-- Menu Navigasi -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="divide-y divide-gray-100">
            
            <a href="profil.php" class="flex items-center justify-between p-4 hover:bg-gray-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-store"></i></div>
                    <span class="font-medium text-gray-700 group-hover:text-blue-600">Kelola Toko / Iklan</span>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            </a>

            <a href="wishlist.php" class="flex items-center justify-between p-4 hover:bg-gray-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center"><i class="fa-solid fa-heart"></i></div>
                    <span class="font-medium text-gray-700 group-hover:text-red-600">Barang Favorit</span>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            </a>

            <a href="notifikasi.php" class="flex items-center justify-between p-4 hover:bg-gray-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center"><i class="fa-solid fa-bell"></i></div>
                    <span class="font-medium text-gray-700 group-hover:text-yellow-600">Notifikasi</span>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            </a>

            <a href="pengaturan.php" class="flex items-center justify-between p-4 hover:bg-gray-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center"><i class="fa-solid fa-gear"></i></div>
                    <span class="font-medium text-gray-700 group-hover:text-gray-900">Pengaturan Akun</span>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
            </a>

            <!-- Menu Khusus Admin -->
            <?php if($user['role'] == 'admin'): ?>
            <a href="admin/dashboard.php" class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition group border-l-4 border-nabire-primary">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-nabire-primary text-white flex items-center justify-center"><i class="fa-solid fa-gauge"></i></div>
                    <span class="font-bold text-nabire-primary">Panel Admin</span>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square text-nabire-primary text-xs"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Menu Bantuan & Lainnya -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="divide-y divide-gray-100">
            <a href="bantuan.php" class="flex items-center justify-between p-4 hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <i class="fa-regular fa-circle-question text-gray-400 w-6 text-center"></i>
                    <span class="text-gray-600">Pusat Bantuan</span>
                </div>
            </a>
            <a href="tentang.php" class="flex items-center justify-between p-4 hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-info text-gray-400 w-6 text-center"></i>
                    <span class="text-gray-600">Tentang Aplikasi</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Tombol Keluar -->
    <div class="text-center">
        <a href="auth.php?logout=true" class="inline-flex items-center justify-center text-red-500 font-medium hover:bg-red-50 px-6 py-2 rounded-full transition">
            <i class="fa-solid fa-power-off mr-2"></i> Keluar dari Akun
        </a>
        <p class="text-xs text-gray-400 mt-4">Versi 1.0.0 &bull; NokenMART</p>
    </div>

</div>

<?php include 'templates/footer.php'; ?>