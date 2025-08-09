<?php
// Pastikan hanya pelanggan yang bisa akses
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Pelanggan' && $_SESSION['level'] != 'pelanggan')) {
    echo "<div class='alert alert-danger mt-4'>Halaman ini khusus untuk pelanggan.</div>";
    return;
}

$idPelanggan = $_SESSION['idPengguna'];
?>

<h1 class="mt-4">Riwayat Transaksi</h1>
<p>Lihat semua riwayat belanja produk dan kontribusi sosial Anda (Donasi, Zakat, Wakaf).</p>

<div class="mb-3">
    <button class="btn btn-primary filter-btn active" data-filter="semua">Semua</button>
    <button class="btn btn-outline-primary filter-btn" data-filter="Donasi">Donasi</button>
    <button class="btn btn-outline-success filter-btn" data-filter="Zakat">Zakat</button>
    <button class="btn btn-outline-info filter-btn" data-filter="Wakaf">Wakaf</button>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-history mr-1"></i>Riwayat Kontribusi Sosial</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Keterangan Program</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody id="riwayat-table-body">
                    <?php
                        $sql = "SELECT ts.*, ks.judul as judul_kampanye 
                                FROM transaksi_sosial ts 
                                LEFT JOIN kampanye_sosial ks ON ts.idKampanye = ks.idKampanye 
                                WHERE ts.idPelanggan = ? 
                                ORDER BY ts.tanggal_transaksi DESC";
                        $stmt = mysqli_prepare($kon, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $idPelanggan);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        if (mysqli_num_rows($result) == 0) {
                            echo "<tr><td colspan='6' class='text-center'>Anda belum memiliki riwayat kontribusi sosial.</td></tr>";
                        }

                        while ($row = mysqli_fetch_assoc($result)) {
                            $keterangan = '';
                            if ($row['tipe'] == 'Zakat') {
                                $keterangan = 'Pembayaran Zakat Maal';
                            } elseif ($row['tipe'] == 'Wakaf' && empty($row['idKampanye'])) {
                                $keterangan = 'Wakaf Uang Produktif';
                            } else {
                                $keterangan = htmlspecialchars($row['judul_kampanye']);
                            }
                    ?>
                    <tr class="riwayat-item" data-tipe="<?php echo $row['tipe']; ?>">
                        <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                        <td>
                            <?php
                                $tipe = $row['tipe'];
                                $badge_class = 'badge-secondary';
                                if ($tipe == 'Donasi') $badge_class = 'badge-primary';
                                if ($tipe == 'Zakat') $badge_class = 'badge-success';
                                if ($tipe == 'Wakaf') $badge_class = 'badge-info';
                                echo "<span class='badge " . $badge_class . "'>" . $tipe . "</span>";
                            ?>
                        </td>
                        <td><?php echo $keterangan; ?></td>
                        <td>Rp <?php echo number_format($row['jumlah']); ?></td>
                        <td><?php echo htmlspecialchars($row['status_pembayaran']); ?></td>
                        <td>
                            <?php if (!empty($row['bukti_pembayaran'])): ?>
                                <a href="../uploads/bukti_pembayaran/<?php echo htmlspecialchars($row['bukti_pembayaran']); ?>" target="_blank" class="btn btn-sm btn-light">Lihat</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const tableRows = document.querySelectorAll('.riwayat-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Atur style tombol
            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-primary');

            const filterValue = this.getAttribute('data-filter');

            // Logika filter tabel
            tableRows.forEach(row => {
                if (filterValue === 'semua' || row.getAttribute('data-tipe') === filterValue) {
                    row.style.display = ''; // Tampilkan baris
                } else {
                    row.style.display = 'none'; // Sembunyikan baris
                }
            });
        });
    });
});
</script>