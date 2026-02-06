<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// TAMBAH REKENING
if (isset($_POST['tambah'])) {
    $bank = mysqli_real_escape_string($conn, $_POST['nama_bank']);
    $norek = mysqli_real_escape_string($conn, $_POST['nomor_rekening']);
    $an = mysqli_real_escape_string($conn, $_POST['atas_nama']);
    
    // Upload Logo (Opsional)
    $logo = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "../assets/img/bank/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
        $name_file = "bank_" . uniqid() . "." . $ext;
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_dir . $name_file)) {
            $logo = "assets/img/bank/" . $name_file;
        }
    }

    $query = "INSERT INTO rekening (nama_bank, nomor_rekening, atas_nama, logo_bank) VALUES ('$bank', '$norek', '$an', '$logo')";
    mysqli_query($conn, $query);
    echo "<script>alert('Rekening berhasil ditambah!'); window.location='rekening.php';</script>";
}

// HAPUS REKENING
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM rekening WHERE id=$id");
    echo "<script>alert('Rekening dihapus.'); window.location='rekening.php';</script>";
}

$rekening = mysqli_query($conn, "SELECT * FROM rekening ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Rekening - Admin NokenMART</title>
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
            <a href="rekening.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-wallet mr-3"></i> Kelola Rekening</a>
            
            <a href="wilayah.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-map-location-dot mr-3"></i> Kelola Wilayah</a>
            <a href="banner.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-image mr-3"></i> Kelola Banner</a>
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manajemen Rekening Bank</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Tambah -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-700 mb-4">Tambah Rekening Baru</h3>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-sm text-gray-600 mb-1">Nama Bank</label>
                            <input type="text" name="nama_bank" required placeholder="Contoh: Bank Papua" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-600 mb-1">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" required placeholder="Contoh: 123-456-789" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-600 mb-1">Atas Nama</label>
                            <input type="text" name="atas_nama" required placeholder="Contoh: NokenMART Official" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-600 mb-1">Logo Bank (Opsional)</label>
                            <input type="file" name="logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        </div>
                        <button type="submit" name="tambah" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 rounded-lg transition">
                            <i class="fa-solid fa-plus mr-1"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">Bank</th>
                                <th class="px-6 py-3">Info Rekening</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php while($row = mysqli_fetch_assoc($rekening)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <?php if(!empty($row['logo_bank'])): ?>
                                            <img src="../<?php echo $row['logo_bank']; ?>" class="w-10 h-10 object-contain p-1 border rounded bg-white">
                                        <?php else: ?>
                                            <div class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center text-gray-400"><i class="fa-solid fa-building-columns"></i></div>
                                        <?php endif; ?>
                                        <span class="font-bold text-gray-800"><?php echo htmlspecialchars($row['nama_bank']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-lg font-mono text-gray-900"><?php echo htmlspecialchars($row['nomor_rekening']); ?></div>
                                    <div class="text-xs text-gray-500">A.N. <?php echo htmlspecialchars($row['atas_nama']); ?></div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="rekening.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus rekening ini?')" class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-lg text-xs font-medium border border-red-100">
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