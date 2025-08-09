<?php
session_start();
    if (isset($_POST['tambah_kategori_barang'])) {
        
        include '../../../config/database.php';
        
        function input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            mysqli_query($kon,"START TRANSACTION");

            $kodeKategori=input($_POST["kodeKategori"]);
            $namaKategori=input($_POST["namaKategori"]);

            $sql="insert into kategoribarang (kodeKategori,namaKategori) values
                ('$kodeKategori','$namaKategori')";

            $simpan_kategori_barang=mysqli_query($kon,$sql);

            if ($simpan_kategori_barang) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../dist/index.php?page=kategori&add=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../dist/index.php?page=kategori&add=gagal");
            }

        }
       
    }
?>


<?php
    include '../../../config/database.php';
    $query = mysqli_query($kon, "SELECT max(idKategori) as kodeTerbesar FROM kategoribarang");
    $data = mysqli_fetch_array($query);
    $idKategori = $data['kodeTerbesar'];
    $idKategori++;
    $huruf = "kat";
    $kodeKategori = $huruf . sprintf("%03s", $idKategori);
?>
<form action="barang/kategori/tambah-kategori.php" method="post">
    <div class="form-group">
        <label>Kode kategori Barang:</label>
        <h3><?php echo $kodeKategori; ?></h3>
        <input name="kodeKategori" value="<?php echo $kodeKategori; ?>" type="hidden" class="form-control">
    </div>
    <div class="form-group">
        <label>Nama kategori Barag:</label>
        <input name="namaKategori" type="text" class="form-control" placeholder="Masukan nama kategori barang" required>
    </div>

    <button type="submit" name="tambah_kategori_barang" id="btn-barang" class="btn btn-dark">Tambah</button>
</form>

