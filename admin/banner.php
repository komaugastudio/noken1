<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// --- LOGIKA TAMBAH BANNER ---
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $subjudul = mysqli_real_escape_string($conn, $_POST['subjudul']);
    
    $gambar = 'https://placehold.co/1200x600?text=Banner+Baru'; // Default

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $name = "banner_" . uniqid() . "." . $ext;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $name)) {
            $gambar = "assets/img/" . $name;
        }
    }

    // Matikan banner lain dulu agar hanya 1 yang aktif (Opsional)
    mysqli_query($conn, "UPDATE banners SET aktif='tidak'");

    $query = "INSERT INTO banners (judul, subjudul, gambar, aktif) VALUES ('$judul', '$subjudul', '$gambar', 'ya')";
    mysqli_query($conn, $query);
    echo "<script>alert('Banner berhasil ditambahkan!'); window.location='banner.php';</script>";
}

// --- LOGIKA UPDATE BANNER (BARU) ---
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $subjudul = mysqli_real_escape_string($conn, $_POST['subjudul']);
    $gambar_lama = $_POST['gambar_lama'];
    
    $gambar = $gambar_lama; // Default pakai yang lama

    // Cek jika ada gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/";
        $ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $name = "banner_" . uniqid() . "." . $ext;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $name)) {
            $gambar = "assets/img/" . $name;
            // Hapus gambar lama jika file fisik dan bukan URL eksternal
            if (strpos($gambar_lama, 'assets/img/') !== false && file_exists("../" . $gambar_lama)) {
                unlink("../" . $gambar_lama);
            }
        }
    }

    $query = "UPDATE banners SET judul='$judul', subjudul='$subjudul', gambar='$gambar' WHERE id=$id";
    if(mysqli_query($conn, $query)) {
        echo "<script>alert('Banner berhasil diperbarui!'); window.location='banner.php';</script>";
    } else {
        echo "<script>alert('Gagal update.');</script>";
    }
}

// --- LOGIKA FETCH DATA UNTUK EDIT ---
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM banners WHERE id=$id_edit");
    if(mysqli_num_rows($q_edit) > 0){
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

// --- LOGIKA AKTIFKAN BANNER ---
if (isset($_GET['aktifkan'])) {
    $id = (int)$_GET['aktifkan'];
    mysqli_query($conn, "UPDATE banners SET aktif='tidak'"); // Reset semua
    mysqli_query($conn, "UPDATE banners SET aktif='ya' WHERE id=$id"); // Set yang dipilih
    echo "<script>window.location='banner.php';</script>";
}

// --- LOGIKA HAPUS BANNER ---
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $q = mysqli_query($conn, "SELECT gambar FROM banners WHERE id=$id");
    $data = mysqli_fetch_assoc($q);
    
    // Hapus file fisik jika ada di server
    if (strpos($data['gambar'], 'assets/img/') !== false && file_exists("../" . $data['gambar'])) {
        unlink("../" . $data['gambar']);
    }
    
    mysqli_query($conn, "DELETE FROM banners WHERE id=$id");
    echo "<script>alert('Banner dihapus.'); window.location='banner.php';</script>";
}

$banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Banner - Admin NokenMART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white hidden md:block">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-teal-400">Noken<span class="text-yellow-500">ADMIN</span></h1>
        </div>
        <nav class="mt-6">
            <a href="dashboard.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gauge mr-3"></i> Dashboard</a>
            <a href="produk.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-box mr-3"></i> Kelola Produk</a>
            <a href="kategori.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-tags mr-3"></i> Kelola Kategori</a>
            <!-- Menu Aktif -->
            <a href="banner.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-image mr-3"></i> Kelola Banner</a>
            
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Banner Depan</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Tambah / Edit -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-4">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">
                        <?php echo $edit_data ? 'Edit Banner' : 'Upload Banner Baru'; ?>
                    </h3>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <?php if($edit_data): ?>
                            <!-- Input Hidden untuk ID saat Edit -->
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($edit_data['gambar']); ?>">
                        <?php endif; ?>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Judul Utama</label>
                                <input type="text" name="judul" required 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['judul']) : ''; ?>"
                                       placeholder="Contoh: Diskon Akhir Tahun" 
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Sub Judul</label>
                                <textarea name="subjudul" rows="2" placeholder="Keterangan singkat..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none"><?php echo $edit_data ? htmlspecialchars($edit_data['subjudul']) : ''; ?></textarea>
                            </div>
                            
                            <!-- Preview Gambar Saat Edit -->
                            <?php if($edit_data): ?>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Gambar Saat Ini:</label>
                                    <img src="<?php echo strpos($edit_data['gambar'], 'http') === 0 ? $edit_data['gambar'] : '../' . $edit_data['gambar']; ?>" class="h-24 w-full object-cover rounded border">
                                </div>
                            <?php endif; ?>

                            <div>
                                <label class="block text-sm text-gray-600 mb-1">
                                    <?php echo $edit_data ? 'Ganti Gambar (Opsional)' : 'File Gambar (Landscape)'; ?>
                                </label>
                                <input type="file" name="gambar" accept="image/*" <?php echo $edit_data ? '' : 'required'; ?> class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            </div>

                            <div class="flex gap-2 pt-2">
                                <?php if($edit_data): ?>
                                    <button type="submit" name="update" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-lg transition shadow">
                                        <i class="fa-solid fa-save mr-2"></i> Update
                                    </button>
                                    <a href="banner.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</a>
                                <?php else: ?>
                                    <button type="submit" name="tambah" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 rounded-lg transition shadow">
                                        <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload & Aktifkan
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Daftar Banner -->
            <div class="lg:col-span-2 space-y-4">
                <?php while($row = mysqli_fetch_assoc($banners)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row <?php echo $row['aktif'] == 'ya' ? 'ring-2 ring-teal-500' : 'opacity-80'; ?>">
                    <div class="w-full md:w-48 h-48 md:h-auto relative">
                        <img src="<?php echo strpos($row['gambar'], 'http') === 0 ? $row['gambar'] : '../' . $row['gambar']; ?>" class="w-full h-full object-cover">
                        <?php if($row['aktif'] == 'ya'): ?>
                            <div class="absolute top-2 left-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">AKTIF</div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($row['judul']); ?></h4>
                            <p class="text-gray-500 text-sm mt-1"><?php echo htmlspecialchars($row['subjudul']); ?></p>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3 items-center">
                            <?php if($row['aktif'] == 'tidak'): ?>
                                <a href="banner.php?aktifkan=<?php echo $row['id']; ?>" class="text-teal-600 hover:text-teal-800 text-sm font-medium border border-teal-200 px-3 py-1 rounded-full hover:bg-teal-50 transition">
                                    <i class="fa-regular fa-circle-check mr-1"></i> Jadikan Utama
                                </a>
                            <?php endif; ?>
                            
                            <!-- Tombol Edit -->
                            <a href="banner.php?edit=<?php echo $row['id']; ?>" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium border border-yellow-200 px-3 py-1 rounded-full hover:bg-yellow-50 transition">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                            </a>

                            <a href="banner.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus banner ini?')" class="text-red-500 hover:text-red-700 text-sm font-medium ml-auto border border-red-200 px-3 py-1 rounded-full hover:bg-red-50 transition">
                                <i class="fa-solid fa-trash mr-1"></i> Hapus
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>