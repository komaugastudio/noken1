<?php
/**
 * Script Pembersih NokenMART
 * Menghapus file instalasi/upgrade agar website aman.
 */

$files_to_delete = [
    'hash_password.php',
    'fix_db.php',
    'update_db.php',
    'update_views.php',
    // Daftar semua file upgrade
    'upgrade_banner.php', 'upgrade_blog.php', 'upgrade_deskripsi.php', 
    'upgrade_faq.php', 'upgrade_fitur_baru.php', 'upgrade_foto_profil.php', 
    'upgrade_galeri.php', 'upgrade_kategori.php', 'upgrade_kategori_icon.php', 
    'upgrade_komentar.php', 'upgrade_kondisi.php', 'upgrade_logs.php', 
    'upgrade_maintenance.php', 'upgrade_newsletter.php', 'upgrade_notifikasi.php', 
    'upgrade_pages.php', 'upgrade_pengaturan.php', 'upgrade_pengunjung.php', 
    'upgrade_premium.php', 'upgrade_registrasi.php', 'upgrade_rekening.php', 
    'upgrade_sosmed.php', 'upgrade_sundul.php', 'upgrade_tips.php', 
    'upgrade_ulasan.php', 'upgrade_verifikasi.php', 'upgrade_wilayah.php',
    'nokenmart_db.sql' // Hapus backup SQL awal jika sudah diimport
];

echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h1>🧹 Pembersihan Sistem NokenMART</h1>";
echo "<ul>";

$deleted_count = 0;

foreach ($files_to_delete as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "<li style='color: green;'>✅ Berhasil menghapus: <strong>$file</strong></li>";
            $deleted_count++;
        } else {
            echo "<li style='color: red;'>❌ Gagal menghapus: <strong>$file</strong> (Cek izin folder)</li>";
        }
    }
}

if ($deleted_count == 0) {
    echo "<li style='color: blue;'>✨ Sistem sudah bersih! Tidak ada file sampah ditemukan.</li>";
}

echo "</ul>";
echo "<hr>";
echo "<p>Total file dihapus: <strong>$deleted_count</strong></p>";
echo "<p style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'>⚠️ <strong>PENTING:</strong> Sekarang hapus file <code>cleanup.php</code> ini secara manual melalui File Manager Anda agar tidak dijalankan orang lain.</p>";
echo "<a href='index.php' style='background: #0F766E; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ke Halaman Utama</a>";
echo "</div>";
?>