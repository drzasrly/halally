<?php
    include '../../../config/database.php';

    function input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $idKategori=input($_GET["idKategori"]);
 

    $hapus_kategori_barang=mysqli_query($kon,"delete from kategoribarang where idKategori=$idKategori");
    if ($hapus_kategori_barang) {
        header("Location:../../../dist/index.php?page=kategori&hapus=berhasil");
    }
    else {
        header("Location:../../../dist/index.php?page=kategori&hapus=gagal");
    }
    
?>
