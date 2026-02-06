<?php
// Set Waktu Indonesia Timur (Papua)
date_default_timezone_set('Asia/Jayapura');

$host = "localhost";
$user = "root";
$pass = "";
$db   = "nokenmart_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// Fungsi Pencatat Log Global
if (!function_exists('catat_log')) {
    function catat_log($conn, $aksi, $detail) {
        $email = isset($_SESSION['user']) ? $_SESSION['user'] : 'System/Guest';
        $ip = $_SERVER['REMOTE_ADDR'];
        $aksi = mysqli_real_escape_string($conn, $aksi);
        $detail = mysqli_real_escape_string($conn, $detail);
        
        $sql = "INSERT INTO logs (user_email, aksi, detail, ip_address) VALUES ('$email', '$aksi', '$detail', '$ip')";
        mysqli_query($conn, $sql);
    }
}
?>