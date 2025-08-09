<?php
// Kode ini hampir identik dengan donasi/detail.php, hanya disesuaikan untuk wakaf
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger mt-4'>Halaman tidak ditemukan.</div>";
    return;
}
$idKampanye = intval($_GET['id']);
$stmt = mysqli_prepare($kon, "SELECT * FROM kampanye_sosial WHERE idKampanye = ? AND status = 'Aktif' AND tipe='Wakaf'");
mysqli_stmt_bind_param($stmt, "i", $idKampanye);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<div class='alert alert-danger mt-4'>Proyek wakaf tidak ditemukan atau sudah tidak aktif.</div>";
    return;
}

$persentase = ($data['target_dana'] > 0) ? ($data['dana_terkumpul'] / $data['target_dana']) * 100 : 0;
if ($persentase > 100) $persentase = 100;
$gambar_path = (!empty($data['gambar_kampanye'])) ? 'uploads/kampanye_wakaf/' . htmlspecialchars($data['gambar_kampanye']) : 'src/img/placeholder.jpg';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-7">
            <a href="index.php?page=wakaf" class="btn btn-sm btn-light mb-2"><i class="fas fa-arrow-left"></i> Kembali ke semua proyek</a>
            <h2 class="mb-3"><?php echo htmlspecialchars($data['judul']); ?></h2>
            <img src="<?php echo $gambar_path; ?>" class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($data['judul']); ?>">
            <div class="progress mb-4" style="height: 10px;">
                <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $persentase; ?>%;"></div>
            </div>
            <h4>Deskripsi Proyek</h4>
            <div class="text-justify"><?php echo nl2br(htmlspecialchars($data['deskripsi'])); ?></div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-info text-white"><h5>Formulir Wakaf Proyek</h5></div>
                <div class="card-body">
                    <form action="wakaf/aksi_wakaf.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="idKampanye" value="<?php echo $idKampanye; ?>">
                        <input type="hidden" name="jenis_wakaf" value="proyek">
                        <div class="form-group">
                            <label>Nominal Wakaf (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" min="10000" required>
                        </div>
                        <div class="alert alert-secondary small">
                            <strong>Informasi Pembayaran:</strong><br>
                            Silakan transfer ke rekening <b>Bank Wakaf Sejahtera: 112-233-4455</b> a.n. Yayasan Wakaf Produktif.
                        </div>
                        <div class="form-group">
                            <label>Unggah Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" class="form-control-file" required>
                        </div>
                        <button type="submit" name="submit_wakaf" class="btn btn-info btn-block">Konfirmasi Wakaf Saya</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>