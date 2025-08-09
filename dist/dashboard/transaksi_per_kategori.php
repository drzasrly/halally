<?php
session_start();
?>
<canvas id="transaksi_berdasarkan_kategori" width="100%" height="60"></canvas>
<?php
    $tahun=date('Y');

    include '../../config/database.php';

    if ($_SESSION["level"]=='Pelanggan' or $_SESSION["level"]=='pelanggan'){

        $kodePelanggan=$_SESSION["kodePengguna"];
        $sql="select k.namaKategori,count(*) as total
        from detail_transaksi d 
        inner join transaksi pj on d.kodeTransaksi=pj.kodeTransaksi
        inner join barang p on p.kodeBarang=d.kodeBarang
        inner join kategoribarang k on k.idKategori=p.kodeKategori
        where YEAR(tglTransaksi)='$tahun' and pj.kodePelanggan='$kodePelanggan'
        group by k.namaKategori";
      }else {
        $sql="select k.namaKategori,count(*) as total
        from detail_transaksi d 
        inner join barang p on p.kodeBarang=d.kodeBarang
        inner join kategoribarang k on k.idKategori=p.kodeKategori
        where YEAR(tglTransaksi)='$tahun'
        group by k.namaKategori";
      }

    $hasil=mysqli_query($kon,$sql);
    while ($data = mysqli_fetch_array($hasil)) {
        $namaKategori[]=$data['namaKategori'];
        $total[] = $data['total'];

    }
?>


<script>
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';

    // Pie Chart
    var ctx = document.getElementById("transaksi_berdasarkan_kategori");
    var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels:  <?php echo json_encode($namaKategori); ?>,
      datasets: [{
        data:  <?php echo json_encode($total); ?>,
        backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745','#53ff1a','#ff9900','#7300e6','#75a3a3','#99994d','#ac3939','#66b3ff','#ac7339','#ff00ff'],
      }],
    },
    });
</script>
