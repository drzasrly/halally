<?php
// Cek akses admin
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk Admin.</div>";
    return;
}

// Query untuk statistik
$q_total = mysqli_query($kon, "SELECT SUM(jumlah) as total FROM transaksi_sosial WHERE status_pembayaran = 'Dikonfirmasi' AND tipe = 'Donasi'");
$total_donasi = mysqli_fetch_assoc($q_total)['total'];

$q_transaksi = mysqli_query($kon, "SELECT COUNT(idTransaksi) as total FROM transaksi_sosial WHERE tipe = 'Donasi'");
$total_transaksi = mysqli_fetch_assoc($q_transaksi)['total'];

$q_kampanye_aktif = mysqli_query($kon, "SELECT COUNT(idKampanye) as total FROM kampanye_sosial WHERE status = 'Aktif' AND tipe = 'Donasi'");
$kampanye_aktif = mysqli_fetch_assoc($q_kampanye_aktif)['total'];
?>
<h1 class="mt-4">Laporan Program Sosial</h1>

<div class="row mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <h5>Total Donasi Terkonfirmasi</h5>
                <h2>Rp <?php echo number_format($total_donasi ?? 0); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <h5>Jumlah Transaksi</h5>
                <h2><?php echo number_format($total_transaksi ?? 0); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card bg-warning text-dark mb-4">
            <div class="card-body">
                <h5>Kampanye Aktif</h5>
                <h2><?php echo number_format($kampanye_aktif ?? 0); ?></h2>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header">Detail Semua Transaksi</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Program</th>
                        <th>Donatur</th>
                        <th>Jumlah</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT ts.*, p.username, ks.judul 
                                FROM transaksi_sosial ts 
                                LEFT JOIN pengguna p ON ts.idPelanggan = p.idPengguna 
                                LEFT JOIN kampanye_sosial ks ON ts.idKampanye = ks.idKampanye 
                                WHERE ts.tipe='Donasi'
                                ORDER BY ts.tanggal_transaksi DESC";
                                                $result = mysqli_query($kon, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $bukti_path = '../uploads/bukti_pembayaran/' . htmlspecialchars($row['bukti_pembayaran']);
                            echo "<tr>";
                            echo "<td>" . $row['idTransaksi'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>Rp " . number_format($row['jumlah']) . "</td>";
                            echo "<td>" . date('d-m-Y H:i', strtotime($row['tanggal_transaksi'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status_pembayaran']) . "</td>";
                            echo "<td><a href='$bukti_path' target='_blank' class='btn btn-sm btn-info'>Lihat</a></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>$(document).ready(function() { $('#dataTable').DataTable(); });</script>