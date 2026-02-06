<?php
session_start();
include 'config/koneksi.php';

// 1. CEK LOGIN
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu untuk melaporkan!'); window.location='index.php';</script>";
    exit;
}

// 2. AMBIL ID BARANG
$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query_produk = mysqli_query($conn, "SELECT judul FROM produk WHERE id = $id_produk");
$data_produk = mysqli_fetch_assoc($query_produk);

if (!$data_produk) {
    echo "<script>alert('Barang tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// 3. PROSES SIMPAN LAPORAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_laporan'])) {
    $pelapor = $_SESSION['user'];
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    
    $alasan_lengkap = "$alasan: $deskripsi";

    $query_insert = "INSERT INTO laporan (produk_id, pelapor_email, alasan) VALUES ($id_produk, '$pelapor', '$alasan_lengkap')";
    
    if (mysqli_query($conn, $query_insert)) {
        echo "<script>alert('Laporan berhasil dikirim! Terima kasih telah membantu menjaga keamanan NokenMART.'); window.location='detail.php?id=$id_produk';</script>";
    } else {
        echo "<script>alert('Gagal mengirim laporan.');</script>";
    }
}

include 'templates/header.php';
?>

<div class="max-w-xl mx-auto px-4 py-12">
    <div class="bg-white rounded-xl shadow-lg border border-red-100 overflow-hidden">
        <div class="bg-red-600 px-6 py-4 border-b border-red-700">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Laporkan Barang
            </h1>
        </div>
        
        <div class="p-8">
            <p class="text-gray-600 mb-6">
                Anda melaporkan: <strong><?php echo htmlspecialchars($data_produk['judul']); ?></strong>
            </p>

            <form action="" method="POST">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pelaporan</label>
                        <select name="alasan" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="">Pilih Alasan...</option>
                            <option value="Penipuan">Indikasi Penipuan</option>
                            <option value="Barang Terlarang">Barang Terlarang / Ilegal</option>
                            <option value="Duplikat">Iklan Ganda / Spam</option>
                            <option value="Konten Tidak Pantas">Foto/Kata-kata Tidak Pantas</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                        <textarea name="deskripsi" required rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 outline-none" placeholder="Jelaskan detail masalahnya..."></textarea>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="submit" name="kirim_laporan" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg shadow-md transition">
                            Kirim Laporan
                        </button>
                        <a href="detail.php?id=<?php echo $id_produk; ?>" class="px-6 py-3 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>