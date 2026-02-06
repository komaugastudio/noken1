<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Masa Aktif Iklan Premium</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-sync"></i> Proses Pengecekan Iklan Premium</h4>
        </div>
        <div class="card-body">

            <?php
            // Hubungkan ke database (Path disesuaikan karena file ada di dalam folder nokenmart)
            include 'config/koneksi.php';

            echo "<p class='text-muted'>Memulai proses pengecekan tanggal: " . date('d F Y H:i:s') . "</p><hr>";

            // -----------------------------------------------------------
            // 1. CEK IKLAN YANG SUDAH KADALUARSA (Expired)
            // -----------------------------------------------------------
            $now = date('Y-m-d H:i:s');
            $query_expired = mysqli_query($conn, "SELECT id, nama_produk, penjual FROM produk WHERE premium = 'ya' AND premium_until < '$now'");

            $count_expired = 0;
            echo "<h5>1. Memeriksa Iklan Kadaluarsa...</h5>";
            echo "<ul>";

            while ($row = mysqli_fetch_assoc($query_expired)) {
                $produk_id = $row['id'];
                $penjual_email = $row['penjual'];
                $nama_produk = mysqli_real_escape_string($conn, $row['nama_produk']);
                
                // 1. Update status jadi non-premium
                mysqli_query($conn, "UPDATE produk SET premium = 'tidak', premium_until = NULL WHERE id = '$produk_id'");
                
                // 2. Kirim Notifikasi ke User
                $q_user = mysqli_query($conn, "SELECT id FROM users WHERE email = '$penjual_email'");
                if($u = mysqli_fetch_assoc($q_user)){
                    $user_id = $u['id'];
                    $pesan = "Masa aktif Premium untuk iklan <b>$nama_produk</b> telah habis. Iklan kini menjadi reguler.";
                    $link = "detail.php?id=$produk_id";
                    
                    mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan, link, dibaca, created_at) VALUES ('$user_id', '$pesan', '$link', 0, NOW())");
                }
                
                echo "<li class='text-danger'>Iklan <b>$nama_produk</b> telah expired. Status Premium dicabut.</li>";
                $count_expired++;
            }

            if($count_expired == 0) {
                echo "<li class='text-success'>Tidak ada iklan yang expired hari ini.</li>";
            }
            echo "</ul>";


            // -----------------------------------------------------------
            // 2. INGATKAN IKLAN YANG AKAN HABIS DALAM 3 HARI
            // -----------------------------------------------------------
            echo "<hr><h5>2. Mengirim Reminder (H-3)...</h5>";
            echo "<ul>";

            $three_days_later = date('Y-m-d H:i:s', strtotime('+3 days'));
            $query_reminder = mysqli_query($conn, "SELECT id, nama_produk, penjual, premium_until FROM produk WHERE premium = 'ya' AND premium_until > '$now' AND premium_until <= '$three_days_later'");

            $count_reminded = 0;
            while ($row = mysqli_fetch_assoc($query_reminder)) {
                $produk_id = $row['id'];
                $penjual_email = $row['penjual'];
                $nama_produk = mysqli_real_escape_string($conn, $row['nama_produk']);
                $tgl_habis = date('d M Y', strtotime($row['premium_until']));
                
                $q_user = mysqli_query($conn, "SELECT id FROM users WHERE email = '$penjual_email'");
                if($u = mysqli_fetch_assoc($q_user)){
                    $user_id = $u['id'];
                    $pesan = "Reminder: Masa aktif Premium iklan <b>$nama_produk</b> akan habis pada $tgl_habis. Segera perpanjang!";
                    $link = "hubungi_admin.php"; 
                    
                    // Cek duplikasi notifikasi hari ini agar tidak spam
                    $cek_notif = mysqli_query($conn, "SELECT id FROM notifikasi WHERE user_id='$user_id' AND pesan LIKE '%$nama_produk%' AND DATE(created_at) = CURDATE()");
                    
                    if(mysqli_num_rows($cek_notif) == 0){
                        mysqli_query($conn, "INSERT INTO notifikasi (user_id, pesan, link, dibaca, created_at) VALUES ('$user_id', '$pesan', '$link', 0, NOW())");
                        echo "<li class='text-warning'>Mengirim reminder untuk <b>$nama_produk</b> (Habis: $tgl_habis).</li>";
                        $count_reminded++;
                    }
                }
            }

            if($count_reminded == 0) {
                echo "<li class='text-success'>Tidak ada iklan yang perlu diingatkan (H-3) hari ini.</li>";
            }
            echo "</ul>";
            ?>

            <div class="alert alert-success mt-4">
                <strong>Selesai!</strong> Proses pengecekan telah berhasil dilakukan.
            </div>

            <div class="text-center mt-4">
                <!-- Link kembali ke Dashboard Admin disesuaikan -->
                <a href="admin/dashboard.php" class="btn btn-primary btn-lg">Kembali ke Dashboard Admin</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>