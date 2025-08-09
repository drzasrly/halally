<?php
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    return;
}
?>
<h1 class="mt-4">Laporan Dana Wakaf</h1>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-table mr-1"></i>Laporan Transaksi Wakaf Proyek Spesifik</div>
    <div class="card-body">
        <table class="table table-bordered" id="wakafProyekTable">
            <thead><tr><th>Tanggal</th><th>Donatur</th><th>Proyek</th><th>Jumlah</th><th>Status</th></tr></thead>
            <tbody>
            <?php
                $sql_proyek = "SELECT ts.*, p.username, ks.judul FROM transaksi_sosial ts JOIN pengguna p ON ts.idPelanggan = p.idPengguna JOIN kampanye_sosial ks ON ts.idKampanye = ks.idKampanye WHERE ts.tipe='Wakaf' AND ts.idKampanye IS NOT NULL ORDER BY ts.tanggal_transaksi DESC";
                $res_proyek = mysqli_query($kon, $sql_proyek);
                while ($row = mysqli_fetch_assoc($res_proyek)) {
                    echo "<tr>";
                    echo "<td>" . date('d M Y', strtotime($row['tanggal_transaksi'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['judul']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status_pembayaran']) . "</td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-table mr-1"></i>Laporan Transaksi Wakaf Uang (Umum)</div>
    <div class="card-body">
        <table class="table table-bordered" id="wakafUangTable">
             <thead><tr><th>Tanggal</th><th>Donatur</th><th>Jumlah</th><th>Status</th></tr></thead>
             <tbody>
             <?php
                $sql_uang = "SELECT ts.*, p.username FROM transaksi_sosial ts JOIN pengguna p ON ts.idPelanggan = p.idPengguna WHERE ts.tipe='Wakaf' AND ts.idKampanye IS NULL ORDER BY ts.tanggal_transaksi DESC";
                $res_uang = mysqli_query($kon, $sql_uang);
                while ($row = mysqli_fetch_assoc($res_uang)) {
                    echo "<tr>";
                    echo "<td>" . date('d M Y', strtotime($row['tanggal_transaksi'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status_pembayaran']) . "</td>";
                    echo "</tr>";
                }
            ?>
             </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#wakafProyekTable').DataTable({"order": [[ 0, "desc" ]]});
        $('#wakafUangTable').DataTable({"order": [[ 0, "desc" ]]});
    });
</script>