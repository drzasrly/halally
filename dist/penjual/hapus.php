<?php
session_start();
    //Koneksi database
    include '../../config/database.php';

    mysqli_query($kon,"START TRANSACTION");


    //ambil url
    $idPenjual=$_GET['idPenjual'];
    $kodePenjual=$_GET['kodePenjual'];

    //Menghapus data dalam tabel karyawan
    $hapuspenjual=mysqli_query($kon," DELETE FROM penjual WHERE idPenjual='$idPenjual'");

    //Menghapus data dalam tabel pengguna
    $hapuspengguna=mysqli_query($kon,"DELETE FROM pengguna WHERE kodePengguna='$kodePenjual'");


    //Kondisi apakah berhasil atau tidak dalam mengeksekusi query diatas
    if ($hapuspenjual && $hapuspengguna) {
        mysqli_query($kon,"COMMIT");
        header("Location:../../dist/index.php?page=penjual&hapus=berhasil");
    }
    else {
        mysqli_query($kon,"ROLLBACK");
        header("Location:../../dist/index.php?page=penjual&hapus=gagal");

    }

?>