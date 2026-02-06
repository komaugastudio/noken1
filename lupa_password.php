<?php
session_start();
include 'config/koneksi.php';

$step = 1; // Default langkah pertama
$error = "";
$success = "";
$email_verified = "";

// LOGIKA TAHAP 1: VERIFIKASI DATA
if (isset($_POST['cek_data'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);

    // Cek apakah ada user dengan email DAN whatsapp tersebut
    $query = "SELECT * FROM users WHERE email = '$email' AND whatsapp = '$whatsapp'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        // Data cocok, lanjut ke langkah 2
        $step = 2;
        $email_verified = $email; // Simpan email untuk tahap selanjutnya
    } else {
        $error = "Data tidak ditemukan! Pastikan Email dan No WhatsApp sesuai saat mendaftar.";
    }
}

// LOGIKA TAHAP 2: GANTI PASSWORD
if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email_verified']);
    $pass_baru = $_POST['password_baru'];
    
    // Update password langsung (tanpa hash dulu agar mudah)
    $query_update = "UPDATE users SET password = '$pass_baru' WHERE email = '$email'";
    
    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Password berhasil diganti! Silakan login dengan password baru.'); window.location='index.php';</script>";
        exit;
    } else {
        $error = "Gagal mereset password: " . mysqli_error($conn);
    }
}

include 'templates/header.php';
?>

<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-nabire-primary px-6 py-4 border-b border-gray-100 text-center">
            <h1 class="text-xl font-bold text-white">Pemulihan Akun</h1>
            <p class="text-teal-100 text-xs mt-1">Reset password Anda dengan aman</p>
        </div>
        
        <div class="p-8">
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- TAHAP 1: Verifikasi Identitas -->
            <?php if ($step == 1): ?>
            <form action="" method="POST">
                <p class="text-gray-600 text-sm mb-6 text-center">
                    Masukkan <strong>Email</strong> dan <strong>Nomor WhatsApp</strong> yang terdaftar untuk memverifikasi kepemilikan akun.
                </p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Terdaftar</label>
                        <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="email@contoh.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                        <input type="text" name="whatsapp" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="0812xxxx">
                    </div>
                    <button type="submit" name="cek_data" class="w-full bg-nabire-secondary hover:bg-yellow-600 text-white font-bold py-3 rounded-lg shadow-md transition">
                        Verifikasi Akun
                    </button>
                    <div class="text-center mt-4">
                        <a href="index.php" class="text-sm text-gray-500 hover:text-nabire-primary">Kembali ke Beranda</a>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <!-- TAHAP 2: Buat Password Baru -->
            <?php if ($step == 2): ?>
            <form action="" method="POST">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-500 mb-3">
                        <i class="fa-solid fa-check text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Verifikasi Berhasil!</h3>
                    <p class="text-sm text-gray-500">Silakan buat password baru untuk akun <strong><?php echo htmlspecialchars($email_verified); ?></strong></p>
                </div>

                <input type="hidden" name="email_verified" value="<?php echo htmlspecialchars($email_verified); ?>">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password_baru" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-nabire-primary outline-none" placeholder="******">
                    </div>
                    <button type="submit" name="reset_password" class="w-full bg-nabire-primary hover:bg-teal-800 text-white font-bold py-3 rounded-lg shadow-md transition">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>