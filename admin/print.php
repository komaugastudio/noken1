<?php
session_start();
include '../config/admin_guard.php';
include '../config/koneksi.php';

if (!isset($_SESSION['user'])) { header("Location: ../index.php"); exit; }

$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$judul_laporan = "Laporan Data";
$tgl = date('d F Y');

// LOGIKA DATA
if ($tipe == 'produk') {
    $where = "";
    if ($status == 'terjual') $where = "WHERE status = 'terjual'";
    
    $judul_laporan = "Laporan Data Produk " . ($status == 'terjual' ? '(TERJUAL)' : '(SEMUA)');
    $query = "SELECT * FROM produk $where ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $kolom = ['No', 'Nama Barang', 'Harga', 'Kategori', 'Penjual', 'Status', 'Tgl Upload'];
} 
elseif ($tipe == 'users') {
    $judul_laporan = "Laporan Data Pengguna Terdaftar";
    $query = "SELECT * FROM users ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $kolom = ['No', 'Nama Lengkap', 'Email', 'WhatsApp', 'Role', 'Tgl Daftar'];
}
elseif ($tipe == 'laporan') {
    $judul_laporan = "Arsip Laporan Masuk (Keluhan)";
    $query = "SELECT laporan.*, produk.judul FROM laporan LEFT JOIN produk ON laporan.produk_id = produk.id ORDER BY laporan.id DESC";
    $result = mysqli_query($conn, $query);
    $kolom = ['No', 'Barang Dilaporkan', 'Pelapor', 'Alasan / Detail', 'Tanggal Laporan'];
}
else {
    die("Tipe laporan tidak valid.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak - <?php echo $judul_laporan; ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #0F766E; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>NokenMART</h1>
        <p>Pusat Jual Beli Online Nabire & Papua Tengah</p>
        <p><em>Laporan digenerate pada: <?php echo $tgl; ?></em></p>
    </div>

    <h2 style="text-align: center;"><?php echo $judul_laporan; ?></h2>

    <table>
        <thead>
            <tr>
                <?php foreach($kolom as $k): ?>
                    <th><?php echo $k; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($result)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                
                <?php if ($tipe == 'produk'): ?>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td>Rp <?php echo number_format($row['harga']); ?></td>
                    <td><?php echo $row['kategori']; ?></td>
                    <td><?php echo $row['penjual']; ?></td>
                    <td><?php echo strtoupper($row['status']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_upload'])); ?></td>
                
                <?php elseif ($tipe == 'users'): ?>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['whatsapp']; ?></td>
                    <td><?php echo ucfirst($row['role']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>

                <?php elseif ($tipe == 'laporan'): ?>
                    <td><?php echo $row['judul'] ? htmlspecialchars($row['judul']) : '<i style="color:red">Barang dihapus</i>'; ?></td>
                    <td><?php echo $row['pelapor_email']; ?></td>
                    <td><?php echo htmlspecialchars($row['alasan']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Nabire, <?php echo $tgl; ?></p>
        <br><br><br>
        <p><strong>Administrator</strong></p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Lagi</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Tutup</button>
    </div>

</body>
</html>