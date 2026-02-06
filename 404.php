<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - NokenMART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { nabire: { primary: '#0F766E', secondary: '#F59E0B' } }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen font-sans">

    <div class="text-center px-4">
        <h1 class="text-9xl font-bold text-nabire-primary opacity-20">404</h1>
        <h2 class="text-3xl font-bold text-gray-800 -mt-8 mb-4">Aduh, Halaman Hilang!</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Halaman yang Anda cari mungkin sudah dihapus, namanya diganti, atau memang tidak pernah ada.
        </p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="index.php" class="px-6 py-3 bg-nabire-primary text-white font-bold rounded-lg shadow-lg hover:bg-teal-800 transition">
                Kembali ke Beranda
            </a>
            <a href="bantuan.php" class="px-6 py-3 bg-white text-gray-700 font-bold rounded-lg shadow border border-gray-200 hover:bg-gray-50 transition">
                Pusat Bantuan
            </a>
        </div>
    </div>

</body>
</html>