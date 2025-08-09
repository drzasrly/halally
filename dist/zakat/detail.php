<?php
// Pastikan parameter ID ada dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger mt-4'>Halaman tidak ditemukan.</div>";
    return;
}

$idKampanye = intval($_GET['id']);
$stmt = mysqli_prepare($kon, "SELECT * FROM kampanye_sosial WHERE idKampanye = ? AND status = 'Aktif'");
mysqli_stmt_bind_param($stmt, "i", $idKampanye);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<div class='alert alert-danger mt-4'>Program donasi tidak ditemukan atau sudah tidak aktif.</div>";
    return;
}

// Hitung Progres
$persentase = 0;
if ($data['target_dana'] > 0) {
    $persentase = ($data['dana_terkumpul'] / $data['target_dana']) * 100;
}
if ($persentase > 100) $persentase = 100;

$gambar_path = (!empty($data['gambar_kampanye'])) ? '../uploads/kampanye/' . htmlspecialchars($data['gambar_kampanye']) : '../src/img/placeholder.jpg';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-7">
            <h2 class="mb-3"><?php echo htmlspecialchars($data['judul']); ?></h2>
            <img src="<?php echo $gambar_path; ?>" class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($data['judul']); ?>">
            
            <div class="d-flex justify-content-between font-weight-bold">
                <span>Terkumpul: Rp <?php echo number_format($data['dana_terkumpul'], 0, ',', '.'); ?></span>
                <span>Target: Rp <?php echo number_format($data['target_dana'], 0, ',', '.'); ?></span>
            </div>
            <div class="progress mb-4" style="height: 10px;">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $persentase; ?>%;" aria-valuenow="<?php echo $persentase; ?>"></div>
            </div>

            <h4>Deskripsi Program</h4>
            <div class="text-justify">
                <?php echo nl2br(htmlspecialchars($data['deskripsi'])); ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Formulir Donasi</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal'): ?>
                        <div class="alert alert-danger">Gagal memproses donasi: <?php echo htmlspecialchars($_GET['pesan']); ?></div>
                    <?php endif; ?>

                    <form action="donasi/aksi_donasi.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="idKampanye" value="<?php echo $idKampanye; ?>">
                        
                        <div class="form-group">
                            <label for="jumlah">Masukkan Nominal Donasi (Rp)</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" min="10000" step="1000" placeholder="Contoh: 50000" required>
                        </div>
                        
                        <div class="alert alert-info small">
                            <strong>Informasi Pembayaran:</strong><br>
                            Silakan transfer ke rekening berikut:<br>
                            <b>Bank ABC: 123-456-7890</b><br>
                            a.n. Yayasan Kebaikan Bersama
                        </div>

                        <div class="form-group">
                            <label for="bukti_pembayaran">Unggah Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control-file" accept="image/*" required>
                            <small class="form-text text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                        </div>
                        
                        <button type="submit" name="submit_donasi" class="btn btn-success btn-block">Konfirmasi Donasi Saya</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>