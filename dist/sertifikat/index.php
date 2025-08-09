<?php
// Pastikan sesi sudah dimulai di file index.php utama
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Penjual' && $_SESSION['level'] != 'penjual')) {
    // Jika bukan penjual, jangan tampilkan apa-apa atau redirect
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk Penjual.</div>";
    return;
}

// Ambil id penjual dari sesi
$idPenjual = $_SESSION['idPengguna'];

// Ambil data sertifikat terakhir yang diunggah oleh penjual
$query = mysqli_prepare($kon, "SELECT * FROM sertifikat_halal WHERE idPenjual = ? ORDER BY tanggal_upload DESC LIMIT 1");
mysqli_stmt_bind_param($query, "i", $idPenjual);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

?>

<h1 class="mt-4">Sertifikat Halal Saya</h1>
<p class="mb-4">Di sini Anda dapat mengunggah dan melihat status verifikasi sertifikat halal untuk toko Anda.</p>

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sukses') {
        echo "<div class='alert alert-success'>Sertifikat berhasil diunggah dan sedang menunggu verifikasi.</div>";
    } elseif ($_GET['status'] == 'gagal') {
        echo "<div class='alert alert-danger'>Terjadi kesalahan: " . htmlspecialchars($_GET['pesan']) . "</div>";
    }
}
?>


<?php if ($data) : ?>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-certificate mr-1"></i>
            Status Sertifikat Anda Saat Ini
        </div>
        <div class="card-body">
            <p><strong>Nomor Sertifikat:</strong> <?php echo htmlspecialchars($data['nomor_sertifikat']); ?></p>
            <p><strong>Diterbitkan oleh:</strong> <?php echo htmlspecialchars($data['penerbit']); ?></p>
            <p><strong>Berlaku Hingga:</strong> <?php echo date("d F Y", strtotime($data['tanggal_berlaku'])); ?></p>
            <p>
                <strong>Status Verifikasi:</strong>
                <?php
                $status = $data['status_verifikasi'];
                if ($status == 'Diverifikasi') {
                    echo '<span class="badge badge-success">✅ Diverifikasi</span>';
                } elseif ($status == 'Ditolak') {
                    echo '<span class="badge badge-danger">❌ Ditolak</span>';
                } else {
                    echo '<span class="badge badge-warning">⏳ Menunggu Verifikasi</span>';
                }
                ?>
            </p>
            <?php if ($status == 'Ditolak' && !empty($data['catatan_admin'])) : ?>
                <div class="alert alert-warning">
                    <strong>Catatan dari Admin:</strong> <?php echo htmlspecialchars($data['catatan_admin']); ?>
                </div>
            <?php endif; ?>
            <a href="../uploads/sertifikat/<?php echo htmlspecialchars($data['file_sertifikat']); ?>" target="_blank" class="btn btn-info btn-sm">Lihat File Sertifikat</a>
        </div>
    </div>
<?php else : ?>
    <div class="alert alert-info">Anda belum pernah mengunggah sertifikat halal. Silakan unggah melalui form di bawah ini.</div>
<?php endif; ?>


<div class="card">
    <div class="card-header">
        <i class="fas fa-upload mr-1"></i>
        Unggah Sertifikat Baru
    </div>
    <div class="card-body">
        <form action="sertifikat/aksi_upload.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nomor_sertifikat">Nomor Sertifikat</label>
                <input type="text" class="form-control" id="nomor_sertifikat" name="nomor_sertifikat" placeholder="Masukkan nomor sertifikat" required>
            </div>
            <div class="form-group">
                <label for="penerbit">Lembaga Penerbit</label>
                <input type="text" class="form-control" id="penerbit" name="penerbit" value="LPPOM MUI" required>
            </div>
            <div class="form-group">
                <label for="tanggal_berlaku">Tanggal Berakhir Sertifikat</label>
                <input type="date" class="form-control" id="tanggal_berlaku" name="tanggal_berlaku" required>
            </div>
            <div class="form-group">
                <label for="file_sertifikat">File Sertifikat (Format: PDF, JPG, PNG. Maks: 2MB)</label>
                <input type="file" class="form-control-file" id="file_sertifikat" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Kirim untuk Verifikasi</button>
        </form>
    </div>
</div>