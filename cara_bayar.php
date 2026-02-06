<?php
session_start();
include 'config/koneksi.php';
include 'templates/header.php';

// Ambil Data Rekening
$q_rek = mysqli_query($conn, "SELECT * FROM rekening ORDER BY id ASC");
?>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Cara Pembayaran</h1>
            <p class="text-gray-600">Silakan lakukan pembayaran biaya iklan Premium ke salah satu rekening di bawah ini.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 md:p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-nabire-primary"></i> Rekening Resmi
                </h2>

                <div class="space-y-4">
                    <?php if(mysqli_num_rows($q_rek) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($q_rek)): ?>
                        <div class="flex flex-col sm:flex-row items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-4 mb-3 sm:mb-0">
                                <?php if(!empty($row['logo_bank'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['logo_bank']); ?>" class="h-12 w-auto object-contain">
                                <?php else: ?>
                                    <div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 font-bold text-xs">BANK</div>
                                <?php endif; ?>
                                <div>
                                    <p class="text-gray-500 text-xs uppercase font-bold"><?php echo htmlspecialchars($row['nama_bank']); ?></p>
                                    <p class="text-lg font-mono font-bold text-gray-800 tracking-wide"><?php echo htmlspecialchars($row['nomor_rekening']); ?></p>
                                    <p class="text-sm text-gray-600">a.n. <?php echo htmlspecialchars($row['atas_nama']); ?></p>
                                </div>
                            </div>
                            <button onclick="copyText('<?php echo $row['nomor_rekening']; ?>')" class="text-nabire-secondary hover:text-yellow-600 font-medium text-sm flex items-center gap-1">
                                <i class="fa-regular fa-copy"></i> Salin
                            </button>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 italic">Belum ada rekening yang tersedia.</p>
                    <?php endif; ?>
                </div>

                <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <h4 class="font-bold text-blue-800 mb-2 flex items-center gap-2"><i class="fa-solid fa-circle-info"></i> Konfirmasi Pembayaran</h4>
                    <p class="text-sm text-blue-700 leading-relaxed">
                        Setelah melakukan transfer, harap kirimkan bukti transfer (screenshot/foto struk) kepada Admin melalui WhatsApp agar iklan Premium Anda segera diaktifkan.
                    </p>
                    <a href="https://wa.me/<?php echo $web['wa_admin']; ?>" target="_blank" class="inline-block mt-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-lg transition">
                        <i class="fa-brands fa-whatsapp mr-1"></i> Kirim Bukti Transfer
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function copyText(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Nomor rekening berhasil disalin!');
        }, function(err) {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>

<?php include 'templates/footer.php'; ?>