<?php
// Cek akses admin, pastikan hanya admin yang bisa membuka halaman ini
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk Admin.</div>";
    return;
}

// Menentukan aksi yang akan dilakukan: 'tambah', 'edit', atau menampilkan daftar
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// =================================================================
// BAGIAN 1: TAMPILAN FORM UNTUK TAMBAH ATAU EDIT DATA
// =================================================================
if ($aksi == 'tambah' || $aksi == 'edit') {
    $judul_form = ($aksi == 'tambah') ? "Tambah Kampanye Donasi Baru" : "Edit Kampanye Donasi";
    // Data default untuk form tambah
    $data = [
        'judul' => '', 'deskripsi' => '',
        'target_dana' => '', 'status' => 'Aktif', 'gambar_kampanye' => ''
    ];

    // Jika aksinya adalah 'edit', ambil data kampanye dari database
    if ($aksi == 'edit' && $id > 0) {
        $stmt = mysqli_prepare($kon, "SELECT * FROM kampanye_sosial WHERE idKampanye = ? AND tipe = 'Donasi'");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        if (!$data) {
            echo "<div class='alert alert-danger'>Kampanye tidak ditemukan.</div>";
            return;
        }
    }
?>
    <h1 class="mt-4"><?php echo $judul_form; ?></h1>
    <a href="index.php?page=manajemen-donasi" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
    <div class="card">
        <div class="card-body">
            <form action="donasi/aksi_kampanye.php" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="aksi" value="<?php echo $aksi; ?>">
                <input type="hidden" name="tipe" value="Donasi"> <?php if ($aksi == 'edit'): ?>
                    <input type="hidden" name="idKampanye" value="<?php echo $id; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Judul Kampanye</label>
                    <input type="text" name="judul" class="form-control" value="<?php echo htmlspecialchars($data['judul']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="5" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>
                <div class="form-row">
                     <div class="form-group col-md-6">
                        <label>Target Dana (Rp)</label>
                        <input type="number" name="target_dana" class="form-control" value="<?php echo htmlspecialchars($data['target_dana']); ?>" required>
                    </div>
                     <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="Aktif" <?php echo ($data['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="Selesai" <?php echo ($data['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                            <option value="Ditutup" <?php echo ($data['status'] == 'Ditutup') ? 'selected' : ''; ?>>Ditutup</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Gambar Kampanye (Maks 2MB)</label>
                    <input type="file" name="gambar_kampanye" class="form-control-file" accept="image/*">
                    <?php if ($aksi == 'edit' && !empty($data['gambar_kampanye'])): ?>
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                        <img src="../uploads/kampanye_donasi/<?php echo htmlspecialchars($data['gambar_kampanye']); ?>" class="img-thumbnail mt-2" style="width: 150px;">
                    <?php endif; ?>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Simpan Kampanye</button>
            </form>
        </div>
    </div>
<?php
// =================================================================
// BAGIAN 2: TAMPILAN DAFTAR SEMUA KAMPANYE
// =================================================================
} else { 
?>
    <h1 class="mt-4">Manajemen Program Donasi</h1>
    <a href="index.php?page=manajemen-donasi&aksi=tambah" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Tambah Kampanye Baru</a>

    <div class="card">
        <div class="card-header"><i class="fas fa-table mr-1"></i>Daftar Semua Kampanye Donasi</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Target</th>
                            <th>Terkumpul</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Query difilter agar hanya menampilkan kampanye dengan tipe 'Donasi'
                            $sql = "SELECT * FROM kampanye_sosial WHERE tipe='Donasi' ORDER BY idKampanye DESC";
                            $result = mysqli_query($kon, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                
                                // Sel untuk menampilkan gambar
                                echo "<td>";
                                if (!empty($row['gambar_kampanye'])) {
                                    $image_path = '../uploads/kampanye_donasi/' . htmlspecialchars($row['gambar_kampanye']);
                                    echo "<img src='" . $image_path . "' class='img-thumbnail' style='width: 100px; height: auto;'>";
                                } else {
                                    echo "<em>Tidak ada gambar</em>";
                                }
                                echo "</td>";

                                echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
                                echo "<td>Rp " . number_format($row['target_dana']) . "</td>";
                                echo "<td>Rp " . number_format($row['dana_terkumpul']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                echo "<td>
                                        <a href='index.php?page=manajemen-donasi&aksi=edit&id=" . $row['idKampanye'] . "' class='btn btn-sm btn-warning mb-1' title='Edit'><i class='fas fa-edit'></i></a>
                                        <a href='donasi/aksi_kampanye.php?aksi=hapus&id=" . $row['idKampanye'] . "' class='btn btn-sm btn-danger' title='Hapus' onclick='return confirm(\"Yakin ingin menghapus kampanye ini?\")'><i class='fas fa-trash'></i></a>
                                      </td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>$(document).ready(function() { $('#dataTable').DataTable({"order": [[ 1, "asc" ]]}); });</script>
<?php
}
?>