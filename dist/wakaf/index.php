<h1 class="mt-4">Program Wakaf</h1>
<p class="mb-4">Salurkan wakaf produktif untuk pahala yang terus mengalir atau ikut serta dalam proyek wakaf spesifik.</p>

<div class="card card-body mb-5 bg-light text-center">
    <h3>Salurkan Wakaf Uang Produktif</h3>
    <p>Wakaf Anda akan dikelola untuk program kebaikan berkelanjutan.</p>
    <form class="form-inline justify-content-center" action="wakaf/aksi_wakaf.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="jenis_wakaf" value="uang">
        <div class="form-group mx-sm-3 mb-2">
            <label for="jumlah_uang" class="sr-only">Jumlah</label>
            <input type="number" name="jumlah" class="form-control form-control-lg" id="jumlah_uang" placeholder="Rp 100.000" required min="10000">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <label for="bukti_wakaf_uang" class="sr-only">Bukti</label>
             <input type="file" name="bukti_pembayaran" class="form-control-file" id="bukti_wakaf_uang" required>
        </div>
        <button type="submit" name="submit_wakaf" class="btn btn-primary btn-lg mb-2">Tunaikan Wakaf Uang</button>
    </form>
    <small class="text-muted">Silakan transfer ke rekening yang tertera & unggah bukti pembayaran.</small>
</div>


<h3 class="mb-3">Ikut Serta dalam Proyek Wakaf Pilihan</h3>
<div class="row">
    <?php
        $sql = "SELECT * FROM kampanye_sosial WHERE status = 'Aktif' AND tipe='Wakaf' ORDER BY tanggal_mulai DESC";
        $result = mysqli_query($kon, $sql);
        if (mysqli_num_rows($result) == 0) {
            echo "<div class='col-12'><div class='alert alert-info'>Saat ini belum ada proyek wakaf spesifik yang berjalan.</div></div>";
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $persentase = ($row['target_dana'] > 0) ? ($row['dana_terkumpul'] / $row['target_dana']) * 100 : 0;
            if ($persentase > 100) $persentase = 100;
            $gambar_path = (!empty($row['gambar_kampanye'])) ? 'uploads/kampanye_wakaf/' . htmlspecialchars($row['gambar_kampanye']) : 'src/img/placeholder.jpg';
    ?>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <img src="<?php echo $gambar_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['judul']); ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
                <h5><a href="index.php?page=detail-wakaf&id=<?php echo $row['idKampanye']; ?>" class="text-dark"><?php echo htmlspecialchars($row['judul']); ?></a></h5>
                <p><span class="badge badge-info"><?php echo htmlspecialchars($row['tipe']); ?></span></p>
                <div class="progress mb-2" style="height: 10px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $persentase; ?>%;"></div>
                </div>
                <div class="d-flex justify-content-between small">
                    <span><b>Terkumpul</b><br>Rp <?php echo number_format($row['dana_terkumpul']); ?></span>
                    <span class="text-right"><b>Target</b><br>Rp <?php echo number_format($row['target_dana']); ?></span>
                </div>
            </div>
             <div class="card-footer bg-white border-0">
                <a href="index.php?page=detail-wakaf&id=<?php echo $row['idKampanye']; ?>" class="btn btn-info btn-block">Lihat & Ikut Wakaf</a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>