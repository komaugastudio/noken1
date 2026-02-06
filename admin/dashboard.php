<?php
// 1. KEAMANAN: Pastikan Guard Admin dimuat paling atas
include '../config/admin_guard.php';
include '../config/koneksi.php';

// 2. LOGIKA HAPUS (Jika ada permintaan dari tabel preview)
if (isset($_GET['hapus_produk'])) {
    $id = (int)$_GET['hapus_produk'];
    $q_info = mysqli_query($conn, "SELECT judul FROM produk WHERE id=$id");
    $d_info = mysqli_fetch_assoc($q_info);
    $judul_produk = $d_info['judul'];

    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    
    if(function_exists('catat_log')) {
        catat_log($conn, "Hapus Produk", "Admin menghapus produk: $judul_produk (ID: $id)");
    }
    header("Location: dashboard.php?status=deleted");
    exit;
}

// 3. AMBIL DATA STATISTIK UNTUK KARTU
$total_produk   = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM produk"));
$total_user     = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users"));
$total_laporan  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM laporan"));
$total_pesan    = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pesan"));
$total_subs     = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM subscribers"));

// --- STATISTIK PENGUNJUNG ---
$total_visit_today = 0;
$visit_labels = [];
$visit_data = [];

$cek_tabel_visit = mysqli_query($conn, "SHOW TABLES LIKE 'pengunjung'");
if ($cek_tabel_visit && mysqli_num_rows($cek_tabel_visit) > 0) {
    // Pengunjung Hari Ini
    $tgl_ini = date('Y-m-d');
    $q_today = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengunjung WHERE tanggal = '$tgl_ini'");
    $d_today = mysqli_fetch_assoc($q_today);
    $total_visit_today = $d_today['total'];

    // Data Grafik 7 Hari Terakhir
    $q_chart = mysqli_query($conn, "SELECT tanggal, COUNT(*) as total FROM pengunjung GROUP BY tanggal ORDER BY tanggal DESC LIMIT 7");
    $temp_labels = [];
    $temp_data = [];
    while($v = mysqli_fetch_assoc($q_chart)) {
        $temp_labels[] = date('d M', strtotime($v['tanggal']));
        $temp_data[] = $v['total'];
    }
    $visit_labels = array_reverse($temp_labels);
    $visit_data = array_reverse($temp_data);
}

// --- DATA GRAFIK KATEGORI ---
$q_kat = mysqli_query($conn, "SELECT kategori, COUNT(*) as jumlah FROM produk GROUP BY kategori");
$label_kategori = [];
$data_kategori = [];
while ($k = mysqli_fetch_assoc($q_kat)) {
    $label_kategori[] = ucfirst($k['kategori']);
    $data_kategori[] = $k['jumlah'];
}

// 4. DATA UNTUK TABEL PREVIEW
$recent_products = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC LIMIT 5");
$recent_reports = mysqli_query($conn, "SELECT laporan.*, produk.judul FROM laporan LEFT JOIN produk ON laporan.produk_id = produk.id ORDER BY laporan.id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NokenMART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-gray-900 text-white hidden md:block flex-shrink-0 overflow-y-auto">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-teal-400">Noken<span class="text-yellow-500">ADMIN</span></h1>
                <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest">Control Panel</p>
            </div>
            <nav class="mt-4 pb-10">
                <a href="dashboard.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium">
                    <i class="fa-solid fa-gauge mr-3"></i> Dashboard
                </a>
                <a href="produk.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-box mr-3"></i> Kelola Produk
                </a>
                <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-users mr-3"></i> Pengguna
                </a>
                <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition flex justify-between items-center">
                    <span><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</span>
                    <?php if($total_laporan > 0): ?><span class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-full"><?php echo $total_laporan; ?></span><?php endif; ?>
                </a>
                <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition flex justify-between items-center">
                    <span><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</span>
                    <?php if($total_pesan > 0): ?><span class="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full"><?php echo $total_pesan; ?></span><?php endif; ?>
                </a>
                <a href="subscribers.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-rss mr-3"></i> Subscriber
                </a>
                <div class="mt-4 px-6 text-[10px] text-gray-500 uppercase font-bold">Pengaturan Web</div>
                <a href="banner.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-image mr-3"></i> Banner & Promo
                </a>
                <a href="kategori.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-tags mr-3"></i> Kategori Produk
                </a>
                <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    <i class="fa-solid fa-gear mr-3"></i> Konfigurasi Web
                </a>
                
                <div class="mt-8 border-t border-gray-800 pt-4 px-6">
                    <a href="../index.php" class="block py-2 text-gray-500 hover:text-white transition text-sm">
                        <i class="fa-solid fa-globe mr-3"></i> Lihat Website
                    </a>
                    <a href="../auth.php?logout=true" class="block py-2 text-red-400 hover:text-red-300 transition text-sm">
                        <i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-8">
            <!-- Top Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Dashboard Overview</h2>
                    <p class="text-gray-500">Selamat datang kembali, <strong><?php echo $_SESSION['nama']; ?></strong>.</p>
                </div>
                <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-gray-800">Admin Mode</p>
                        <p class="text-[10px] text-green-500">System Online</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0F766E&color=fff" class="w-10 h-10 rounded-full border border-gray-200">
                </div>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <!-- Visitor Today -->
                <div class="bg-gradient-to-br from-teal-600 to-teal-700 p-6 rounded-2xl shadow-lg text-white">
                    <p class="text-teal-100 text-xs font-bold uppercase mb-1">Pengunjung Hari Ini</p>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl font-bold"><?php echo number_format($total_visit_today); ?></h3>
                        <i class="fa-solid fa-chart-line text-teal-300 text-2xl opacity-50"></i>
                    </div>
                </div>
                <!-- Total Produk -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-xs font-bold uppercase mb-1">Total Produk</p>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo number_format($total_produk); ?></h3>
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg"><i class="fa-solid fa-box"></i></div>
                    </div>
                </div>
                <!-- Total Users -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-xs font-bold uppercase mb-1">Pengguna</p>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo number_format($total_user); ?></h3>
                        <div class="p-2 bg-yellow-50 text-yellow-600 rounded-lg"><i class="fa-solid fa-users"></i></div>
                    </div>
                </div>
                <!-- Laporan -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-xs font-bold uppercase mb-1">Laporan</p>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl font-bold <?php echo $total_laporan > 0 ? 'text-red-600' : 'text-gray-800'; ?>">
                            <?php echo $total_laporan; ?>
                        </h3>
                        <div class="p-2 bg-red-50 text-red-600 rounded-lg"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                </div>
                <!-- Subscribers -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-gray-400 text-xs font-bold uppercase mb-1">Subscribers</p>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $total_subs; ?></h3>
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg"><i class="fa-solid fa-rss"></i></div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Line Chart: Visitor Trend -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="font-bold text-gray-700">Trafik Kunjungan (7 Hari Terakhir)</h4>
                        <span class="text-[10px] bg-teal-100 text-teal-700 px-2 py-1 rounded-full font-bold">LIVE DATA</span>
                    </div>
                    <div class="h-64">
                        <canvas id="chartVisitors"></canvas>
                    </div>
                </div>

                <!-- Bar Chart: Categories -->
                <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-gray-700 mb-6">Produk per Kategori</h4>
                    <div class="h-64">
                        <canvas id="chartCategories"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Products Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h4 class="font-bold text-gray-700">Produk Terbaru</h4>
                        <a href="produk.php" class="text-xs text-teal-600 font-bold hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-bold">
                                <tr>
                                    <th class="px-5 py-3">Barang</th>
                                    <th class="px-5 py-3">Harga</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php while($p = mysqli_fetch_assoc($recent_products)): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-800 line-clamp-1"><?php echo htmlspecialchars($p['judul']); ?></div>
                                        <div class="text-[10px] text-gray-400"><?php echo ucfirst($p['kategori']); ?></div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">Rp <?php echo number_format($p['harga'], 0, ',', '.'); ?></td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="dashboard.php?hapus_produk=<?php echo $p['id']; ?>" onclick="return confirm('Hapus iklan ini?')" class="text-red-400 hover:text-red-600 transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Reports Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-red-50 flex justify-between items-center bg-red-50/30">
                        <h4 class="font-bold text-red-800">Laporan Masuk</h4>
                        <a href="laporan.php" class="text-xs text-red-600 font-bold hover:underline">Kelola Laporan</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-red-50/50 text-red-400 uppercase text-[10px] font-bold">
                                <tr>
                                    <th class="px-5 py-3">Detail</th>
                                    <th class="px-5 py-3">Pelapor</th>
                                    <th class="px-5 py-3 text-right">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if(mysqli_num_rows($recent_reports) > 0): ?>
                                    <?php while($r = mysqli_fetch_assoc($recent_reports)): ?>
                                    <tr class="hover:bg-red-50/30 transition">
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-red-700"><?php echo htmlspecialchars(explode(':', $r['alasan'])[0]); ?></div>
                                            <div class="text-[10px] text-gray-500 italic">Barang: <?php echo $r['judul'] ? htmlspecialchars($r['judul']) : 'Sudah Dihapus'; ?></div>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-gray-500"><?php echo htmlspecialchars($r['pelapor_email']); ?></td>
                                        <td class="px-5 py-4 text-right text-[10px] text-gray-400"><?php echo date('d/m H:i', strtotime($r['tanggal'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="px-5 py-10 text-center text-gray-400 italic">Tidak ada laporan. Website aman!</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Charts Initialization -->
    <script>
        // 1. Grafik Pengunjung (Line Chart)
        const visitCtx = document.getElementById('chartVisitors').getContext('2d');
        new Chart(visitCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($visit_labels); ?>,
                datasets: [{
                    label: 'Jumlah Pengunjung',
                    data: <?php echo json_encode($visit_data); ?>,
                    borderColor: '#0F766E',
                    backgroundColor: 'rgba(15, 118, 110, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0F766E',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Grafik Kategori (Bar Chart)
        const catCtx = document.getElementById('chartCategories').getContext('2d');
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($label_kategori); ?>,
                datasets: [{
                    data: <?php echo json_encode($data_kategori); ?>,
                    backgroundColor: '#F59E0B',
                    borderRadius: 8,
                    barThickness: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>