<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['user']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin')) {
    // Jika bukan admin, tendang ke home (Logic sederhana, di real app cek role database)
    // Untuk saat ini kita pakai session user saja sebagai validasi dasar
    if (!isset($_SESSION['user'])) {
        header("Location: ../index.php");
        exit;
    }
}

// Hapus Pesan
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pesan WHERE id=$id");
    echo "<script>alert('Pesan dihapus'); window.location='pesan.php';</script>";
}

// Ambil Data Pesan
$pesan = mysqli_query($conn, "SELECT * FROM pesan ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk - Admin NokenMART</title>
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
                <p class="text-xs text-gray-400 mt-1">Panel Kontrol Utama</p>
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-gauge mr-3"></i> Dashboard
                </a>
                <a href="pesan.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium">
                    <i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk
                </a>
                <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-globe mr-3"></i> Lihat Website
                </a>
                <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10">
                    <i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-800">Pesan Masuk</h2>
                <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-sm font-semibold">
                    <?php echo mysqli_num_rows($pesan); ?> Pesan
                </span>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">Pengirim</th>
                                <th class="px-6 py-3">Subjek</th>
                                <th class="px-6 py-3">Pesan</th>
                                <th class="px-6 py-3">Waktu</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php while($row = mysqli_fetch_assoc($pesan)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($row['email']); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 text-gray-700 py-1 px-2 rounded text-xs font-medium">
                                        <?php echo htmlspecialchars($row['subjek']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['pesan']); ?>">
                                        <?php echo htmlspecialchars($row['pesan']); ?>
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    <?php echo date('d M Y H:i', strtotime($row['tanggal'])); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="mailto:<?php echo $row['email']; ?>" class="text-blue-500 hover:text-blue-700 mr-3" title="Balas Email">
                                        <i class="fa-solid fa-reply"></i>
                                    </a>
                                    <a href="pesan.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus pesan ini?')" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>