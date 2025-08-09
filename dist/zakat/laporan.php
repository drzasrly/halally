<?php
// 1. Keamanan: Pastikan hanya Admin yang dapat mengakses halaman ini.
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    echo "<div class='alert alert-danger mt-4'>Akses ditolak. Halaman ini hanya untuk Admin.</div>";
    return;
}

// 2. Kalkulasi Data untuk Ringkasan (Dashboard)
// Menghitung total penerimaan zakat yang sudah dikonfirmasi
$query_penerimaan = mysqli_query($kon, "SELECT SUM(jumlah) as total FROM transaksi_sosial WHERE tipe='Zakat' AND status_pembayaran='Dikonfirmasi'");
$total_penerimaan = mysqli_fetch_assoc($query_penerimaan)['total'] ?? 0;

// Menghitung total dana zakat yang sudah disalurkan
$query_penyaluran = mysqli_query($kon, "SELECT SUM(jumlah) as total FROM penyaluran_dana WHERE tipe_dana='Zakat'");
$total_penyaluran = mysqli_fetch_assoc($query_penyaluran)['total'] ?? 0;

// Menghitung saldo akhir
$saldo_akhir = $total_penerimaan - $total_penyaluran;
?>

<h1 class="mt-4">Laporan Dana Zakat</h1>
<p>Laporan rekapitulasi penerimaan dan penyaluran dana Zakat.</p>

<div class="row">
    <div class="col-xl-4 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small">Total Penerimaan</div>
                        <div class="h5">Rp <?php echo number_format($total_penerimaan, 0, ',', '.'); ?></div>
                    </div>
                    <div class="h1"><i class="fas fa-arrow-down"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small">Total Penyaluran</div>
                        <div class="h5">Rp <?php echo number_format($total_penyaluran, 0, ',', '.'); ?></div>
                    </div>
                    <div class="h1"><i class="fas fa-arrow-up"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="small">Saldo Akhir Dana Zakat</div>
                        <div class="h5">Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?></div>
                    </div>
                    <div class="h1"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table mr-1"></i>
        Laporan Detail Penerimaan Zakat
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="penerimaanTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Donatur (Muzakki)</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti Bayar</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Query untuk mengambil detail transaksi penerimaan
                    $sql_penerimaan = "SELECT ts.*, p.username 
                                       FROM transaksi_sosial ts 
                                       JOIN pengguna p ON ts.idPelanggan = p.idPengguna 
                                       WHERE ts.tipe='Zakat' 
                                       ORDER BY ts.tanggal_transaksi DESC";
                    $result_penerimaan = mysqli_query($kon, $sql_penerimaan);
                    while ($row = mysqli_fetch_assoc($result_penerimaan)) {
                ?>
                    <tr>
                        <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="badge badge-<?php echo ($row['status_pembayaran'] == 'Dikonfirmasi') ? 'success' : 'warning'; ?>">
                                <?php echo htmlspecialchars($row['status_pembayaran']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($row['bukti_pembayaran'])): ?>
                                <a href="../uploads/bukti_pembayaran/<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table mr-1"></i>
        Laporan Detail Penyaluran Dana Zakat
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="penyaluranTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tanggal Penyaluran</th>
                        <th>Jumlah</th>
                        <th>Disalurkan Kepada (Asnaf)</th>
                        <th>Keterangan</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    // Query untuk mengambil detail penyaluran dana
                    $sql_penyaluran = "SELECT pd.*, p.username as pencatat 
                                     FROM penyaluran_dana pd 
                                     LEFT JOIN pengguna p ON pd.dicatat_oleh = p.idPengguna 
                                     WHERE pd.tipe_dana='Zakat' 
                                     ORDER BY pd.tanggal_penyaluran DESC";
                    $result_penyaluran = mysqli_query($kon, $sql_penyaluran);
                    while ($row = mysqli_fetch_assoc($result_penyaluran)) {
                ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_penyaluran'])); ?></td>
                        <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($row['disalurkan_kepada']); ?></td>
                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                        <td><?php echo htmlspecialchars($row['pencatat'] ?? 'N/A'); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#penerimaanTable').DataTable({
            "order": [[ 0, "desc" ]] // Urutkan berdasarkan tanggal (kolom pertama) secara menurun
        });
        $('#penyaluranTable').DataTable({
            "order": [[ 0, "desc" ]] // Urutkan berdasarkan tanggal (kolom pertama) secara menurun
        });
    });
</script>