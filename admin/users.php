<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

// Cek Sesi Admin
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

// 1. LOGIKA VERIFIKASI (CENTANG BIRU) + NOTIFIKASI OTOMATIS
if (isset($_GET['verifikasi'])) {
    $id = (int)$_GET['verifikasi'];
    $status = (int)$_GET['status']; // 1 = Verifikasi/Terima, 0 = Batal/Tolak
    
    // Ambil data user sebelum update untuk mengirim pesan
    $q_user = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    $d_user = mysqli_fetch_assoc($q_user);

    if ($d_user) {
        // Update database status
        mysqli_query($conn, "UPDATE users SET is_verified=$status WHERE id=$id");

        $nama_user = $d_user['nama'];
        $wa_user = $d_user['whatsapp'];
        $email_user = $d_user['email'];

        // Siapkan Isi Pesan Otomatis
        if ($status == 1) {
            // PESAN DITERIMA
            $pesan_wa = "Halo $nama_user, Selamat! Pendaftaran Akun Penjual Anda di *NokenMART* telah *DISETUJUI/DIVERIFIKASI* oleh Admin. Anda sekarang mendapatkan lencana Terpercaya (Centang Biru) dan fitur penjual aktif. Selamat berjualan!";
            
            $subjek_email = "Selamat! Akun NokenMART Anda Terverifikasi";
            $isi_email = "Halo $nama_user,\n\nKami membawa kabar gembira!\n\nAkun Penjual Anda di NokenMART telah berhasil diverifikasi oleh tim Admin kami.\n\nSekarang profil toko Anda menampilkan lencana Centang Biru yang menandakan toko terpercaya.\n\nSilakan login dan mulai berjualan:\nhttp://localhost/nokenmart/index.php\n\nSalam,\nTim NokenMART";
        } else {
            // PESAN DITOLAK / DICABUT
            $pesan_wa = "Halo $nama_user, Mohon maaf. Verifikasi Akun Penjual NokenMART Anda saat ini *DITOLAK* atau *DICABUT* karena data/dokumen yang tidak sesuai. Silakan periksa kembali profil Anda atau hubungi Admin.";
            
            $subjek_email = "Pemberitahuan Status Akun NokenMART";
            $isi_email = "Halo $nama_user,\n\nMohon maaf, kami harus menginformasikan bahwa status verifikasi akun Penjual Anda saat ini DITOLAK atau DICABUT.\n\nHal ini mungkin disebabkan oleh:\n1. Foto identitas tidak jelas.\n2. Data tidak sesuai.\n3. Pelanggaran ketentuan komunitas.\n\nSilakan hubungi admin untuk informasi lebih lanjut.\n\nSalam,\nTim NokenMART";
        }

        // Encode pesan untuk URL
        $link_wa = "https://wa.me/$wa_user?text=" . urlencode($pesan_wa);
        $link_email = "mailto:$email_user?subject=" . urlencode($subjek_email) . "&body=" . urlencode($isi_email);

        // Redirect kembali dengan parameter notifikasi agar JS bisa mengeksekusi popup
        echo "<script>window.location='users.php?notify=1&wa_link=" . urlencode($link_wa) . "&email_link=" . urlencode($link_email) . "&status_msg=" . ($status==1 ? 'disetujui' : 'ditolak') . "';</script>";
        exit;
    }
}

// 2. LOGIKA HAPUS USER
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id == 1) { // Proteksi Super Admin (ID 1)
        echo "<script>alert('Anda tidak bisa menghapus Super Admin!'); window.location='users.php';</script>";
        exit;
    }
    
    // Hapus file fisik (KTP/Selfie) jika ada
    $q_file = mysqli_query($conn, "SELECT foto_ktp, foto_selfie, foto_profil FROM users WHERE id=$id");
    $f = mysqli_fetch_assoc($q_file);
    
    if (!empty($f['foto_ktp']) && file_exists("../" . $f['foto_ktp'])) unlink("../" . $f['foto_ktp']);
    if (!empty($f['foto_selfie']) && file_exists("../" . $f['foto_selfie'])) unlink("../" . $f['foto_selfie']);
    if (!empty($f['foto_profil']) && file_exists("../" . $f['foto_profil'])) unlink("../" . $f['foto_profil']);

    // Hapus data di database
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    echo "<script>alert('Pengguna berhasil dihapus.'); window.location='users.php';</script>";
}

// Pencarian
$keyword = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = "WHERE 1=1";
if (!empty($keyword)) {
    $where .= " AND (nama LIKE '%$keyword%' OR email LIKE '%$keyword%' OR whatsapp LIKE '%$keyword%')";
}

$users = mysqli_query($conn, "SELECT * FROM users $where ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin NokenMART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <!-- Menu Aktif -->
            <a href="users.php" class="block py-3 px-6 bg-gray-800 border-l-4 border-teal-500 text-teal-400 font-medium"><i class="fa-solid fa-users mr-3"></i> Kelola Pengguna</a>
            <a href="laporan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-triangle-exclamation mr-3"></i> Laporan</a>
            <a href="pesan.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-regular fa-envelope mr-3"></i> Pesan Masuk</a>
            <a href="cetak.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-print mr-3"></i> Cetak Data</a>
            <a href="pengaturan_website.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-gear mr-3"></i> Pengaturan Web</a>
            <a href="blog.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition"><i class="fa-solid fa-newspaper mr-3"></i> Kelola Blog</a>
            
            <a href="../index.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8 border-t border-gray-700 pt-4"><i class="fa-solid fa-globe mr-3"></i> Lihat Website</a>
            <a href="../auth.php?logout=true" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition"><i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar</a>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h2 class="text-3xl font-bold text-gray-800">Daftar Pengguna</h2>
            <form action="" method="GET" class="relative">
                <input type="text" name="cari" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari nama, email, WA..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none w-64">
                <i class="fa-solid fa-search absolute left-3 top-3 text-gray-400"></i>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">User Info</th>
                            <th class="px-6 py-3">Kontak</th>
                            <th class="px-6 py-3">Role & Dokumen</th>
                            <th class="px-6 py-3 text-right">Verifikasi & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php while($row = mysqli_fetch_assoc($users)): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                <div class="flex items-center gap-3">
                                    <!-- Tampilkan Foto Profil jika ada -->
                                    <?php 
                                        $foto = !empty($row['foto_profil']) ? '../'.$row['foto_profil'] : "https://ui-avatars.com/api/?name=".urlencode($row['nama'])."&background=random&color=fff";
                                    ?>
                                    <img src="<?php echo $foto; ?>" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <?php echo htmlspecialchars($row['nama']); ?>
                                        <?php if($row['is_verified']): ?>
                                            <i class="fa-solid fa-circle-check text-blue-500 ml-1" title="Terverifikasi"></i>
                                        <?php endif; ?>
                                        <div class="text-xs text-gray-500 font-normal">ID: <?php echo $row['id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="text-xs flex items-center gap-1"><i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                                <div class="text-green-600 font-medium text-xs flex items-center gap-1 mt-1"><i class="fa-brands fa-whatsapp"></i> <?php echo htmlspecialchars($row['whatsapp']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-semibold <?php 
                                    if($row['role'] == 'admin') echo 'bg-purple-100 text-purple-600';
                                    elseif($row['role'] == 'penjual') echo 'bg-yellow-100 text-yellow-700';
                                    else echo 'bg-blue-100 text-blue-600';
                                ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                                
                                <!-- Tombol Lihat Dokumen (Hanya untuk Penjual) -->
                                <?php if($row['role'] == 'penjual'): ?>
                                    <div class="mt-2">
                                        <?php if(!empty($row['foto_ktp']) || !empty($row['foto_selfie'])): ?>
                                            <button onclick="lihatDokumen('<?php echo htmlspecialchars($row['nama']); ?>', '<?php echo !empty($row['foto_ktp']) ? '../'.$row['foto_ktp'] : ''; ?>', '<?php echo !empty($row['foto_selfie']) ? '../'.$row['foto_selfie'] : ''; ?>')" 
                                                    class="text-xs text-teal-600 hover:text-teal-800 underline flex items-center gap-1">
                                                <i class="fa-solid fa-id-card"></i> Lihat Berkas
                                            </button>
                                        <?php else: ?>
                                            <span class="text-xs text-red-400 italic">Berkas Kosong</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <?php if($row['role'] != 'admin'): ?>
                                    <!-- Tombol Verifikasi -->
                                    <?php if($row['is_verified']): ?>
                                        <a href="users.php?verifikasi=<?php echo $row['id']; ?>&status=0" onclick="return confirm('Cabut verifikasi penjual ini?')" class="text-gray-500 hover:text-gray-700 bg-gray-100 px-3 py-1 rounded-lg border border-gray-200 mr-2 text-xs" title="Batalkan Verifikasi">
                                            <i class="fa-solid fa-xmark"></i> Tolak/Batal
                                        </a>
                                    <?php else: ?>
                                        <a href="users.php?verifikasi=<?php echo $row['id']; ?>&status=1" onclick="return confirm('Verifikasi penjual ini? Pastikan dokumen sudah valid.')" class="text-white hover:bg-blue-600 bg-blue-500 px-3 py-1 rounded-lg border border-blue-500 mr-2 text-xs" title="Verifikasi Penjual">
                                            <i class="fa-solid fa-check"></i> Verifikasi
                                        </a>
                                    <?php endif; ?>

                                    <a href="users.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus user ini? Tindakan ini tidak bisa dibatalkan.')" class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded-lg border border-red-100 text-xs">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs italic">Super Admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- MODAL LIHAT DOKUMEN -->
<div id="modalDokumen" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm" onclick="tutupModal()">
    <div class="bg-white rounded-xl max-w-4xl w-full overflow-hidden shadow-2xl transform transition-all scale-95" onclick="event.stopPropagation()">
        <div class="bg-gray-900 px-6 py-4 flex justify-between items-center">
            <h3 class="text-white font-bold text-lg" id="modalTitle">Dokumen Verifikasi</h3>
            <button onclick="tutupModal()" class="text-gray-400 hover:text-white transition"><i class="fa-solid fa-xmark text-2xl"></i></button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[80vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- KTP -->
                <div class="text-center">
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Foto KTP / Identitas</h4>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-2 bg-gray-50 min-h-[200px] flex items-center justify-center">
                        <img id="imgKTP" src="" alt="KTP Tidak Ditemukan" class="max-w-full max-h-[400px] rounded shadow-sm object-contain">
                        <p id="msgKTP" class="hidden text-gray-400 italic">Tidak ada foto KTP</p>
                    </div>
                </div>
                <!-- Selfie -->
                <div class="text-center">
                    <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Foto Swafoto (Selfie)</h4>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-2 bg-gray-50 min-h-[200px] flex items-center justify-center">
                        <img id="imgSelfie" src="" alt="Selfie Tidak Ditemukan" class="max-w-full max-h-[400px] rounded shadow-sm object-contain">
                        <p id="msgSelfie" class="hidden text-gray-400 italic">Tidak ada foto Selfie</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 text-center bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <p class="text-sm text-yellow-800"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Periksa kesesuaian wajah di KTP dengan foto Selfie sebelum melakukan verifikasi.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // POPUP OTOMATIS KIRIM NOTIFIKASI (JIKA ADA PARAMETER NOTIFY)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('notify')) {
        const waLink = decodeURIComponent(urlParams.get('wa_link'));
        const emailLink = decodeURIComponent(urlParams.get('email_link'));
        const statusMsg = urlParams.get('status_msg');
        const icon = statusMsg === 'disetujui' ? 'success' : 'warning';

        Swal.fire({
            title: 'Tindakan Berhasil',
            text: `Akun penjual telah ${statusMsg}. Segera beri tahu penjual melalui:`,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#25D366', // Warna WA
            cancelButtonColor: '#0F766E', // Warna Email (Teal)
            confirmButtonText: '<i class="fa-brands fa-whatsapp"></i> Kirim WhatsApp',
            cancelButtonText: '<i class="fa-regular fa-envelope"></i> Kirim Email',
            footer: '<a href="users.php" class="text-gray-400 text-sm">Tutup tanpa mengirim</a>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(waLink, '_blank');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                window.open(emailLink, '_blank');
            }
            // Bersihkan URL
            window.history.replaceState({}, document.title, "users.php");
        });
    }

    function lihatDokumen(nama, ktpPath, selfiePath) {
        document.getElementById('modalTitle').innerText = 'Dokumen Verifikasi: ' + nama;
        
        const imgKTP = document.getElementById('imgKTP');
        const msgKTP = document.getElementById('msgKTP');
        const imgSelfie = document.getElementById('imgSelfie');
        const msgSelfie = document.getElementById('msgSelfie');
        
        // Cek KTP
        if (ktpPath) {
            imgKTP.src = ktpPath;
            imgKTP.classList.remove('hidden');
            msgKTP.classList.add('hidden');
        } else {
            imgKTP.classList.add('hidden');
            msgKTP.classList.remove('hidden');
        }

        // Cek Selfie
        if (selfiePath) {
            imgSelfie.src = selfiePath;
            imgSelfie.classList.remove('hidden');
            msgSelfie.classList.add('hidden');
        } else {
            imgSelfie.classList.add('hidden');
            msgSelfie.classList.remove('hidden');
        }

        const modal = document.getElementById('modalDokumen');
        modal.classList.remove('hidden');
        // Efek animasi simpel
        setTimeout(() => {
            modal.firstElementChild.classList.remove('scale-95');
            modal.firstElementChild.classList.add('scale-100');
        }, 10);
    }

    function tutupModal() {
        const modal = document.getElementById('modalDokumen');
        modal.firstElementChild.classList.remove('scale-100');
        modal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
</script>

</body>
</html>