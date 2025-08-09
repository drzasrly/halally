<?php
// Ambil jumlah dari parameter URL jika ada
$jumlah = isset($_GET['jumlah']) ? intval($_GET['jumlah']) : 0;
?>
<h1 class="mt-4">Konfirmasi Pembayaran Zakat</h1>
<p>Anda akan menunaikan Zakat Maal. Silakan lengkapi form di bawah ini.</p>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Formulir Pembayaran Zakat</div>
            <div class="card-body">
                <form action="zakat/aksi_zakat.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="jumlah">Nominal Zakat (Rp)</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" value="<?php echo $jumlah; ?>" required>
                    </div>
                    
                    <div class="alert alert-info small">
                        <strong>Informasi Pembayaran:</strong><br>
                        Silakan transfer ke rekening berikut:<br>
                        <b>Bank Syariah Kita: 987-654-3210</b><br>
                        a.n. Lembaga Amil Zakat Terpercaya
                    </div>

                    <div class="form-group">
                        <label for="bukti_pembayaran">Unggah Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" class="form-control-file" accept="image/*" required>
                    </div>
                    
                    <button type="submit" name="submit_zakat" class="btn btn-success btn-block">Saya Sudah Membayar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="alert alert-warning">
            <h5>Perhatian</h5>
            <p>Pastikan Anda telah melakukan transfer sebelum menekan tombol konfirmasi. Pengecekan akan dilakukan oleh admin dalam waktu 1x24 jam.</p>
        </div>
    </div>
</div>