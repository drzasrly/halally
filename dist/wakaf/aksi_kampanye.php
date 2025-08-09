<?php
session_start();
include '../../config/database.php';

// Ini adalah adaptasi dari donasi/aksi_kampanye.php, disesuaikan untuk wakaf
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin') || !isset($_POST['submit'])) {
    header("Location: ../index.php?page=manajemen-wakaf&status=gagal");
    exit();
}
$aksi = $_POST['aksi'];
$tipe = $_POST['tipe']; // Seharusnya selalu 'Wakaf'
$idKampanye = intval($_POST['idKampanye'] ?? 0);
$judul = mysqli_real_escape_string($kon, $_POST['judul']);
$deskripsi = mysqli_real_escape_string($kon, $_POST['deskripsi']);
$target_dana = intval($_POST['target_dana']);
$status = mysqli_real_escape_string($kon, $_POST['status']);
$nama_file_gambar = '';

// Proses Upload Gambar
if (isset($_FILES['gambar_kampanye']) && $_FILES['gambar_kampanye']['error'] == 0) {
    $target_dir = "../../uploads/kampanye_wakaf/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $nama_file_asli = basename($_FILES["gambar_kampanye"]["name"]);
    $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    $nama_file_gambar = uniqid() . '-wakaf-' . time() . '.' . $ekstensi_file;
    $target_file = $target_dir . $nama_file_gambar;
    move_uploaded_file($_FILES["gambar_kampanye"]["tmp_name"], $target_file);
}

// Logika Simpan ke DB
if ($aksi == 'tambah') {
    $stmt = mysqli_prepare($kon, "INSERT INTO kampanye_sosial (judul, deskripsi, tipe, target_dana, status, gambar_kampanye) VALUES (?, ?, 'Wakaf', ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssiss", $judul, $deskripsi, $target_dana, $status, $nama_file_gambar);
} elseif ($aksi == 'edit' && $idKampanye > 0) {
    if (!empty($nama_file_gambar)) {
        $stmt = mysqli_prepare($kon, "UPDATE kampanye_sosial SET judul=?, deskripsi=?, target_dana=?, status=?, gambar_kampanye=? WHERE idKampanye=?");
        mysqli_stmt_bind_param($stmt, "ssissi", $judul, $deskripsi, $target_dana, $status, $nama_file_gambar, $idKampanye);
    } else {
        $stmt = mysqli_prepare($kon, "UPDATE kampanye_sosial SET judul=?, deskripsi=?, target_dana=?, status=? WHERE idKampanye=?");
        mysqli_stmt_bind_param($stmt, "ssisi", $judul, $deskripsi, $target_dana, $status, $idKampanye);
    }
}

if (isset($stmt) && mysqli_stmt_execute($stmt)) {
    header("Location: ../index.php?page=manajemen-wakaf&status=sukses");
} else {
    header("Location: ../index.php?page=manajemen-wakaf&status=gagal");
}
exit();
?>