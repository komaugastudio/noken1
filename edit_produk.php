<?php
session_start();
include 'config/koneksi.php';

// 1. CEK LOGIN
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login dulu!'); window.location='index.php';</script>";
    exit;
}

// 2. AMBIL ID BARANG
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_email = $_SESSION['user'];

// 3. CEK KEPEMILIKAN
$query = "SELECT * FROM produk WHERE id = $id AND penjual = '$user_email'";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    echo "<script>alert('Barang tidak ditemukan!'); window.location='profil.php';</script>";
    exit;
}

// 4. PROSES UPDATE DATA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_barang'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $harga = (int)$_POST['harga'];
    $kategori = $_POST['kategori'];
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    
    // TANGKAP KONDISI (BARU)
    $kondisi = isset($_POST['kondisi']) ? $_POST['kondisi'] : 'Bekas';
    
    // Default gambar lama
    $gambar = $item['gambar'];

    // Cek upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "assets/img/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $name = uniqid() . '.' . $ext;
        $target_file = $target_dir . $name;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $target_file;
        }
    }

    // UPDATE QUERY: Tambahkan update kondisi
    $update_query = "UPDATE produk SET 
                     judul='$judul', 
                     harga='$harga', 
                     kategori='$kategori', 
                     lokasi='$lokasi', 
                     kondisi='$kondisi',
                     gambar='$gambar' 
                     WHERE id=$id";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Data iklan berhasil diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "');</script>";
    }
}

include 'templates/header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-nabire-primary px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h1 class="text-xl font-bold text-white">Edit Iklan</h1>
            <a href="profil.php" class="text-white hover:text-gray-200 text-sm"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali</a>
        </div>
        
        <div class="p-8">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                        <input type="text" name="judul" value="<?php echo htmlspecialchars($item['judul']); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                    </div>
                    
                    <!-- FITUR BARU: Edit Kondisi Barang -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi Barang</label>
                        <div class="flex gap-6">
                            <label class="flex items-center cursor-pointer gap-2">
                                <input type="radio" name="kondisi" value="Baru" class="accent-nabire-primary w-4 h-4" <?php if(isset($item['kondisi']) && $item['kondisi'] == 'Baru') echo 'checked'; ?>>
                                <span class="text-sm text-gray-600">Baru</span>
                            </label>
                            <label class="flex items-center cursor-pointer gap-2">
                                <input type="radio" name="kondisi" value="Bekas" class="accent-nabire-primary w-4 h-4" <?php if(!isset($item['kondisi']) || $item['kondisi'] == 'Bekas') echo 'checked'; ?>>
                                <span class="text-sm text-gray-600">Bekas</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="harga" value="<?php echo $item['harga']; ?>" required 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select name="kategori" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                                <option value="kendaraan" <?php if($item['kategori'] == 'kendaraan') echo 'selected'; ?>>Kendaraan</option>
                                <option value="elektronik" <?php if($item['kategori'] == 'elektronik') echo 'selected'; ?>>Elektronik</option>
                                <option value="properti" <?php if($item['kategori'] == 'properti') echo 'selected'; ?>>Properti</option>
                                <option value="lokal" <?php if($item['kategori'] == 'lokal') echo 'selected'; ?>>Hasil Bumi/Lokal</option>
                                <option value="sewa" <?php if($item['kategori'] == 'sewa') echo 'selected'; ?>>Jasa Sewa</option>
                                <option value="lainnya" <?php if($item['kategori'] == 'lainnya') echo 'selected'; ?>>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input type="text" name="lokasi" value="<?php echo htmlspecialchars($item['lokasi']); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto (Opsional)</label>
                        <input type="file" name="gambar" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-500">
                        <img src="<?php echo htmlspecialchars($item['gambar']); ?>" class="h-24 w-auto mt-2 rounded border border-gray-200 object-cover">
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit" name="update_barang" class="flex-1 bg-nabire-secondary hover:bg-yellow-600 text-white font-bold py-3 rounded-lg shadow-md transition">
                            Simpan Perubahan
                        </button>
                        <a href="profil.php" class="px-6 py-3 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>