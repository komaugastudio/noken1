<?php
session_start();
include 'config/koneksi.php';

// Helper SweetAlert (Sama seperti di auth.php)
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

// Cek Login
if (!isset($_SESSION['user'])) {
    // Jika belum login, redirect ke halaman sebelumnya atau home
    $back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Belum Login',
                text: 'Silakan login terlebih dahulu untuk menyimpan barang favorit!',
                confirmButtonColor: '#0F766E'
            }).then(() => {
                window.location = '$back_url';
            });
        </script>
    </body>
    </html>";
    exit;
}

$user_email = $_SESSION['user'];

// --- LOGIKA TAMBAH KE WISHLIST ---
if (isset($_GET['add'])) {
    $produk_id = (int)$_GET['add'];
    
    // Cek apakah sudah ada di wishlist
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_email = '$user_email' AND produk_id = $produk_id");
    
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO wishlist (user_email, produk_id) VALUES ('$user_email', $produk_id)");
        sweetAlert('success', 'Berhasil Disimpan', 'Barang telah ditambahkan ke Favorit Anda.', 'wishlist.php');
    } else {
        sweetAlert('info', 'Sudah Ada', 'Barang ini sudah ada di daftar Favorit Anda.', 'wishlist.php');
    }
}

// --- LOGIKA HAPUS DARI WISHLIST ---
if (isset($_GET['remove'])) {
    $id_wishlist = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM wishlist WHERE id = $id_wishlist AND user_email = '$user_email'");
    sweetAlert('success', 'Dihapus', 'Barang dihapus dari Favorit.', 'wishlist.php');
}

// --- AMBIL DATA WISHLIST (JOIN dengan tabel Produk) ---
$query = "SELECT wishlist.id as wishlist_id, produk.* FROM wishlist 
          JOIN produk ON wishlist.produk_id = produk.id 
          WHERE wishlist.user_email = '$user_email' 
          ORDER BY wishlist.id DESC";
$result = mysqli_query($conn, $query);
$count = mysqli_num_rows($result);

// Helper
function formatRupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}

include 'templates/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-screen">
    
    <div class="mb-8 flex items-center gap-3">
        <div class="bg-red-100 p-3 rounded-full text-red-500">
            <i class="fa-solid fa-heart text-2xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Favorit Saya</h1>
            <p class="text-gray-500 text-sm">Menampilkan <?php echo $count; ?> barang impian Anda</p>
        </div>
    </div>

    <?php if ($count > 0): ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-300 border border-gray-100 overflow-hidden flex flex-col h-full group relative">
                
                <a href="detail.php?id=<?php echo $row['id']; ?>" class="block relative h-48 bg-gray-200 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($row['gambar']); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                </a>

                <!-- Tombol Hapus (X) dengan SweetAlert Confirm -->
                <button onclick="confirmHapus('<?php echo $row['wishlist_id']; ?>')" class="absolute top-2 right-2 bg-white text-red-500 w-8 h-8 rounded-full flex items-center justify-center shadow-sm hover:bg-red-50 transition z-10" title="Hapus dari Favorit">
                    <i class="fa-solid fa-trash-can"></i>
                </button>

                <div class="p-4 flex flex-col flex-grow">
                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="block">
                        <h3 class="font-semibold text-gray-800 line-clamp-2 mb-1 text-sm md:text-base h-10 group-hover:text-nabire-primary transition"><?php echo htmlspecialchars($row['judul']); ?></h3>
                    </a>
                    
                    <p class="text-nabire-secondary font-bold text-lg mb-2"><?php echo formatRupiah($row['harga']); ?></p>
                    
                    <div class="mt-auto">
                        <a href="https://wa.me/<?php echo $row['whatsapp']; ?>?text=Halo, saya tertarik dengan barang favorit saya '<?php echo urlencode($row['judul']); ?>'..." 
                           target="_blank"
                           class="block w-full text-center bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg font-medium transition text-sm flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i> Hubungi
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
            <i class="fa-regular fa-heart text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Belum ada barang favorit</h3>
            <p class="text-gray-500 mb-6">Jelajahi NokenMART dan simpan barang yang Anda suka!</p>
            <a href="index.php" class="bg-nabire-primary text-white px-6 py-2 rounded-lg hover:bg-teal-800 transition font-medium">
                Mulai Belanja
            </a>
        </div>
    <?php endif; ?>

</div>

<!-- Script SweetAlert untuk Konfirmasi Hapus -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmHapus(id) {
        Swal.fire({
            title: 'Hapus dari Favorit?',
            text: "Barang ini akan dihapus dari daftar keinginan Anda.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'wishlist.php?remove=' + id;
            }
        })
    }
</script>

<?php include 'templates/footer.php'; ?>