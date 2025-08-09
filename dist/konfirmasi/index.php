<?php
// Pastikan hanya Admin yang bisa akses
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    echo "<div class='alert alert-danger mt-4'>Akses ditolak.</div>";
    return;
}
?>
<h1 class="mt-4">Konfirmasi Pembayaran</h1>
<p>Daftar semua transaksi sosial (Donasi, Zakat, Wakaf) yang menunggu konfirmasi.</p>

<?php
// Tampilkan pesan sukses/gagal jika ada dari proses aksi
if (isset($_GET['status'])) {
    $pesan = htmlspecialchars($_GET['pesan'] ?? 'Aksi berhasil.');
    if ($_GET['status'] == 'sukses') {
        echo "<div class='alert alert-success'>Sukses! $pesan</div>";
    } elseif ($_GET['status'] == 'gagal') {
        echo "<div class='alert alert-danger'>Gagal! $pesan</div>";
    }
}
?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="konfirmasiTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Pelanggan</th>
                        <th>Program</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $sql = "SELECT ts.*, p.username, ks.judul 
                            FROM transaksi_sosial ts 
                            JOIN pengguna p ON ts.idPelanggan = p.idPengguna
                            LEFT JOIN kampanye_sosial ks ON ts.idKampanye = ks.idKampanye
                            WHERE ts.status_pembayaran = 'Menunggu Konfirmasi'
                            ORDER BY ts.tanggal_transaksi ASC";
                    $result = mysqli_query($kon, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo date('d M Y H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                        <td><span class="badge badge-info"><?php echo $row['tipe']; ?></span></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['judul'] ?? 'Pembayaran Langsung'); ?></td>
                        <td>Rp <?php echo number_format($row['jumlah']); ?></td>
                        <td>
                            <a href="../uploads/bukti_pembayaran/<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>" target="_blank" class="btn btn-sm btn-light">Lihat Bukti</a>
                        </td>
                        <td>
                            <a href="konfirmasi/aksi.php?id=<?php echo $row['idTransaksi']; ?>&aksi=konfirmasi" class="btn btn-sm btn-success" onclick="return confirm('Anda yakin ingin mengonfirmasi pembayaran ini?')">Konfirmasi</a>
                            <a href="konfirmasi/aksi.php?id=<?php echo $row['idTransaksi']; ?>&aksi=tolak" class="btn btn-sm btn-danger mt-1" onclick="return confirm('Anda yakin ingin menolak pembayaran ini?')">Tolak</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#konfirmasiTable').DataTable({
            "order": [[ 0, "asc" ]] // Urutkan berdasarkan tanggal terlama
        });
    });
</script>