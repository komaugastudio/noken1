<?php
session_start();
include 'config/koneksi.php';

/**
 * NokenMART Authentication System
 * Perbaikan Keamanan: Password Hashing, Image Validation, & Role-based Access.
 * Organisasi Folder: Menggunakan nama folder Indonesia agar sinkron dengan aset fisik.
 */

// Fungsi Helper untuk Alert (MENGGUNAKAN EXIT AGAR EKSEKUSI BERHENTI)
function sweetAlert($icon, $title, $text, $redirect) {
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                confirmButtonColor: '#0F766E'
            }).then(() => {
                window.location = '$redirect';
            });
        </script>
    </body>
    </html>";
    exit;
}

// --- 1. PROSES REGISTER ---
if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Hashing password untuk keamanan (Bcrypt)
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek Duplikat Email
    $cek_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        sweetAlert('error', 'Gagal Daftar', 'Email sudah terdaftar! Gunakan email lain.', 'index.php');
    }

    $foto_ktp = "";
    $foto_selfie = "";

    // Logika Khusus Penjual (Upload Berkas ke Folder Terorganisir)
    if ($role == 'penjual') {
        // PERBAIKAN: Menggunakan folder 'identitas' agar sinkron dengan file fisik Anda
        $target_dir = "assets/img/identitas/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        // a. Validasi & Upload KTP
        if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] == 0) {
            if (getimagesize($_FILES['foto_ktp']['tmp_name']) !== false) {
                $ext = pathinfo($_FILES['foto_ktp']['name'], PATHINFO_EXTENSION);
                $nama_ktp = "ktp_" . uniqid() . "." . $ext;
                $path_ktp = $target_dir . $nama_ktp;
                if (move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $path_ktp)) {
                    $foto_ktp = $path_ktp;
                }
            } else {
                sweetAlert('error', 'File Ditolak', 'Berkas KTP harus berupa gambar asli!', 'index.php');
            }
        }

        // b. Simpan Selfie dari Kamera (Base64)
        if (!empty($_POST['foto_selfie_base64'])) {
            $data_uri = $_POST['foto_selfie_base64'];
            $encoded_image = explode(",", $data_uri)[1];
            $decoded_image = base64_decode($encoded_image);
            
            $nama_selfie = "selfie_" . uniqid() . ".jpg";
            $path_selfie = $target_dir . $nama_selfie;
            
            if (file_put_contents($path_selfie, $decoded_image)) {
                $foto_selfie = $path_selfie;
            }
        }

        if (empty($foto_ktp) || empty($foto_selfie)) {
            sweetAlert('error', 'Data Kurang', 'Penjual wajib melampirkan KTP dan Foto Selfie.', 'index.php');
        }
    }

    // Simpan ke Database
    $query = "INSERT INTO users (nama, email, password, role, whatsapp, foto_ktp, foto_selfie, is_verified) 
              VALUES ('$nama', '$email', '$password', '$role', '$whatsapp', '$foto_ktp', '$foto_selfie', 0)";
    
    if (mysqli_query($conn, $query)) {
        if ($role == 'penjual') {
            sweetAlert('success', 'Pendaftaran Berhasil', 'Akun Penjual Anda sedang ditinjau Admin. Tunggu konfirmasi WhatsApp.', 'index.php');
        } else {
            // Pembeli langsung login otomatis
            $_SESSION['user'] = $email;
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
            $_SESSION['wa']   = $whatsapp;
            sweetAlert('success', 'Selamat Datang', 'Pendaftaran berhasil. Silakan mulai berbelanja!', 'index.php');
        }
    }
}

// --- 2. PROSES LOGIN ---
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_input = $_POST['password'];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($q) == 1) {
        $user = mysqli_fetch_assoc($q);
        
        // Verifikasi Hash Password
        if (password_verify($password_input, $user['password'])) {
            
            // Cek jika penjual belum diverifikasi
            if ($user['role'] == 'penjual' && $user['is_verified'] == 0) {
                sweetAlert('warning', 'Menunggu Verifikasi', 'Akun Anda belum disetujui Admin. Mohon tunggu proses validasi data.', 'index.php');
            }

            // Set Data Session
            $_SESSION['user'] = $user['email'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['wa']   = $user['whatsapp'];
            $_SESSION['foto'] = $user['foto_profil'];

            // Log Aktivitas
            if (function_exists('catat_log')) {
                catat_log($conn, "Login", "User $email berhasil masuk ke sistem.");
            }

            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                sweetAlert('success', 'Login Berhasil', 'Halo ' . $user['nama'] . ', selamat datang kembali!', 'index.php');
            }
            exit;
        }
    }
    sweetAlert('error', 'Gagal Masuk', 'Email atau Password salah. Silakan coba lagi.', 'index.php');
}

// --- 3. PROSES UPDATE PROFIL ---
if (isset($_POST['update_profile'])) {
    if (!isset($_SESSION['user'])) exit;
    $email_current = $_SESSION['user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    
    $sql_update = "UPDATE users SET nama='$nama', whatsapp='$whatsapp'";

    // a. Ganti Password jika diisi
    if (!empty($_POST['password'])) {
        $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql_update .= ", password='$new_pass'";
    }

    // b. Update Foto Profil ke folder Terorganisir
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        if (getimagesize($_FILES['foto_profil']['tmp_name']) !== false) {
            // PERBAIKAN: Menggunakan folder 'profil' agar sinkron dengan file fisik Anda
            $dir_profiles = "assets/img/profil/";
            if (!is_dir($dir_profiles)) mkdir($dir_profiles, 0777, true);
            
            $ext = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
            $nama_foto = "user_" . uniqid() . "." . $ext;
            $path_foto = $dir_profiles . $nama_foto;
            
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $path_foto)) {
                // Hapus foto lama jika bukan default/eksternal
                if (!empty($_SESSION['foto']) && strpos($_SESSION['foto'], 'assets/') === 0 && file_exists($_SESSION['foto'])) {
                    unlink($_SESSION['foto']);
                }
                $sql_update .= ", foto_profil='$path_foto'";
                $_SESSION['foto'] = $path_foto; 
            }
        }
    }

    $sql_update .= " WHERE email='$email_current'";
    if (mysqli_query($conn, $sql_update)) {
        // Update Data Sesi agar tampilan Header langsung berubah
        $_SESSION['nama'] = $nama;
        $_SESSION['wa'] = $whatsapp;
        sweetAlert('success', 'Profil Diperbarui', 'Data Anda berhasil disimpan.', 'pengaturan.php');
    }
}

// --- 4. LOGOUT ---
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
?>