<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$email = $_SESSION['user'];

// LOGIKA TANDAI SUDAH DIBACA (Saat diklik)
if (isset($_GET['baca'])) {
    $id_notif = (int)$_GET['baca'];
    $link = $_GET['link']; // Ambil link tujuan
    
    // Update status jadi sudah dibaca
    mysqli_query($conn, "UPDATE notifikasi SET status='sudah_dibaca' WHERE id=$id_notif AND user_email='$email'");
    
    // Redirect ke halaman tujuan (misal: detail produk)
    header("Location: " . $link);
    exit;
}

// LOGIKA TANDAI SEMUA DIBACA
if (isset($_GET['baca_semua'])) {
    mysqli_query($conn, "UPDATE notifikasi SET status='sudah_dibaca' WHERE user_email='$email'");
    echo "<script>window.location='notifikasi.php';</script>";
}

// AMBIL NOTIFIKASI
$query = "SELECT * FROM notifikasi WHERE user_email = '$email' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$count = mysqli_num_rows($result);

include 'templates/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-8 min-h-screen">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi Anda</h1>
        <?php if($count > 0): ?>
            <a href="notifikasi.php?baca_semua=true" class="text-sm text-nabire-primary hover:underline">Tandai semua sudah dibaca</a>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if ($count > 0): ?>
            <ul class="divide-y divide-gray-100">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <li class="hover:bg-gray-50 transition <?php echo ($row['status'] == 'belum_dibaca') ? 'bg-teal-50' : ''; ?>">
                    <a href="notifikasi.php?baca=<?php echo $row['id']; ?>&link=<?php echo urlencode($row['link']); ?>" class="block p-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-1">
                                <?php if($row['status'] == 'belum_dibaca'): ?>
                                    <div class="w-2.5 h-2.5 bg-red-500 rounded-full"></div>
                                <?php else: ?>
                                    <div class="w-2.5 h-2.5 bg-gray-300 rounded-full"></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800 text-sm <?php echo ($row['status'] == 'belum_dibaca') ? 'font-semibold' : ''; ?>">
                                    <?php echo htmlspecialchars($row['pesan']); ?>
                                </p>
                                <span class="text-xs text-gray-500 mt-1 block">
                                    <?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?>
                                </span>
                            </div>
                            <div>
                                <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fa-regular fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Belum ada notifikasi baru.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>