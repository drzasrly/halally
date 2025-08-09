<?php
session_start();
include '../../config/database.php';

// Hanya admin yang bisa mengakses file ini
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    header("Location: ../index.php?page=verifikasi-sertifikat&status=gagal");
    exit();
}

// Cek apakah parameter ID dan Aksi ada
if (!isset($_GET['id']) || !isset($_GET['aksi'])) {
    header("Location: ../index.php?page=verifikasi-sertifikat&status=gagal");
    exit();
}

$idSertifikat = intval($_GET['id']);
$aksi = $_GET['aksi'];
$berhasil = false;

if ($aksi == 'setujui') {
    // Aksi untuk menyetujui sertifikat
    $stmt = mysqli_prepare($kon, "UPDATE sertifikat_halal SET status_verifikasi = 'Diverifikasi', catatan_admin = NULL WHERE idSertifikat = ?");
    mysqli_stmt_bind_param($stmt, "i", $idSertifikat);
    if (mysqli_stmt_execute($stmt)) {
        $berhasil = true;
    }
    mysqli_stmt_close($stmt);

} elseif ($aksi == 'tolak' && isset($_POST['submit_tolak'])) {
    // Aksi untuk menolak sertifikat
    $catatan = mysqli_real_escape_string($kon, $_POST['catatan']);
    if (!empty($catatan)) {
        $stmt = mysqli_prepare($kon, "UPDATE sertifikat_halal SET status_verifikasi = 'Ditolak', catatan_admin = ? WHERE idSertifikat = ?");
        mysqli_stmt_bind_param($stmt, "si", $catatan, $idSertifikat);
        if (mysqli_stmt_execute($stmt)) {
            $berhasil = true;
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($kon);

// Redirect kembali ke halaman verifikasi dengan status
if ($berhasil) {
    header("Location: ../index.php?page=verifikasi-sertifikat&status=sukses");
} else {
    header("Location: ../index.php?page=verifikasi-sertifikat&status=gagal");
}
exit();

?>