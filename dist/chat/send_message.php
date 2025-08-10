<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['kodePengguna'])) {
    die("Silakan login terlebih dahulu.");
}

// Ambil ID numeric dari kodePengguna
$kodePengguna = $_SESSION['kodePengguna'];
$res = mysqli_query($kon, "SELECT kodePengguna FROM pengguna WHERE kodePengguna='$kodePengguna' LIMIT 1");
if (!$res || mysqli_num_rows($res) == 0) {
    die("Pengguna tidak ditemukan.");
}
$row = mysqli_fetch_assoc($res);
$idPengirim = $row['kodePengguna'];

$idPenerimaKode = $_POST['idPenerima'] ?? null;
$pesan = trim($_POST['pesan'] ?? '');

if (!$idPenerimaKode || $pesan === '') {
    die("Data tidak lengkap.");
}

// Cari idPenerima numeric
$res2 = mysqli_query($kon, "SELECT kodePengguna FROM pengguna WHERE kodePengguna='$idPenerimaKode' LIMIT 1");
if (!$res2 || mysqli_num_rows($res2) == 0) {
    die("Penerima tidak ditemukan.");
}
$row2 = mysqli_fetch_assoc($res2);
$idPenerima = $row2['kodePengguna'];

$sql = "INSERT INTO chat (idPengirim, idPenerima, pesan, waktu)
        VALUES ('$idPengirim', '$idPenerima', '$pesan', NOW())";
if (!mysqli_query($kon, $sql)) {
    die("Gagal mengirim pesan: " . mysqli_error($kon));
}

echo "OK";

?>
