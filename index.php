<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/koneksi.php';

/**
 * LOGIKA TAMBAH BARANG (POST)
 * Dengan Proteksi Keamanan:
 * 1. Validasi getimagesize()
 * 2. Sanitasi mysqli_real_escape_string
 * 3. Folder terorganisir: assets/img/produk/
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_barang'])) {
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Silakan login terlebih dahulu!'); window.location='index.php';</script>";
        exit;
    }

    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = (int)$_POST['harga'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $kondisi = mysqli_real_escape_string($conn, $_POST['kondisi']);
    $diskon = isset($_POST['diskon']) ? (int)$_POST['diskon'] : 0;
    $is_premium = isset($_POST['is_premium']) ? 1 : 0;
    
    $penjual = $_SESSION['user'];
    $whatsapp = $_SESSION['wa']; 
    $gambar = 'https://placehold.co/400x300?text=No+Image';

    // Proses Upload Gambar Produk
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Cek apakah benar-benar gambar
        if (getimagesize($_FILES['gambar']['tmp_name']) !== false) {
            $target_dir = "assets/img/produk/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $file_name = "prod_" . uniqid() . '.' . $ext;
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $gambar = $target_file;
            }
        } else {
            echo "<script>alert('File yang diunggah bukan gambar valid!'); window.location='index.php';</script>";
            exit;
        }
    }

    $query_insert = "INSERT INTO produk (judul, deskripsi, harga, diskon, kategori, lokasi, kondisi, gambar, penjual, whatsapp, status, is_premium, waktu_sundul) 
                     VALUES ('$judul', '$deskripsi', $harga, $diskon, '$kategori', '$lokasi', '$kondisi', '$gambar', '$penjual', '$whatsapp', 'aktif', $is_premium, NOW())";
    
    if(mysqli_query($conn, $query_insert)) {
        echo "<script>alert('Iklan Berhasil Ditayangkan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}

// ... Logika Tampilan tetap sama menggunakan templates/header.php, section.php, footer.php ...

// Ambil Barang untuk Tampilan (Contoh: Terbaru)
$query_tampil = "SELECT * FROM produk WHERE status='aktif' ORDER BY is_premium DESC, waktu_sundul DESC LIMIT 12";
$result_produk = mysqli_query($conn, $query_tampil);

include 'templates/header.php';
include 'templates/section.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Rekomendasi Terbaru</h2>
        <a href="pencarian.php" class="text-nabire-primary font-bold hover:underline">Lihat Semua</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        <?php while($row = mysqli_fetch_assoc($result_produk)): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full group relative">
                <?php if($row['is_premium']): ?>
                    <div class="absolute top-2 right-2 z-10 bg-yellow-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                        <i class="fa-solid fa-crown"></i> PREMIUM
                    </div>
                <?php endif; ?>
                
                <a href="detail.php?id=<?php echo $row['id']; ?>" class="block h-48 overflow-hidden bg-gray-100">
                    <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x300'">
                </a>
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-bold text-gray-800 line-clamp-2 mb-2 text-sm md:text-base"><?php echo htmlspecialchars($row['judul']); ?></h3>
                    <p class="text-nabire-secondary font-bold text-lg">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                    <div class="mt-auto pt-4 flex items-center text-gray-500 text-xs gap-1">
                        <i class="fa-solid fa-location-dot text-red-400"></i> <?php echo htmlspecialchars($row['lokasi']); ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>