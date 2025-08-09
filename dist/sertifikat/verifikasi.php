<?php
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk Admin.</div>";
    return;
}
?>

<h1 class="mt-4">Verifikasi Sertifikat Halal</h1>
<p class="mb-4">Daftar sertifikat dari penjual yang menunggu verifikasi atau sudah diverifikasi.</p>

<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sukses') {
        echo "<div class='alert alert-success'>Status sertifikat berhasil diperbarui.</div>";
    } elseif ($_GET['status'] == 'gagal') {
        echo "<div class='alert alert-danger'>Gagal memperbarui status.</div>";
    }
}
?>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-table mr-1"></i>Daftar Pengajuan Sertifikat</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Penjual</th>
                        <th>No. Sertifikat</th>
                        <th>Status</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $no = 1;
                    // Query untuk mengambil semua data sertifikat dan nama pengguna penjual
                    $sql = "SELECT s.*, p.username 
                            FROM sertifikat_halal s 
                            JOIN pengguna p ON s.idPenjual = p.idPengguna 
                            ORDER BY FIELD(s.status_verifikasi, 'Menunggu Verifikasi', 'Ditolak', 'Diverifikasi'), s.tanggal_upload DESC";
                    $result = mysqli_query($kon, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['nomor_sertifikat']); ?></td>
                        <td>
                            <?php
                            $status = $row['status_verifikasi'];
                            if ($status == 'Diverifikasi') { echo '<span class="badge badge-success">Diverifikasi</span>'; } 
                            elseif ($status == 'Ditolak') { echo '<span class="badge badge-danger">Ditolak</span>'; } 
                            else { echo '<span class="badge badge-warning">Menunggu Verifikasi</span>'; }
                            ?>
                        </td>
                        <td><?php echo date("d-m-Y H:i", strtotime($row['tanggal_upload'])); ?></td>
                        <td>
                            <a href="../uploads/sertifikat/<?php echo htmlspecialchars($row['file_sertifikat']); ?>" target="_blank" class="btn btn-info btn-sm mb-1" title="Lihat File"><i class="fas fa-eye"></i></a>
                            <?php if ($status == 'Menunggu Verifikasi'): ?>
                                <a href="sertifikat/aksi_verifikasi.php?id=<?php echo $row['idSertifikat']; ?>&aksi=setujui" class="btn btn-success btn-sm mb-1" onclick="return confirm('Anda yakin ingin menyetujui sertifikat ini?')" title="Setujui"><i class="fas fa-check"></i></a>
                                <button type="button" class="btn btn-danger btn-sm mb-1" data-toggle="modal" data-target="#tolakModal<?php echo $row['idSertifikat']; ?>" title="Tolak"><i class="fas fa-times"></i></button>
                            <?php else: ?>
                                <span class="badge badge-light">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <div class="modal fade" id="tolakModal<?php echo $row['idSertifikat']; ?>" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="sertifikat/aksi_verifikasi.php?id=<?php echo $row['idSertifikat']; ?>&aksi=tolak" method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tolak Verifikasi Sertifikat</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Anda akan menolak sertifikat dari <strong><?php echo htmlspecialchars($row['username']); ?></strong>.</p>
                                        <div class="form-group">
                                            <label for="catatan">Alasan Penolakan (Wajib diisi)</label>
                                            <textarea name="catatan" class="form-control" rows="3" required placeholder="Contoh: File tidak terbaca, nomor sertifikat tidak valid, dll."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" name="submit_tolak" class="btn btn-danger">Ya, Tolak Sertifikat</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script> $(document).ready(function() { $('#dataTable').DataTable(); }); </script>