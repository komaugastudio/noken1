<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anda Sedang Offline - NokenMART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen px-4">
    <div class="text-center bg-white p-8 rounded-2xl shadow-lg max-w-md w-full border border-gray-100">
        <div class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-wifi text-4xl text-gray-400"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Yah, Internet Putus!</h1>
        <p class="text-gray-500 mb-6 leading-relaxed">
            Sepertinya koneksi internet Anda sedang bermasalah. Periksa kembali jaringan WiFi atau data seluler Anda.
        </p>
        <button onclick="window.location.reload()" class="w-full bg-teal-700 hover:bg-teal-800 text-white font-bold py-3 rounded-xl transition shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid fa-rotate-right"></i> Coba Lagi
        </button>
    </div>
</body>
</html>