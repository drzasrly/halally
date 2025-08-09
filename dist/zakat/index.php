<h1 class="mt-4">Zakat Maal</h1>
<p class="mb-4">Hitung dan tunaikan kewajiban Zakat Maal Anda dengan mudah dan tepat.</p>

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-calculator mr-1"></i>
                Kalkulator Zakat Maal (Harta)
            </div>
            <div class="card-body">
                <p class="small text-muted">Asumsi harga emas saat ini adalah <strong>Rp 1.400.000/gram</strong>, sehingga Nishab (batas wajib zakat) adalah 85 gram x Rp 1.400.000 = <strong>Rp 119.000.000</strong>.</p>
                
                <div class="form-group row">
                    <label class="col-sm-5 col-form-label">A. Harta yang Dihitung:</label>
                </div>
                <div class="form-group row">
                    <label for="harta_tabungan" class="col-sm-5 col-form-label">Uang Tunai, Tabungan, Deposito</label>
                    <div class="col-sm-7">
                        <input type="number" class="form-control zakat-input" id="harta_tabungan" value="0">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="harta_emas" class="col-sm-5 col-form-label">Emas, Perak, atau Logam Mulia Lainnya</label>
                    <div class="col-sm-7">
                        <input type="number" class="form-control zakat-input" id="harta_emas" value="0">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="harta_investasi" class="col-sm-5 col-form-label">Aset Investasi (Saham, Reksadana)</label>
                    <div class="col-sm-7">
                        <input type="number" class="form-control zakat-input" id="harta_investasi" value="0">
                    </div>
                </div>

                <hr>
                <div class="form-group row">
                    <label class="col-sm-5 col-form-label">B. Pengurang:</label>
                </div>
                <div class="form-group row">
                    <label for="harta_utang" class="col-sm-5 col-form-label">Utang Jatuh Tempo (yang harus dibayar tahun ini)</label>
                    <div class="col-sm-7">
                        <input type="number" class="form-control zakat-input" id="harta_utang" value="0">
                    </div>
                </div>
                <hr>

                <div class="alert alert-info" id="hasil-zakat">
                    <h5 class="alert-heading">Hasil Perhitungan:</h5>
                    <p>Total Harta: <strong id="total-harta">Rp 0</strong></p>
                    <p>Total Harta Kena Zakat: <strong id="harta-kena-zakat">Rp 0</strong></p>
                    <hr>
                    <p class="mb-0">Jumlah Zakat yang Harus Dibayar: <strong class="h4" id="jumlah-zakat">Rp 0</strong></p>
                </div>

                <div id="pesan-nishab" class="alert alert-warning" style="display: none;">
                    Total harta Anda belum mencapai nishab. Anda belum diwajibkan untuk membayar Zakat Maal saat ini. Namun, Anda tetap bisa ber-infaq.
                </div>

                <a href="#" id="tombol-bayar-zakat" class="btn btn-primary btn-block" style="display: none;">Tunaikan Zakat Sekarang</a>

            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <h4>Apa itu Zakat Maal?</h4>
        <p class="text-justify">Zakat Maal adalah zakat yang dikenakan atas segala jenis harta yang secara materi maupun substansi perolehannya tidak bertentangan dengan ketentuan agama. Zakat Maal wajib dikeluarkan jika harta tersebut telah mencapai batas minimum (nishab) dan telah dimiliki selama satu tahun (haul).</p>
        
        <h4>Niat Mengeluarkan Zakat</h4>
        <p class="text-muted">Berikut adalah lafadz niat untuk mengeluarkan zakat maal:</p>
        <div class="card bg-light">
            <div class="card-body text-center">
                <h5 lang="ar" dir="rtl">نَوَيْتُ أَنْ أُخْرِجَ زَكَاةَ مَالِى فَرْضًا لِلَّهِ تَعَالَى</h5>
                <p><i>"Nawaitu an ukhrija zakaata maali fardhan lillaahi ta'aala."</i></p>
                <p>Artinya: "Saya niat mengeluarkan zakat harta milikku, fardhu karena Allah Ta'ala."</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.zakat-input');
    const nishab = 119000000;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function hitungZakat() {
        let tabungan = parseFloat(document.getElementById('harta_tabungan').value) || 0;
        let emas = parseFloat(document.getElementById('harta_emas').value) || 0;
        let investasi = parseFloat(document.getElementById('harta_investasi').value) || 0;
        let utang = parseFloat(document.getElementById('harta_utang').value) || 0;

        let totalHarta = tabungan + emas + investasi;
        let hartaKenaZakat = totalHarta - utang;

        document.getElementById('total-harta').innerText = formatRupiah(totalHarta);
        document.getElementById('harta-kena-zakat').innerText = formatRupiah(hartaKenaZakat);

        let jumlahZakat = 0;
        if (hartaKenaZakat >= nishab) {
            jumlahZakat = 0.025 * hartaKenaZakat;
            document.getElementById('jumlah-zakat').innerText = formatRupiah(jumlahZakat);
            document.getElementById('hasil-zakat').style.display = 'block';
            document.getElementById('pesan-nishab').style.display = 'none';
            document.getElementById('tombol-bayar-zakat').style.display = 'block';
            document.getElementById('tombol-bayar-zakat').href = `index.php?page=bayar-zakat&jumlah=${Math.round(jumlahZakat)}`;
        } else {
            document.getElementById('jumlah-zakat').innerText = formatRupiah(0);
            document.getElementById('hasil-zakat').style.display = 'none';
            document.getElementById('pesan-nishab').style.display = 'block';
            document.getElementById('tombol-bayar-zakat').style.display = 'none';
        }
    }

    inputs.forEach(input => {
        input.addEventListener('keyup', hitungZakat);
        input.addEventListener('change', hitungZakat);
    });

    hitungZakat(); // Panggil saat halaman dimuat
});
</script>