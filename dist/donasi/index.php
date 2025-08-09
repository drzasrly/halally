<h1 class="mt-4">Program Kebaikan</h1>
<p class="mb-4">Mari berbagi untuk sesama melalui program donasi, zakat, dan wakaf yang sedang berjalan.</p>

<div class="row">
    <?php
        // Ambil semua kampanye yang statusnya aktif dan tipenya Donasi
        $sql = "SELECT * FROM kampanye_sosial WHERE status = 'Aktif' AND tipe='Donasi' ORDER BY tanggal_mulai DESC";
        $result = mysqli_query($kon, $sql);

        if (mysqli_num_rows($result) == 0) {
            echo "<div class='col-12'><div class='alert alert-info'>Saat ini belum ada program donasi yang sedang berjalan.</div></div>";
        }

        while ($row = mysqli_fetch_assoc($result)) {
            // Hitung persentase progres
            $persentase = 0;
            if ($row['target_dana'] > 0) {
                $persentase = ($row['dana_terkumpul'] / $row['target_dana']) * 100;
            }
            // Batasi persentase maksimal 100%
            if ($persentase > 100) {
                $persentase = 100;
            }

            // =====================================================================
            // BAGIAN YANG DIPERBAIKI: Path ke folder uploads diperbaiki (../ dihapus)
            // =====================================================================
            $gambar_path = (!empty($row['gambar_kampanye'])) ? 'uploads/kampanye_donasi/' . htmlspecialchars($row['gambar_kampanye']) : 'src/img/placeholder.jpg';
    ?>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <img src="<?php echo $gambar_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['judul']); ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
                <h5><a href="index.php?page=detail-donasi&id=<?php echo $row['idKampanye']; ?>" class="text-dark"><?php echo htmlspecialchars($row['judul']); ?></a></h5>
                
                <p><span class="badge badge-primary"><?php echo htmlspecialchars($row['tipe']); ?></span></p>
                
                <div class="progress mb-2" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $persentase; ?>%;" aria-valuenow="<?php echo $persentase; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="d-flex justify-content-between small">
                    <span><b>Terkumpul</b><br>Rp <?php echo number_format($row['dana_terkumpul'], 0, ',', '.'); ?></span>
                    <span class="text-right"><b>Target</b><br>Rp <?php echo number_format($row['target_dana'], 0, ',', '.'); ?></span>
                </div>
            </div>
             <div class="card-footer bg-white border-0">
                <a href="index.php?page=detail-donasi&id=<?php echo $row['idKampanye']; ?>" class="btn btn-success btn-block">Donasi Sekarang</a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>