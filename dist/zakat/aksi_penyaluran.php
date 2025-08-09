<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin') || !isset($_POST['submit_penyaluran'])) {
    header("Location: ../index.php"); exit();
}

$tipe_dana = $_POST['tipe_dana'];
$tanggal = $_POST['tanggal_penyaluran'];
$jumlah = $_POST['jumlah'];
$penerima = $_POST['disalurkan_kepada'];
$keterangan = $_POST['keterangan'];
$idAdmin = $_SESSION['idPengguna'];

$stmt = mysqli_prepare($kon, "INSERT INTO penyaluran_dana (tipe_dana, tanggal_penyaluran, jumlah, disalurkan_kepada, keterangan, dicatat_oleh) VALUES (?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssdssi", $tipe_dana, $tanggal, $jumlah, $penerima, $keterangan, $idAdmin);
mysqli_stmt_execute($stmt);

header("Location: ../index.php?page=manajemen-zakat&status=sukses_salur");
exit();
?>