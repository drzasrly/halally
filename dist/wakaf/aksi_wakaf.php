<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['idPengguna']) || !isset($_POST['submit_wakaf'])) {
    header("Location: ../index.php"); exit();
}

$idPelanggan = $_SESSION['idPengguna'];
$jenis_wakaf = $_POST['jenis_wakaf'];
$jumlah = intval($_POST['jumlah']);
$idKampanye = ($jenis_wakaf == 'proyek') ? intval($_POST['idKampanye']) : NULL;
$pesan_error = ''; $nama_file_unik = '';

// Proses Upload Bukti
if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
    $target_dir = "../../uploads/bukti_pembayaran/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $nama_file_asli = basename($_FILES["bukti_pembayaran"]["name"]);
    $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    $nama_file_unik = uniqid() . '-buktiwakaf-' . time() . '.' . $ekstensi_file;
    $target_file = $target_dir . $nama_file_unik;
    if (!move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
        $pesan_error = "Gagal unggah bukti.";
    }
} else {
    $pesan_error = "Bukti pembayaran wajib diunggah.";
}

if (!empty($pesan_error)) {
    header("Location: ../index.php?page=wakaf&status=gagal&pesan=" . urlencode($pesan_error));
    exit();
}

// Simpan ke DB
$stmt = mysqli_prepare($kon, "INSERT INTO transaksi_sosial (tipe, idKampanye, idPelanggan, jumlah, bukti_pembayaran, status_pembayaran) VALUES ('Wakaf', ?, ?, ?, ?, 'Menunggu Konfirmasi')");
mysqli_stmt_bind_param($stmt, "iiis", $idKampanye, $idPelanggan, $jumlah, $nama_file_unik);

if(mysqli_stmt_execute($stmt)){
    header("Location: ../index.php?page=riwayat-kebaikan&status=sukses_wakaf");
} else {
    header("Location: ../index.php?page=wakaf&status=gagal&pesan=dberror");
}
exit();
?>