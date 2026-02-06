<?php
// Pastikan koneksi sudah ada
if (isset($conn)) {
    
    $sekarang = date('Y-m-d H:i:s');

    // 1. Cari produk yang PREMIUM tapi sudah LEWAT TANGGAL
    $query = "SELECT * FROM produk WHERE is_premium = 1 AND premium_until < '$sekarang' AND premium_until IS NOT NULL";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id_produk = $row['id'];
            $judul = $row['judul'];
            $penjual_email = $row['penjual'];

            // A. Turunkan status jadi Regular (0)
            mysqli_query($conn, "UPDATE produk SET is_premium = 0, premium_until = NULL WHERE id = $id_produk");

            // B. Kirim Notifikasi ke Penjual
            $pesan = "Masa aktif Premium untuk iklan <strong>$judul</strong> telah berakhir. Iklan kini kembali ke status Reguler.";
            $link = "profil.php"; // Arahkan ke profil
            
            // Cek biar notifikasi tidak dobel (opsional, tapi bagus)
            // Langsung insert saja agar user tahu
            $q_notif = "INSERT INTO notifikasi (user_email, pesan, link, status, tanggal) 
                        VALUES ('$penjual_email', '$pesan', '$link', 'belum_dibaca', NOW())";
            mysqli_query($conn, $q_notif);
        }
    }
}
?>