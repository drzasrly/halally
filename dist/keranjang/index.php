<?php
// session_start();
include '../config/database.php';
$idPengguna = $_SESSION['idPengguna'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_jumlah'])) {
    foreach ($_POST['jumlah'] as $idVarian => $jumlah) {
        $jumlah = max(1, intval($jumlah));
        mysqli_query($kon, "UPDATE keranjang SET jumlah=$jumlah WHERE idPengguna='$idPengguna' AND idVarian='$idVarian'");
    }
    // Redirect untuk menghindari re-submit form saat refresh
    echo "<script>window.location.href = 'index.php?page=keranjang';</script>";
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>$('title').text('Keranjang Belanja');</script>

<main>
<div class="container-fluid">
    <h2 class="mt-4">Keranjang Belanja</h2>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Keranjang</li>
    </ol>

    <form method="POST" action="keranjang/proses-checkout.php" id="form-checkout">
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="pilih-semua" checked></th>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Barang</th>
                                <th>Varian</th>
                                <th>Harga</th>
                                <th style="width: 120px;">Jumlah</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            $query = mysqli_query($kon, "SELECT k.idVarian, k.jumlah, b.namaBarang, v.typeVarian, v.size, v.harga, g.gambarvarian FROM keranjang k INNER JOIN varianbarang v ON k.idVarian = v.idVarian INNER JOIN barang b ON v.kodeBarang = b.kodeBarang LEFT JOIN gambarvarian g ON v.idGambarVarian = g.idGambarVarian WHERE k.idPengguna = '$idPengguna'");
                            if ($query && mysqli_num_rows($query) > 0):
                                while ($item = mysqli_fetch_assoc($query)):
                                    $no++;
                            ?>
                            <tr data-idvarian="<?= $item['idVarian'] ?>">
                                <td><input type="checkbox" class="pilih-item" name="pilih[]" value="<?= $item['idVarian'] ?>" checked></td>
                                <td><?= $no ?></td>
                                <td><img src="../dist/barang/gambar/<?= htmlspecialchars($item['gambarvarian']) ?>" alt="" style="width: 80px; height: 80px; object-fit: cover;"></td>
                                <td><?= htmlspecialchars($item['namaBarang']) ?></td>
                                <td><?= htmlspecialchars($item['typeVarian'] . ' / ' . ($item['size'] ?: '-')) ?></td>
                                <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary btn-sm minus-btn">-</button>
                                        <input type="number" name="jumlah[<?= $item['idVarian'] ?>]" value="<?= $item['jumlah'] ?>" class="form-control form-control-sm jumlah-input text-center" min="1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm plus-btn">+</button>
                                    </div>
                                </td>
                                <td class="total-per-item" data-harga="<?= $item['harga'] ?>">Rp<?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm hapus-item-btn" title="Hapus item"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="9" class="text-center">Keranjang Anda masih kosong.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" form="form-update-jumlah" name="update_jumlah" class="btn btn-info">Perbarui Jumlah</button>
            </div>
        </div>
        <div class="text-right">
            <h5>Total Dipilih: <span id="total-dipilih">Rp0</span></h5>
            <button type="submit" class="btn btn-success"><i class="fas fa-shopping-cart"></i> Checkout</button>
        </div>
    </form>
    <form method="POST" id="form-update-jumlah"></form>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const updateTotalDipilih = () => {
        let grandTotal = 0;
        document.querySelectorAll('.pilih-item:checked').forEach(cb => {
            const row = cb.closest('tr');
            const harga = parseFloat(row.querySelector('.total-per-item').dataset.harga) || 0;
            const jumlah = parseInt(row.querySelector('.jumlah-input').value) || 0;
            grandTotal += harga * jumlah;
        });
        document.getElementById('total-dipilih').textContent = 'Rp' + grandTotal.toLocaleString('id-ID');
    };

    const updateSubTotal = (input) => {
        const row = input.closest('tr');
        const harga = parseFloat(row.querySelector('.total-per-item').dataset.harga) || 0;
        const jumlah = parseInt(input.value) || 0;
        row.querySelector('.total-per-item').textContent = 'Rp' + (harga * jumlah).toLocaleString('id-ID');
    };

    const hapusItem = (idVarian, barisElement) => {
        fetch('keranjang/aksi-keranjang.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `aksi=hapus_item&idVarian=${idVarian}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                barisElement.remove();
                
                const badge = document.getElementById('keranjang-badge');
                if (badge) {
                    badge.innerText = data.jumlah_baru;
                    badge.style.display = data.jumlah_baru > 0 ? 'inline-block' : 'none';
                }
                updateTotalDipilih();
                Swal.fire('Dihapus!', 'Barang telah dihapus dari keranjang.', 'success');

                if (document.querySelector('tbody tr') === null) {
                    document.querySelector('tbody').innerHTML = '<tr><td colspan="9" class="text-center">Keranjang Anda masih kosong.</td></tr>';
                }
            } else {
                Swal.fire('Gagal!', data.message || 'Gagal menghapus barang.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
        });
    };

    document.querySelector('body').addEventListener('click', function(e) {
        const target = e.target;
        const parentGroup = target.closest('.input-group');
        const hapusBtn = target.closest('.hapus-item-btn');

        if (parentGroup) {
            const input = parentGroup.querySelector('.jumlah-input');
            let jumlah = parseInt(input.value);
            if (target.classList.contains('plus-btn')) jumlah++;
            if (target.classList.contains('minus-btn')) jumlah = Math.max(1, jumlah - 1);
            input.value = jumlah;
            updateSubTotal(input);
        }

        if (hapusBtn) {
            const idVarian = hapusBtn.closest('tr').dataset.idvarian;
            Swal.fire({
                title: 'Anda yakin?',
                text: "Barang ini akan dihapus dari keranjang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) hapusItem(idVarian, hapusBtn.closest('tr'));
            });
        }
        
        if (target.matches('.pilih-item, #pilih-semua, .plus-btn, .minus-btn')) {
            if(target.id === 'pilih-semua') document.querySelectorAll('.pilih-item').forEach(cb => cb.checked = target.checked);
            updateTotalDipilih();
        }
    });
    
    updateTotalDipilih();
});
</script>