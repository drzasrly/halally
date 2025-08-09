<?php
session_start();
include '../../config/database.php';

// Hanya untuk pelanggan yang menekan tombol submit donasi dari halaman detail
if (!isset($_SESSION['idPengguna']) || !isset($_POST['submit_donasi']) || !isset($_POST['idKampanye'])) {
    header("Location: ../index.php"); 
    exit();
}

$idPelanggan = $_SESSION['idPengguna'];
$idKampanye = intval($_POST['idKampanye']);
$jumlah = intval($_POST['jumlah']);
$pesan_error = '';
$nama_file_unik = '';

// Proses upload file bukti pembayaran...
if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
    $target_dir = "../uploads/bukti_pembayaran/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $nama_file_asli = basename($_FILES["bukti_pembayaran"]["name"]);
    $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    $nama_file_unik = uniqid() . '-bukti-' . time() . '.' . $ekstensi_file;
    $target_file = $target_dir . $nama_file_unik;
    
    if (!move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
        $pesan_error = "Gagal mengunggah file bukti.";
    }
} else {
    $pesan_error = "Bukti pembayaran wajib diunggah.";
}

if (!empty($pesan_error)) {
    header("Location: ../index.php?page=detail-donasi&id=$idKampanye&status=gagal&pesan=" . urlencode($pesan_error));
    exit();
}

// Hanya membuat catatan transaksi baru dengan status "Menunggu Konfirmasi"
$stmt = mysqli_prepare($kon, "INSERT INTO transaksi_sosial (tipe, idKampanye, idPelanggan, jumlah, bukti_pembayaran, status_pembayaran) VALUES ('Donasi', ?, ?, ?, ?, 'Menunggu Konfirmasi')");
mysqli_stmt_bind_param($stmt, "iiis", $idKampanye, $idPelanggan, $jumlah, $nama_file_unik);

if(mysqli_stmt_execute($stmt)){
    // Redirect ke halaman riwayat setelah berhasil submit
    header("Location: ../index.php?page=riwayat-kebaikan&status=sukses_submit");
} else {
    header("Location: ../index.php?page=detail-donasi&id=$idKampanye&status=gagal&pesan=dberror");
}

exit();
?>