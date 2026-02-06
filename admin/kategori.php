<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// --- LOGIKA TAMBAH KATEGORI ---
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama)));
    
    // Default ikon (Kosong jika tidak upload)
    $gambar = ""; 

    // Upload Ikon
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/kategori/";
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $name_file = "cat_" . uniqid() . "." . $ext; // Nama unik
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $name_file)) {
            $gambar = "assets/img/kategori/" . $name_file;
        }
    }

    $query = "INSERT INTO kategori (nama_kategori, slug, gambar) VALUES ('$nama', '$slug', '$gambar')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Kategori berhasil ditambah!'); window.location='kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah kategori: " . mysqli_error($conn) . "');</script>";
    }
}

// --- LOGIKA UPDATE KATEGORI (BARU) ---
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama)));
    $gambar_lama = $_POST['gambar_lama'];
    
    $gambar = $gambar_lama; // Default gunakan gambar lama

    // Cek jika ada gambar baru yang diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/kategori/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $name_file = "cat_" . uniqid() . "." . $ext;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $name_file)) {
            $gambar = "assets/img/kategori/" . $name_file;
            
            // Hapus gambar lama jika ada dan bukan URL eksternal
            if (!empty($gambar_lama) && strpos($gambar_lama, 'http') === false && file_exists("../" . $gambar_lama)) {
                unlink("../" . $gambar_lama);
            }
        }
    }

    $query = "UPDATE kategori SET nama_kategori='$nama', slug='$slug', gambar='$gambar' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Kategori berhasil diperbarui!'); window.location='kategori.php';</script>";
    } else {
        echo "<script>alert('Gagal update kategori: " . mysqli_error($conn) . "');</script>";
    }
}

// --- LOGIKA HAPUS KATEGORI ---
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Hapus file gambar fisik jika ada
    $q_cek = mysqli_query($conn, "SELECT gambar FROM kategori WHERE id=$id");
    $d_cek = mysqli_fetch_assoc($q_cek);
    if (!empty($d_cek['gambar']) && file_exists("../" . $d_cek['gambar'])) {
        unlink("../" . $d_cek['gambar']);
    }

    mysqli_query($conn, "DELETE FROM kategori WHERE id=$id");
    echo "<script>alert('Kategori dihapus.'); window.location='kategori.php';</script>";
}

// --- LOGIKA FETCH DATA EDIT ---
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $q_edit = mysqli_query($conn, "SELECT * FROM kategori WHERE id=$id_edit");
    if(mysqli_num_rows($q_edit) > 0){
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

$data_kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin NokenMART</title>
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
            <!-- Menu Aktif -->
            <a href="kategori.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-tags mr-3"></i> Kelola Kategori</a>
            
            <a href="banner.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-image mr-3"></i> Kelola Banner</a>
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="subscribers.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-rss mr-3"></i> Subscriber</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            <a href="faq.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-circle-question mr-3"></i> Kelola FAQ</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Kategori</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Form Tambah / Edit -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-4">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">
                        <?php echo $edit_data ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?>
                    </h3>
                    
                    <form action="" method="POST" enctype="multipart/form-data">
                        <?php if($edit_data): ?>
                            <!-- Input Hidden untuk ID & Gambar Lama saat Edit -->
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($edit_data['gambar']); ?>">
                        <?php endif; ?>

                        <div class="mb-4">
                            <label class="block text-sm text-gray-600 mb-1">Nama Kategori</label>
                            <input type="text" name="nama_kategori" required 
                                   value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama_kategori']) : ''; ?>"
                                   placeholder="Contoh: Kuliner, Jasa..." 
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm text-gray-600 mb-1">
                                <?php echo $edit_data ? 'Ganti Ikon (Opsional)' : 'Ikon / Gambar (Opsional)'; ?>
                            </label>
                            
                            <!-- Preview Gambar Saat Edit -->
                            <?php if($edit_data && !empty($edit_data['gambar'])): ?>
                                <div class="mb-2 p-2 bg-gray-50 border rounded-lg inline-block">
                                    <img src="../<?php echo $edit_data['gambar']; ?>" class="h-12 w-auto object-contain">
                                </div>
                            <?php endif; ?>

                            <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            <p class="text-xs text-gray-400 mt-1">Format: PNG (Transparan) / JPG.</p>
                        </div>
                        
                        <div class="flex gap-2">
                            <?php if($edit_data): ?>
                                <button type="submit" name="update" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-lg transition shadow">
                                    <i class="fa-solid fa-save mr-1"></i> Update
                                </button>
                                <a href="kategori.php" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Batal</a>
                            <?php else: ?>
                                <button type="submit" name="tambah" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 rounded-lg transition shadow-md">
                                    <i class="fa-solid fa-plus mr-1"></i> Simpan
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">Ikon</th>
                                <th class="px-6 py-3">Nama Kategori</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php while($row = mysqli_fetch_assoc($data_kategori)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <?php if(!empty($row['gambar'])): ?>
                                        <img src="../<?php echo $row['gambar']; ?>" class="w-10 h-10 object-contain p-1 border rounded bg-gray-50">
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                    <div class="text-xs text-gray-400 italic">Slug: <?php echo htmlspecialchars($row['slug']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="kategori.php?edit=<?php echo $row['id']; ?>" class="text-yellow-600 hover:text-yellow-800 bg-yellow-50 px-3 py-1 rounded-lg text-xs font-medium border border-yellow-100 mr-2">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                    <a href="kategori.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus kategori ini?')" class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-lg text-xs font-medium border border-red-100">
                                        <i class="fa-solid fa-trash mr-1"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>