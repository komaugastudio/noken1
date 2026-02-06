<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// 1. Ambil FAQ dari database
try {
    $faqs = mysqli_query($conn, "SELECT * FROM faqs ORDER BY urutan ASC, id ASC");
} catch (mysqli_sql_exception $e) {
    // Auto-fix jika tabel belum ada
    $sql_create = "CREATE TABLE IF NOT EXISTS faqs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pertanyaan VARCHAR(255) NOT NULL,
        jawaban TEXT NOT NULL,
        urutan INT DEFAULT 0
    )";
    mysqli_query($conn, $sql_create);
    $faqs = false; // Kosong dulu
}

// 2. Ambil Pengaturan Website (PERBAIKAN DISINI)
// Kita gunakan SELECT * agar data $web lengkap untuk Footer juga
try {
    $q_web = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id=1");
    $web = mysqli_fetch_assoc($q_web);
} catch (Exception $e) {
    $web = null;
}

// Fallback jika database kosong/error agar tidak warning
if (!$web) {
    $web = [
        'nama_website' => 'NokenMART',
        'deskripsi_footer' => 'Membantu UMKM dan warga Nabire.',
        'wa_admin' => '628123456789',
        'alamat' => 'Nabire, Papua Tengah'
    ];
}

$wa_admin = $web['wa_admin']; // Variabel untuk tombol di bawah
?>

<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Pusat Bantuan</h1>
            <p class="text-gray-600">Temukan jawaban atas pertanyaan Anda seputar NokenMART</p>
        </div>

        <div class="space-y-4">
            <?php if($faqs && mysqli_num_rows($faqs) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($faqs)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <details class="group p-6 cursor-pointer">
                        <summary class="flex justify-between items-center font-bold text-gray-800 list-none text-lg">
                            <span><?php echo htmlspecialchars($row['pertanyaan']); ?></span>
                            <span class="transition group-open:rotate-180">
                                <i class="fa-solid fa-chevron-down text-gray-400"></i>
                            </span>
                        </summary>
                        <div class="text-gray-600 mt-4 text-sm leading-relaxed border-t border-gray-100 pt-4">
                            <?php echo nl2br(htmlspecialchars($row['jawaban'])); ?>
                        </div>
                    </details>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                    <i class="fa-regular fa-circle-question text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Belum ada pertanyaan yang ditambahkan oleh Admin.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-12 text-center">
            <p class="text-gray-600 mb-4">Masih punya pertanyaan lain?</p>
            <a href="https://wa.me/<?php echo htmlspecialchars($wa_admin); ?>" class="inline-flex items-center px-6 py-3 bg-green-500 text-white font-bold rounded-full hover:bg-green-600 transition shadow-md">
                <i class="fa-brands fa-whatsapp mr-2 text-xl"></i> Hubungi Admin
            </a>
        </div>

    </div>
</div>

<?php include 'templates/footer.php'; ?>