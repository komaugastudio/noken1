<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

// Cek Admin
if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// Fitur Bersihkan Log (Hapus Semua)
if (isset($_POST['clear_logs'])) {
    mysqli_query($conn, "TRUNCATE TABLE logs");
    // Catat bahwa log baru saja dibersihkan
    catat_log($conn, "Clear Logs", "Admin membersihkan seluruh riwayat aktivitas.");
    echo "<script>alert('Riwayat berhasil dibersihkan.'); window.location='logs.php';</script>";
}

// Ambil Data Logs
$query = "SELECT * FROM logs ORDER BY id DESC LIMIT 100"; // Batasi 100 terakhir
$logs = mysqli_query($conn, $query);
$total = mysqli_num_rows($logs);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aktivitas - Admin NokenMART</title>
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
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="backup.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-database mr-3"></i> Backup Data</a>
            <!-- Menu Aktif -->
            <a href="logs.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-clock-rotate-left mr-3"></i> Riwayat Aktivitas</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Riwayat Aktivitas Sistem</h2>
            <form action="" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA riwayat?')">
                <button type="submit" name="clear_logs" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow text-sm">
                    <i class="fa-solid fa-trash-can mr-2"></i> Bersihkan Log
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Waktu</th>
                            <th class="px-6 py-3">Pengguna</th>
                            <th class="px-6 py-3">Aksi</th>
                            <th class="px-6 py-3">Detail</th>
                            <th class="px-6 py-3">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if($total > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($logs)): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-gray-500 whitespace-nowrap">
                                    <?php echo date('d M Y H:i:s', strtotime($row['tanggal'])); ?>
                                </td>
                                <td class="px-6 py-3 font-medium text-gray-800">
                                    <?php echo htmlspecialchars($row['user_email']); ?>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs font-bold">
                                        <?php echo htmlspecialchars($row['aksi']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    <?php echo htmlspecialchars($row['detail']); ?>
                                </td>
                                <td class="px-6 py-3 text-gray-400 text-xs font-mono">
                                    <?php echo htmlspecialchars($row['ip_address']); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada aktivitas tercatat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                Menampilkan 100 aktivitas terakhir.
            </div>
        </div>
    </main>
</div>
</body>
</html>