<?php
session_start();
include 'config/koneksi.php';

// Jika user sudah login, ambil datanya untuk auto-fill form
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : '';
$email_user = isset($_SESSION['user']) ? $_SESSION['user'] : '';

// PROSES KIRIM PESAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_pesan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subjek = mysqli_real_escape_string($conn, $_POST['subjek']);
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

    $query = "INSERT INTO pesan (nama, email, subjek, pesan) VALUES ('$nama', '$email', '$subjek', '$pesan')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pesan berhasil dikirim! Tim kami akan segera menghubungi Anda.'); window.location='kontak.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim pesan.');</script>";
    }
}

include 'templates/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Hubungi Kami</h1>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Punya pertanyaan, saran, atau ingin bekerjasama? Jangan ragu untuk mengirimkan pesan kepada kami.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Informasi Kontak -->
        <div class="space-y-8">
            <div class="bg-teal-50 p-6 rounded-xl border border-teal-100">
                <h3 class="text-xl font-bold text-nabire-primary mb-4">Kontak Langsung</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="bg-white p-3 rounded-full shadow-sm text-nabire-primary">
                            <i class="fa-solid fa-location-dot text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Alamat Kantor</h4>
                            <p class="text-gray-600 text-sm">Jl. Merdeka No. 45, Oyehe, Nabire, Papua Tengah.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-white p-3 rounded-full shadow-sm text-green-500">
                            <i class="fa-brands fa-whatsapp text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">WhatsApp Admin</h4>
                            <p class="text-gray-600 text-sm">+62 812-3456-7890</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-white p-3 rounded-full shadow-sm text-blue-500">
                            <i class="fa-regular fa-envelope text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Email</h4>
                            <p class="text-gray-600 text-sm">bantuan@nokenmart.com</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-2">Jam Operasional</h3>
                <p class="text-gray-600 text-sm mb-1">Senin - Jumat: 08:00 - 17:00 WIT</p>
                <p class="text-gray-600 text-sm">Sabtu: 08:00 - 14:00 WIT</p>
            </div>
        </div>

        <!-- Formulir Pesan -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Kirim Pesan</h3>
            <form action="" method="POST">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?php echo htmlspecialchars($nama_user); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email_user); ?>" required 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subjek / Perihal</label>
                        <select name="subjek" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none">
                            <option value="Pertanyaan Umum">Pertanyaan Umum</option>
                            <option value="Kendala Teknis">Kendala Teknis</option>
                            <option value="Laporan Penipuan">Laporan Penipuan</option>
                            <option value="Kerjasama">Kerjasama / Kemitraan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Isi Pesan</label>
                        <textarea name="pesan" rows="4" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="Tulis pesan Anda di sini..."></textarea>
                    </div>
                    <button type="submit" name="kirim_pesan" class="w-full bg-nabire-primary text-white font-bold py-3 rounded-lg shadow-md hover:bg-teal-800 transition">
                        Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>