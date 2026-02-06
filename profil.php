<?php
session_start();
include 'config/koneksi.php';

// Helper SweetAlert
function sweetAlert($icon, $title, $text, $redirect) {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Proses...</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                confirmButtonColor: '#0F766E',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                window.location = '$redirect';
            });
        </script>
    </body>
    </html>";
    exit;
}

// 1. CEK LOGIN
if (!isset($_SESSION['user'])) {
    sweetAlert('warning', 'Akses Ditolak', 'Silakan login terlebih dahulu untuk mengakses profil!', 'index.php');
}

$user_email = $_SESSION['user'];

// --- LOGIKA SUNDUL IKLAN ---
if (isset($_GET['sundul'])) {
    $id_produk = (int)$_GET['sundul'];
    
    // Cek kepemilikan
    $cek_milik = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id_produk AND penjual='$user_email'");
    if(mysqli_num_rows($cek_milik) > 0) {
        // Update waktu_sundul ke Waktu Sekarang (NOW)
        mysqli_query($conn, "UPDATE produk SET waktu_sundul = NOW() WHERE id=$id_produk");
        sweetAlert('success', 'Berhasil Disundul!', 'Iklan Anda kini berada di posisi teratas halaman depan.', 'profil.php');
    }
}

// 2. LOGIKA UBAH STATUS
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id_produk = (int)$_GET['id'];
    $status_baru = $_GET['status'] == 'terjual' ? 'terjual' : 'aktif';
    
    // Pastikan milik user sendiri
    $cek_milik = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id_produk AND penjual='$user_email'");
    if(mysqli_num_rows($cek_milik) > 0) {
        mysqli_query($conn, "UPDATE produk SET status='$status_baru' WHERE id=$id_produk");
        header("Location: profil.php"); // Redirect langsung agar cepat
        exit;
    }
}

// 3. LOGIKA HAPUS IKLAN
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    
    // Pastikan barang yang dihapus benar-benar milik user ini (Keamanan)
    $cek_milik = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id_hapus AND penjual='$user_email'");
    
    if(mysqli_num_rows($cek_milik) > 0) {
        // Hapus gambar jika ada (opsional, agar server tidak penuh)
        $data = mysqli_fetch_assoc($cek_milik);
        if (file_exists($data['gambar']) && strpos($data['gambar'], 'placehold.co') === false) {
            unlink($data['gambar']);
        }

        mysqli_query($conn, "DELETE FROM produk WHERE id=$id_hapus");
        sweetAlert('success', 'Terhapus', 'Iklan Anda berhasil dihapus.', 'profil.php');
    } else {
        sweetAlert('error', 'Gagal', 'Anda tidak memiliki izin untuk menghapus barang ini.', 'profil.php');
    }
}

// 4. AMBIL DATA IKLAN MILIK USER
// Urutkan berdasarkan waktu_sundul DESC agar yang baru disundul muncul paling atas
$query = "SELECT * FROM produk WHERE penjual = '$user_email' ORDER BY waktu_sundul DESC";
$result = mysqli_query($conn, $query);
$jumlah_iklan = mysqli_num_rows($result);

// Helper Rupiah
function formatRupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}

include 'templates/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Profil Ringkas -->
        <div class="md:w-1/4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(isset($_SESSION['nama']) ? $_SESSION['nama'] : $user_email); ?>&background=0F766E&color=fff&size=128" 
                     class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 break-words"><?php echo htmlspecialchars(isset($_SESSION['nama']) ? $_SESSION['nama'] : $user_email); ?></h2>
                <p class="text-sm text-gray-500 mb-4">Penjual Terverifikasi</p>
                
                <div class="bg-blue-50 text-blue-800 py-2 px-4 rounded-lg text-sm font-semibold">
                    <?php echo $jumlah_iklan; ?> Iklan Total
                </div>
            </div>
        </div>

        <!-- Daftar Iklan -->
        <div class="md:w-3/4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-lg">Kelola Iklan Saya</h3>
                    <button onclick="openSellModal()" class="text-sm bg-nabire-primary text-white px-3 py-1.5 rounded-lg hover:bg-teal-800 transition">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Baru
                    </button>
                </div>

                <?php if($jumlah_iklan > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-3">Barang</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Aksi Cepat</th>
                                    <th class="px-6 py-3 text-center">Menu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50 transition <?php echo ($row['status'] == 'terjual') ? 'bg-gray-50 opacity-75' : ''; ?>">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-12 h-12 rounded object-cover border border-gray-200" onerror="this.src='https://placehold.co/100'">
                                            <div>
                                                <div class="font-semibold text-gray-900 line-clamp-1"><?php echo htmlspecialchars($row['judul']); ?></div>
                                                <div class="text-xs text-gray-500 capitalize"><?php echo $row['kategori']; ?></div>
                                                <div class="text-xs font-bold text-nabire-secondary mt-1"><?php echo formatRupiah($row['harga']); ?></div>
                                                
                                                <!-- View Counter -->
                                                <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                    <i class="fa-regular fa-eye"></i> <?php echo number_format($row['views']); ?>x dilihat
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if($row['status'] == 'aktif'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Terjual
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <!-- Tombol SUNDUL -->
                                        <a href="profil.php?sundul=<?php echo $row['id']; ?>" class="text-purple-600 hover:text-purple-800 font-medium text-xs flex items-center gap-1 border border-purple-200 bg-purple-50 px-2 py-1 rounded shadow-sm hover:shadow transition" title="Naikkan ke posisi atas">
                                            <i class="fa-solid fa-arrow-up"></i> Naikkan
                                        </a>
                                        <div class="text-[10px] text-gray-400 mt-1 pl-1">
                                            Last: <?php echo $row['waktu_sundul'] ? date('d/m H:i', strtotime($row['waktu_sundul'])) : '-'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- Tombol Ubah Status -->
                                            <?php if($row['status'] == 'aktif'): ?>
                                                <a href="profil.php?status=terjual&id=<?php echo $row['id']; ?>" class="text-green-600 hover:text-green-800 p-1" title="Tandai Terjual">
                                                    <i class="fa-solid fa-check-circle"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="profil.php?status=aktif&id=<?php echo $row['id']; ?>" class="text-gray-400 hover:text-green-600 p-1" title="Aktifkan Kembali">
                                                    <i class="fa-solid fa-rotate-left"></i>
                                                </a>
                                            <?php endif; ?>

                                            <a href="detail.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700 p-1" title="Lihat">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <!-- Tombol Edit -->
                                            <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="text-yellow-500 hover:text-yellow-700 p-1" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <!-- Tombol Hapus (Pakai SweetAlert Confirm) -->
                                            <button onclick="confirmHapus('<?php echo $row['id']; ?>')" class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fa-solid fa-box-open text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Belum ada iklan yang ditayangkan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Script SweetAlert untuk Konfirmasi Hapus -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmHapus(id) {
        Swal.fire({
            title: 'Hapus Iklan Ini?',
            text: "Tindakan ini tidak bisa dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'profil.php?hapus=' + id;
            }
        })
    }
</script>

<?php include 'templates/footer.php'; ?>