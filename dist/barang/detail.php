<?php
session_start();
include '../../config/database.php';

class BarangDetailCarouselDenganThumbnail {
    protected $kon;

    public function __construct($kon) {
        $this->kon = $kon;
    }

    protected function getBarang($idBarang) {
        $idBarang = mysqli_real_escape_string($this->kon, $idBarang);
        $sql = "SELECT b.*, k.namaKategori 
                FROM barang b
                INNER JOIN kategoribarang k ON k.kodeKategori = b.kodeKategori
                WHERE b.idBarang = '$idBarang' LIMIT 1";
        $result = mysqli_query($this->kon, $sql);
        return mysqli_fetch_assoc($result);
    }

    protected function getGambarUtama($kodeBarang) {
        $kodeBarang = mysqli_real_escape_string($this->kon, $kodeBarang);
        $sql = "SELECT * FROM gambarutama WHERE kodeBarang = '$kodeBarang'";
        $result = mysqli_query($this->kon, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    protected function getVarianBarang($kodeBarang) {
        $kodeBarang = mysqli_real_escape_string($this->kon, $kodeBarang);
        $sql = "SELECT v.*, g.gambarvarian 
                FROM varianbarang v
                LEFT JOIN gambarvarian g ON v.idGambarVarian = g.idGambarVarian
                WHERE v.kodeBarang = '$kodeBarang'";
        $result = mysqli_query($this->kon, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    protected function getTotalTerjual($kodeBarang) {
        $kodeBarang = mysqli_real_escape_string($this->kon, $kodeBarang);
        $sql = "SELECT SUM(dt.jumlah) as total
                FROM detail_transaksi dt
                INNER JOIN transaksi t ON t.kodeTransaksi = dt.kodeTransaksi
                INNER JOIN varianbarang vb ON vb.idVarian = dt.idVarian
                WHERE vb.kodeBarang = '$kodeBarang' AND dt.status = 3";
        $result = mysqli_query($this->kon, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    protected function getReviewBarang($kodeBarang) {
        $kodeBarang = mysqli_real_escape_string($this->kon, $kodeBarang);
        $sql = "SELECT r.*, u.username AS nama
                FROM ulasan_produk r
                INNER JOIN pengguna u ON u.idPengguna = r.idPengguna
                WHERE r.kodeBarang = '$kodeBarang'
                ORDER BY r.tanggal_ulasan DESC";
        $result = mysqli_query($this->kon, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function tampilkanDetail($idBarang) {
        $barang = $this->getBarang($idBarang);
        if (!$barang) {
            echo "<div class='alert alert-danger'>Barang tidak ditemukan.</div>";
            return;
        }

        $totalTerjual = $this->getTotalTerjual($barang['kodeBarang']);
        $gambarUtamaList = $this->getGambarUtama($barang['kodeBarang']);
        $varians = $this->getVarianBarang($barang['kodeBarang']);

        echo "<h4>".htmlspecialchars($barang['namaBarang'])." 
              <small class='text-muted'>(".htmlspecialchars($barang['namaKategori']).")</small></h4>";
        echo "<p><strong>Total Terjual:</strong> {$totalTerjual} pcs</p>";

        $semuaGambar = [];
        $gambarVarianUnik = [];
        foreach ($varians as $varian) {
            if (!empty($varian['gambarvarian'])) {
                $gambarVarianUnik[$varian['gambarvarian']] = true;
            }
        }
        $jumlahGambarVarianUnik = count($gambarVarianUnik);

        if ($jumlahGambarVarianUnik > 1) {
            foreach ($gambarUtamaList as $gambar) {
                $semuaGambar[] = ['tipe' => 'utama', 'gambar' => $gambar['gambarUtama'], 'info' => $barang['deskripsi']];
            }
        }

        foreach ($varians as $varian) {
            if (!empty($varian['gambarvarian'])) {
                $semuaGambar[] = ['tipe' => 'varian', 'gambar' => $varian['gambarvarian'], 'info' => $varian, 'idVarian' => $varian['idVarian']];
            }
        }

        $varianSlideIndexMap = [];
        foreach ($semuaGambar as $index => $item) {
            if ($item['tipe'] === 'varian' && isset($item['idVarian'])) {
                $varianSlideIndexMap[$item['idVarian']] = $index;
            }
        }

        $typeVarianGroups = [];
        foreach ($varians as $v) {
            $type = $v['typeVarian'];
            if (!isset($typeVarianGroups[$type])) {
                $typeVarianGroups[$type] = [];
            }
            $typeVarianGroups[$type][] = $v;
        }

        $ukuranTetap = ['S', 'M', 'L', 'XL'];
        $buttonsPerTypeVarian = [];
        foreach ($typeVarianGroups as $type => $listVarian) {
            ob_start();
            echo "<div class='mb-3'><strong>".htmlspecialchars($type).":</strong><br>";
            $varianMap = [];
            foreach ($listVarian as $v) {
                $varianMap[$v['size']] = $v;
            }
            foreach ($ukuranTetap as $size) {
                if (isset($varianMap[$size])) {
                    $v = $varianMap[$size];
                    $disabled = ($v['stok'] <= 0 || empty($v['gambarvarian']));
                    $btnClass = $disabled ? 'btn-outline-secondary disabled' : 'btn-outline-primary';
                    $label = $disabled ? "$size (Stok Habis)" : $size;
                    $slideIndex = $varianSlideIndexMap[$v['idVarian']] ?? -1;
                    echo "<button class='btn $btnClass m-1 varian-btn' data-slide='{$slideIndex}' ".($disabled ? "disabled" : "").">$label</button>";
                } else {
                    echo "<button class='btn btn-outline-secondary m-1 disabled' disabled>$size (Stok Habis)</button>";
                }
            }
            echo "</div>";
            $buttonsPerTypeVarian[$type] = ob_get_clean();
        }

        echo '<div id="carouselDetailBarang" class="carousel slide position-relative" data-ride="carousel">';
        echo '<div class="carousel-inner">';

        foreach ($semuaGambar as $index => $item) {
            $active = $index === 0 ? 'active' : '';
            echo "<div class='carousel-item $active'>";
            echo "<div class='row'>";
            echo "<div class='col-md-6 text-center'>";
            echo "<img class='img-fluid' src='../dist/barang/gambar/".htmlspecialchars($item['gambar'])."' alt='Gambar ".($index+1)."'>";
            echo '<a class="carousel-control-prev" href="#carouselDetailBarang" role="button" data-slide="prev"><span class="carousel-control-prev-icon"></span></a>';
            echo '<a class="carousel-control-next" href="#carouselDetailBarang" role="button" data-slide="next"><span class="carousel-control-next-icon"></span></a>';
            echo "</div>";
            echo "<div class='col-md-6'>";

            if (count($varians) <= 1 && isset($varians[0])) {
                $varian = $varians[0];
                echo "<div class='mt-3'>";
                echo $buttonsPerTypeVarian[$varian['typeVarian']] ?? '';
                echo "<p><strong>Ukuran:</strong> <span id='info-value-{$index}'>".htmlspecialchars($varian['size'])."</span></p>";
                echo "<p><strong>Type Varian:</strong> <span id='info-type-{$index}'>".htmlspecialchars($varian['typeVarian'])."</span></p>";
                echo "<p><strong>Stok:</strong> <span id='info-stok-{$index}'>".htmlspecialchars($varian['stok'])."</span></p>";
                echo "<p><strong>Harga:</strong> Rp <span id='info-harga-{$index}'>" . number_format($varian['harga'], 0, ',', '.') . "</span></p>";

                // Tombol Chat Penjual
                echo "<p><a href='https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20bertanya%20tentang%20produk%20{$barang['namaBarang']}' target='_blank' class='btn btn-info btn-sm'><i class='fas fa-comments'></i> Chat Penjual</a></p>";

                if (isset($_SESSION['level']) && strtolower($_SESSION['level']) === 'pelanggan' && $varian['stok'] > 0) {
                    echo "<div>";
                        echo "<button type='button' class='btn btn-primary btn-sm tambah-keranjang-btn' data-idvarian='{$varian['idVarian']}' data-kodebarang='{$barang['kodeBarang']}'><i class='fas fa-cart-plus mr-1'></i> Tambah ke Keranjang</button>";
                        echo "<form action='keranjang/proses-checkout.php' method='POST' class='d-inline ml-2'>
                                <input type='hidden' name='pilih[]' value='{$varian['idVarian']}'>
                                <input type='hidden' name='jumlah[{$varian['idVarian']}]' value='1'>
                                <button type='submit' class='btn btn-success btn-sm'><i class='fas fa-shopping-bag mr-1'></i> Beli Sekarang</button>
                            </form>";
                    echo "</div>";
                } elseif ($varian['stok'] <= 0) {
                    echo "<div class='alert alert-warning p-1 text-center'>Stok Kosong</div>";
                }
                echo "</div>";

            } else if ($item['tipe'] === 'varian') {
                $varian = $item['info'];
                echo "<div class='mt-3'>";
                echo $buttonsPerTypeVarian[$varian['typeVarian']] ?? '';
                echo "<p><strong>Ukuran:</strong> <span>".htmlspecialchars($varian['size'])."</span></p>";
                echo "<p><strong>Stok:</strong> ".htmlspecialchars($varian['stok'])."</p>";
                echo "<p><strong>Harga:</strong> Rp" . number_format($varian['harga'], 0, ',', '.') . "</p>";
                echo "<p><a href='https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20bertanya%20tentang%20produk%20{$barang['namaBarang']}' target='_blank' class='btn btn-info btn-sm'><i class='fas fa-comments'></i> Chat Penjual</a></p>";

                if (isset($_SESSION['level']) && strtolower($_SESSION['level']) === 'pelanggan' && $varian['stok'] > 0) {
                    echo "<div>";
                        echo "<button type='button' class='btn btn-primary btn-sm tambah-keranjang-btn' data-idvarian='{$varian['idVarian']}' data-kodebarang='{$barang['kodeBarang']}'><i class='fas fa-cart-plus mr-1'></i> Tambah ke Keranjang</button>";
                        echo "<form action='keranjang/proses-checkout.php' method='POST' class='d-inline ml-2'>
                                <input type='hidden' name='pilih[]' value='{$varian['idVarian']}'>
                                <input type='hidden' name='jumlah[{$varian['idVarian']}]' value='1'>
                                <button type='submit' class='btn btn-success btn-sm'><i class='fas fa-shopping-bag mr-1'></i> Beli Sekarang</button>
                            </form>";
                    echo "</div>";
                } elseif ($varian['stok'] <= 0) {
                    echo "<div class='alert alert-warning p-1 text-center'>Stok Kosong</div>";
                }
                echo "</div>";
            }
            echo "</div></div></div>";
        }
        echo '</div></div>';

        // Review Section
        $reviews = $this->getReviewBarang($barang['kodeBarang']);
        echo "<div class='mt-4'><p><strong>Deskripsi:</strong></p><p>".nl2br(htmlspecialchars($barang['deskripsi']))."</p></div>";

        echo "<div class='mt-5'>";
        echo "<h5>Ulasan Pembeli</h5>";
        if (count($reviews) > 0) {
            foreach ($reviews as $rev) {
                $stars = str_repeat("⭐", intval($rev['rating']));
                echo "<div class='border p-2 mb-2'>";
                echo "<strong>".htmlspecialchars($rev['nama'])."</strong> <small class='text-muted'>(".htmlspecialchars($rev['tanggal']).")</small><br>";
                echo "<span class='text-warning'>{$stars}</span>";
                echo "<p>".htmlspecialchars($rev['komentar'])."</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-muted'>Belum ada ulasan untuk produk ini.</p>";
        }
        echo "</div>";
    }
}

$idBarang = $_GET['idBarang'] ?? $_POST['idBarang'] ?? null;
if (!$idBarang) {
    echo "<div class='alert alert-danger'>ID Barang tidak ditemukan.</div>";
    exit;
}

$handler = new BarangDetailCarouselDenganThumbnail($kon);
?>

<div class="container mt-4">
    <?php $handler->tampilkanDetail($idBarang); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.tambah-keranjang-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const idVarian = this.dataset.idvarian;
        const kodeBarang = this.dataset.kodebarang;

        fetch('keranjang/tambah-keranjang.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `idVarian=${idVarian}&kodeBarang=${kodeBarang}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                
                const badgeKeranjang = document.getElementById('keranjang-badge');
                if (badgeKeranjang) {
                    badgeKeranjang.innerText = data.jumlah_baru;
                    badgeKeranjang.style.display = data.jumlah_baru > 0 ? 'inline' : 'none';
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menambahkan ke keranjang.'
            });
        });
    });
});
</script>
