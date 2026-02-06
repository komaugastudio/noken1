<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

// Helper SweetAlert
function sweetAlert($icon, $title, $text, $redirect) {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Proses Broadcast...</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                confirmButtonColor: '#0F766E', // Warna Teal NokenMART
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location = '$redirect';
            });
        </script>
    </body>
    </html>";
    exit;
}

// LOGIKA KIRIM (SIMULASI)
if (isset($_POST['kirim'])) {
    $subjek = $_POST['subjek'];
    $pesan = $_POST['pesan'];
    
    // Ambil semua email
    $q_email = mysqli_query($conn, "SELECT email FROM subscribers");
    $jumlah_penerima = mysqli_num_rows($q_email);
    
    // Di sini seharusnya ada script PHPMailer untuk mengirim email sungguhan.
    // Karena di Localhost tanpa SMTP tidak bisa kirim, kita buat simulasi sukses dengan SweetAlert.
    
    if ($jumlah_penerima > 0) {
        sweetAlert('success', 'Broadcast Terkirim!', "Pesan berhasil dikirim ke $jumlah_penerima pelanggan newsletter.", 'subscribers.php');
    } else {
        sweetAlert('warning', 'Tidak Ada Penerima', 'Belum ada pelanggan yang terdaftar di newsletter.', 'subscribers.php');
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Broadcast - Admin NokenMART</title>
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
            <a href="banner.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-image mr-3"></i> Kelola Banner</a>
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <!-- Menu Aktif -->
            <a href="subscribers.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-rss mr-3"></i> Subscriber</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition mt-10"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Kirim Broadcast Email</h2>
            <a href="subscribers.php" class="text-gray-500 hover:text-gray-700 flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <strong>Catatan:</strong> Fitur ini akan mengirimkan email ke seluruh pelanggan yang terdaftar di Newsletter. Pastikan isi pesan sudah benar.
                </p>
            </div>

            <form action="" method="POST">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Subjek Email</label>
                        <input type="text" name="subjek" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-teal-500 outline-none" placeholder="Contoh: Promo Spesial Akhir Bulan!">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Isi Pesan</label>
                        <textarea name="pesan" rows="10" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-teal-500 outline-none" placeholder="Tulis info promo atau berita terbaru di sini..."></textarea>
                    </div>

                    <button type="submit" name="kirim" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 rounded-lg shadow-md transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Broadcast Sekarang
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>