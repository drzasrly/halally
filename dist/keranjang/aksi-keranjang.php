<?php
session_start();
header('Content-Type: application/json');
include '../../config/database.php';

if (!isset($_POST['aksi'])) {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak ditentukan.']);
    exit;
}

if (!isset($_SESSION['idPengguna'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu.']);
    exit;
}

$idPengguna = $_SESSION['idPengguna'];
$aksi = $_POST['aksi'];

if ($aksi === 'hapus_item') {
    $idVarian = $_POST['idVarian'] ?? null;
    if (!$idVarian) {
        echo json_encode(['status' => 'error', 'message' => 'ID Varian tidak valid.']);
        exit;
    }

    $delete_query = mysqli_query($kon, "DELETE FROM keranjang WHERE idPengguna = '$idPengguna' AND idVarian = '$idVarian'");

    if (!$delete_query) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus barang dari database.']);
        exit;
    }
}
$queryTotal = mysqli_query($kon, "SELECT count(idKeranjang) as total FROM keranjang WHERE idPengguna = '$idPengguna'");
$dataTotal = mysqli_fetch_assoc($queryTotal);
$jumlah_baru = $dataTotal['total'] ?? 0;

echo json_encode([
    'status' => 'success',
    'message' => 'Aksi berhasil dilakukan.',
    'jumlah_baru' => $jumlah_baru
]);

exit;
?>