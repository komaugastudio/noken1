<?php
session_start();
include 'config/koneksi.php';

// Ambil info website
$q_web = mysqli_query($conn, "SELECT nama_website, mode_maintenance FROM pengaturan WHERE id=1");
$web = mysqli_fetch_assoc($q_web);

// Jika mode maintenance MATI, lempar balik ke index
if ($web['mode_maintenance'] == 'tidak') {
    header("Location: index.php");
    exit;
}

// Jika Admin yang akses, langsung ke index
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Perbaikan - <?php echo htmlspecialchars($web['nama_website']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 h-screen flex flex-col items-center justify-center px-4">

    <div class="bg-white p-8 md:p-12 rounded-2xl shadow-xl max-w-lg w-full text-center border-t-8 border-yellow-500">
        <div class="w-24 h-24 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-person-digging text-4xl"></i>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Website Sedang Perbaikan</h1>
        <p class="text-gray-500 mb-8 leading-relaxed">
            Mohon maaf, kami sedang melakukan pemeliharaan sistem untuk meningkatkan layanan. Kami akan segera kembali.
        </p>

        <div class="flex justify-center gap-4 text-sm text-gray-400">
            <span><i class="fa-brands fa-whatsapp"></i> Hubungi Admin</span>
            <span>&bull;</span>
            <span><i class="fa-solid fa-envelope"></i> Kirim Email</span>
        </div>
    </div>

    <!-- Login Admin Darurat (Toggle) -->
    <div class="mt-8 text-center">
        <button onclick="document.getElementById('loginForm').classList.toggle('hidden')" class="text-gray-400 hover:text-gray-600 text-xs underline">
            Login Administrator
        </button>

        <form id="loginForm" action="auth.php" method="POST" class="hidden mt-4 bg-white p-4 rounded-lg shadow-sm max-w-xs mx-auto">
            <input type="email" name="email" required placeholder="Email Admin" class="w-full border p-2 rounded mb-2 text-sm">
            <input type="password" name="password" required placeholder="Password" class="w-full border p-2 rounded mb-2 text-sm">
            <button type="submit" name="login" class="w-full bg-gray-800 text-white py-2 rounded text-sm font-bold">Masuk</button>
        </form>
    </div>

</body>
</html>