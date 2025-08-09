<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['idPengguna']) || !isset($_POST['submit_zakat'])) {
    header("Location: ../index.php"); exit();
}

$idPelanggan = $_SESSION['idPengguna'];
$jumlah = intval($_POST['jumlah']);
$pesan_error = '';
$nama_file_unik = '';

// ===== BAGIAN YANG DIPERBAIKI: PROSES UPLOAD FILE =====
if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
    $target_dir = "../../uploads/bukti_pembayaran/"; // Pastikan folder ini ada
    
    // Buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $nama_file_asli = basename($_FILES["bukti_pembayaran"]["name"]);
    $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    $nama_file_unik = uniqid() . '-bukti-' . time() . '.' . $ekstensi_file;
    $target_file = $target_dir . $nama_file_unik;

    // Validasi ekstensi & ukuran
    $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
        $pesan_error = "Format file bukti salah. Hanya JPG, PNG, PDF yang diizinkan.";
    } elseif ($_FILES["bukti_pembayaran"]["size"] > 2000000) { // Maks 2MB
        $pesan_error = "Ukuran file bukti terlalu besar.";
    }

    // Jika tidak ada error, pindahkan file
    if (empty($pesan_error) && !move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
        $pesan_error = "Gagal mengunggah file bukti.";
    }
} else {
    $pesan_error = "Bukti pembayaran wajib diunggah.";
}

// Jika ada error saat upload, redirect kembali
if (!empty($pesan_error)) {
    // Arahkan kembali ke halaman pembayaran dengan pesan error
    header("Location: ../index.php?page=bayar-zakat&status=gagal&pesan=" . urlencode($pesan_error));
    exit();
}
// ===== AKHIR BAGIAN YANG DIPERBAIKI =====


// Simpan ke database (Sekarang nama_file_unik sudah terisi)
$stmt = mysqli_prepare($kon, "INSERT INTO transaksi_sosial (tipe, idPelanggan, jumlah, bukti_pembayaran, status_pembayaran) VALUES ('Zakat', ?, ?, ?, 'Menunggu Konfirmasi')");
mysqli_stmt_bind_param($stmt, "iis", $idPelanggan, $jumlah, $nama_file_unik);

if(mysqli_stmt_execute($stmt)){
    // Arahkan ke halaman riwayat baru yang akan kita buat
    header("Location: ../index.php?page=riwayat-kebaikan&status=sukses_zakat");
} else {
    header("Location: ../index.php?page=bayar-zakat&status=gagal&pesan=dberror");
}

exit();
?>