<?php
// Kode ini adalah adaptasi dari donasi/admin.php, disesuaikan untuk wakaf
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk Admin.</div>"; return;
}

$q_wakaf_uang = mysqli_query($kon, "SELECT SUM(jumlah) as total FROM transaksi_sosial WHERE tipe='Wakaf' AND idKampanye IS NULL AND status_pembayaran='Dikonfirmasi'");
$total_wakaf_uang = mysqli_fetch_assoc($q_wakaf_uang)['total'] ?? 0;

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($aksi == 'tambah' || $aksi == 'edit') {
    $judul_form = ($aksi == 'tambah') ? "Tambah Proyek Wakaf Baru" : "Edit Proyek Wakaf";
    $data = ['judul' => '', 'deskripsi' => '','target_dana' => '','status' => 'Aktif','gambar_kampanye' => ''];
    if ($aksi == 'edit' && $id > 0) {
        $stmt = mysqli_prepare($kon, "SELECT * FROM kampanye_sosial WHERE idKampanye = ? AND tipe = 'Wakaf'");
        mysqli_stmt_bind_param($stmt, "i", $id); mysqli_stmt_execute($stmt);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }
?>
    <h1 class="mt-4"><?php echo $judul_form; ?></h1>
    <a href="index.php?page=manajemen-wakaf" class="btn btn-secondary mb-3">Kembali ke Daftar</a>
    <div class="card"><div class="card-body">
        <form action="wakaf/aksi_kampanye.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="aksi" value="<?php echo $aksi; ?>">
            <input type="hidden" name="tipe" value="Wakaf">
            <?php if ($aksi == 'edit'): ?><input type="hidden" name="idKampanye" value="<?php echo $id; ?>"><?php endif; ?>
            <div class="form-group"><label>Judul Proyek</label><input type="text" name="judul" class="form-control" value="<?php echo htmlspecialchars($data['judul']); ?>" required></div>
            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="5" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea></div>
            <div class="form-row">
                <div class="form-group col-md-6"><label>Target Dana (Rp)</label><input type="number" name="target_dana" class="form-control" value="<?php echo htmlspecialchars($data['target_dana']); ?>" required></div>
                <div class="form-group col-md-6"><label>Status</label><select name="status" class="form-control"><option value="Aktif" <?php echo ($data['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option><option value="Selesai" <?php echo ($data['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option><option value="Ditutup" <?php echo ($data['status'] == 'Ditutup') ? 'selected' : ''; ?>>Ditutup</option></select></div>
            </div>
            <div class="form-group"><label>Gambar Proyek</label><input type="file" name="gambar_kampanye" class="form-control-file"></div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan Proyek</button>
        </form>
    </div></div>
<?php
} else { 
?>
    <h1 class="mt-4">Manajemen Proyek Wakaf</h1>
    <div class="card bg-info text-white mb-4"><div class="card-body">Total Wakaf Uang (Umum) Terkumpul: <h5>Rp <?php echo number_format($total_wakaf_uang); ?></h5></div></div>
    <a href="index.php?page=manajemen-wakaf&aksi=tambah" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tambah Proyek Baru</a>
    <div class="card"><div class="card-header">Daftar Semua Proyek Wakaf</div><div class="card-body">
        <table class="table table-bordered" id="dataTable">
            <thead><tr><th>Gambar</th><th>Judul</th><th>Target</th><th>Terkumpul</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php
                $sql = "SELECT * FROM kampanye_sosial WHERE tipe='Wakaf' ORDER BY idKampanye DESC";
                $result = mysqli_query($kon, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    $image_path = 'uploads/kampanye_wakaf/' . htmlspecialchars($row['gambar_kampanye']);
                    echo "<tr>";
                    echo "<td><img src='$image_path' class='img-thumbnail' style='width: 100px;'></td>";
                    echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
                    echo "<td>Rp " . number_format($row['target_dana']) . "</td>";
                    echo "<td>Rp " . number_format($row['dana_terkumpul']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td><a href='index.php?page=manajemen-wakaf&aksi=edit&id=" . $row['idKampanye'] . "' class='btn btn-sm btn-warning'>Edit</a></td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>
    </div></div>
    <script>$(document).ready(function() { $('#dataTable').DataTable(); });</script>
<?php
}
?>