<?php
session_start();
include 'config/koneksi.php';

// Cek Login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$email = $_SESSION['user'];

// Ambil data user terbaru
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

include 'templates/header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-nabire-primary px-6 py-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-white">Pengaturan Akun</h1>
            <p class="text-teal-100 text-xs mt-1">Perbarui informasi profil dan foto Anda</p>
        </div>
        
        <div class="p-8">
            <form action="auth.php" method="POST" enctype="multipart/form-data">
                <div class="space-y-8">
                    
                    <!-- Foto Profil -->
                    <div class="flex flex-col items-center">
                        <div class="relative w-28 h-28 mb-4">
                            <?php 
                                // Cek apakah user punya foto profil, jika tidak pakai avatar default
                                $foto = !empty($user['foto_profil']) ? $user['foto_profil'] : "https://ui-avatars.com/api/?name=".urlencode($user['nama'])."&background=0F766E&color=fff";
                            ?>
                            <img src="<?php echo $foto; ?>" id="previewFoto" class="w-full h-full rounded-full object-cover border-4 border-gray-100 shadow-sm">
                            
                            <label for="inputFoto" class="absolute bottom-0 right-0 bg-white border border-gray-200 p-2 rounded-full cursor-pointer hover:bg-gray-50 shadow text-gray-600 transition">
                                <i class="fa-solid fa-camera"></i>
                                <input type="file" id="inputFoto" name="foto_profil" accept="image/*" class="hidden" onchange="previewImage(this)">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">Klik ikon kamera untuk mengganti foto</p>
                    </div>

                    <!-- Informasi Dasar -->
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-4 border-b border-gray-200 pb-2">Data Diri</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email (Tidak bisa diubah)</label>
                                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled 
                                    class="w-full border border-gray-300 bg-gray-100 text-gray-500 rounded-lg px-4 py-2 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                                <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($user['whatsapp']); ?>" required 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Keamanan -->
                    <div class="bg-red-50 p-5 rounded-xl border border-red-100">
                        <h3 class="font-bold text-red-800 mb-4 border-b border-red-200 pb-2">Ganti Password</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti password" 
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex gap-3 pt-2">
                        <button type="submit" name="update_profile" class="flex-1 bg-nabire-secondary hover:bg-yellow-600 text-white font-bold py-3 rounded-lg shadow-md transition transform active:scale-95">
                            Simpan Perubahan
                        </button>
                        <a href="index.php" class="px-6 py-3 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script untuk preview foto sebelum diupload
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewFoto').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'templates/footer.php'; ?>