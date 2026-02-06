<?php
include 'config/koneksi.php';

// Helper SweetAlert
function sweetAlert($icon, $title, $text, $redirect) {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Proses Newsletter...</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                confirmButtonColor: '#0F766E', // Warna Teal NokenMART
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                window.location = '$redirect';
            });
        </script>
    </body>
    </html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email_subs'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email_subs']);

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sweetAlert('error', 'Email Tidak Valid', 'Mohon masukkan alamat email yang benar.', 'index.php');
    }

    // Cek apakah email sudah terdaftar
    $cek = mysqli_query($conn, "SELECT email FROM subscribers WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        sweetAlert('info', 'Sudah Terdaftar', 'Email Anda sudah ada dalam daftar berlangganan kami. Terima kasih!', 'index.php');
    }

    // Simpan
    $query = "INSERT INTO subscribers (email) VALUES ('$email')";
    if (mysqli_query($conn, $query)) {
        sweetAlert('success', 'Berhasil Berlangganan!', 'Terima kasih! Kami akan mengirimkan info menarik seputar NokenMART untuk Anda.', 'index.php');
    } else {
        sweetAlert('error', 'Gagal', 'Terjadi kesalahan sistem saat menyimpan email Anda.', 'index.php');
    }
} else {
    header("Location: index.php");
}
?>