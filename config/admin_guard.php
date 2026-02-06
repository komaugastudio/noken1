<?php
/**
 * admin_guard.php
 * File keamanan pusat untuk folder admin.
 * Mencegah akses tanpa login atau akses oleh user non-admin.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    // Belum login, arahkan ke login
    header("Location: ../index.php?pesan=belum_login");
    exit;
}

// 2. Cek apakah role user adalah 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Sudah login tapi bukan admin (misal: pembeli mencoba masuk admin)
    // Tampilkan pesan error sederhana dan stop eksekusi atau redirect
    echo "<script>
            alert('Akses Ditolak! Anda tidak memiliki izin Administrator.');
            window.location='../index.php';
          </script>";
    exit;
}
?>