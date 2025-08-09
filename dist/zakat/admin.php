<?php
// Cek akses admin
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    echo "<div class='alert alert-danger'>Halaman ini hanya untuk Admin.</div>";
    return;
}
?>
<h1 class="mt-4">Manajemen Dana Zakat</h1>
<p>Halaman untuk mencatat penyaluran dana zakat yang telah terkumpul.</p>

<div class="card mb-4">
    <div class="card-header">Form Penyaluran Dana Zakat</div>
    <div class="card-body">
        <form action="zakat/aksi_penyaluran.php" method="POST">
            <input type="hidden" name="tipe_dana" value="Zakat">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Tanggal Penyaluran</label>
                    <input type="date" name="tanggal_penyaluran" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Jumlah (Rp)</label>
                    <input type="number" name="jumlah" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Disalurkan Kepada (Asnaf)</label>
                    <select name="disalurkan_kepada" class="form-control" required>
                        <option value="Fakir">Fakir</option>
                        <option value="Miskin">Miskin</option>
                        <option value="Amil">Amil</option>
                        <option value="Mu'allaf">Mu'allaf</option>
                        <option value="Riqab (Memerdekakan Budak)">Riqab</option>
                        <option value="Gharim (Orang Berhutang)">Gharim</option>
                        <option value="Fi Sabilillah">Fi Sabilillah</option>
                        <option value="Ibnu Sabil (Musafir)">Ibnu Sabil</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" name="submit_penyaluran" class="btn btn-primary">Catat Penyaluran</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Riwayat Penyaluran Dana Zakat</div>
    <div class="card-body">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th>Golongan (Asnaf)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $sql = "SELECT * FROM penyaluran_dana WHERE tipe_dana='Zakat' ORDER BY tanggal_penyaluran DESC";
                $result = mysqli_query($kon, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . date('d M Y', strtotime($row['tanggal_penyaluran'])) . "</td>";
                    echo "<td>Rp " . number_format($row['jumlah']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['disalurkan_kepada']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['keterangan']) . "</td>";
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>$(document).ready(function() { $('#dataTable').DataTable(); });</script>