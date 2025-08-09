<?php
session_start();
    if (isset($_POST['edit_kategori_barang'])) {
        include '../../../config/database.php';

        mysqli_query($kon,"START TRANSACTION");
        
        function input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        $idKategori=input($_POST["idKategori"]);
        $namaKategori=input($_POST["namaKategori"]);
        
        $sql="update kategoribarang set
        namaKategori='$namaKategori'
        where idKategori=$idKategori";
 
        $edit_kategori_barang=mysqli_query($kon,$sql);
        if ($edit_kategori_barang) {
            mysqli_query($kon,"COMMIT");
            header("Location:../../../dist/index.php?page=kategori&edit=berhasil");
        }
        else {
            mysqli_query($kon,"ROLLBACK");
            header("Location:../../../dist/index.php?page=kategori&edit=gagal");
        }
        
    }

    //-------------------------------------------------------------------------------------------

    $idKategori=$_POST["idKategori"];
    include '../../../config/database.php';
    $query = mysqli_query($kon, "SELECT * FROM kategoribarang where idKategori=$idKategori");
    $data = mysqli_fetch_array($query); 

    $kodeKategori=$data['kodeKategori'];
    $namaKategori=$data['namaKategori'];
 
?>
<form action="barang/kategori/edit-kategori.php" method="post">
    <div class="form-group">
        <label>Kode kategori Barang:</label>
        <h3><?php echo $kodeKategori; ?></h3>
        <input name="kodeKategori" value="<?php echo $kodeKategori; ?>" type="hidden" class="form-control">
        <input name="idKategori" value="<?php echo $idKategori; ?>" type="hidden" class="form-control">
    </div>
    <div class="form-group">
        <label>Nama kategori Barang:</label>
        <input name="namaKategori" value="<?php echo $namaKategori; ?>" type="text" class="form-control" placeholder="Masukan nama kategori" required>
    </div>

    <button type="submit" name="edit_kategori_barang" id="btn-kategori_barang" class="btn btn-dark" >Update</button>
</form>