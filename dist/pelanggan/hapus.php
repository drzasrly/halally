<?php
session_start();
// Koneksi database
include '../../config/database.php';

mysqli_query($kon, "START TRANSACTION");

// Ambil data dari URL
$idPelanggan = $_GET['idPelanggan'];
$kodePelanggan = $_GET['kodePelanggan'];

// Hapus data di tabel pelanggan
$hapuspelanggan = mysqli_query($kon, "DELETE FROM pelanggan WHERE idPelanggan='$idPelanggan'");

// Hapus data di tabel pengguna (perbaiki nama kolom dan variabel)
$hapuspengguna = mysqli_query($kon, "DELETE FROM pengguna WHERE kodePengguna='$kodePelanggan'");

// Cek apakah kedua query berhasil
if ($hapuspelanggan && $hapuspengguna) {
    mysqli_query($kon, "COMMIT");
    header("Location:../../dist/index.php?page=pelanggan&hapus=berhasil");
} else {
    mysqli_query($kon, "ROLLBACK");
    header("Location:../../dist/index.php?page=pelanggan&hapus=gagal");
}
exit;
?>
