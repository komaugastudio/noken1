<?php
include 'config/koneksi.php';

echo "<h1>Upgrade Database Tanggal Premium...</h1>";

// Tambahkan kolom 'premium_until' (DATETIME)
$sql = "ALTER TABLE produk ADD COLUMN premium_until DATETIME NULL DEFAULT NULL";

try {
    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green;'>[OK] Kolom 'premium_until' berhasil ditambahkan.</p>";
    }
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), "Duplicate column") !== false) {
        echo "<p style='color: blue;'>Kolom sudah ada.</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<a href='index.php' style='background: #0F766E; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Kembali ke Website</a>";
?>