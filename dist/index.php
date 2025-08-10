<!-- <?php
session_start();
if (!isset($_SESSION['kodePengguna'])) {
    header("Location: tampilanAwal.php");
    exit();
} else {
    include '../config/database.php';
    $kodePengguna = $_SESSION['kodePengguna'];
    $username = $_SESSION['username'];
    $hasil = mysqli_query($kon, "SELECT username FROM pengguna WHERE kodePengguna='$kodePengguna'");
    $data = mysqli_fetch_array($hasil);
    $username_db = $data['username'];
    
    if ($username != $username_db) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

$jumlah_keranjang = 0;
if (isset($_SESSION['level']) && ($_SESSION['level'] == 'Pelanggan' || $_SESSION['level'] == 'pelanggan')) {
    // Pastikan idPengguna ada di session sebelum digunakan
    if (isset($_SESSION["idPengguna"])){
        $idPengguna = $_SESSION["idPengguna"];
        $query_keranjang = mysqli_query($kon, "SELECT COUNT(idKeranjang) AS total FROM keranjang WHERE idPengguna=$idPengguna");
        $data_keranjang = mysqli_fetch_array($query_keranjang);
        $jumlah_keranjang = $data_keranjang['total'];
    }
}
// Ambil profil aplikasi
$hasil_aplikasi = mysqli_query($kon, "SELECT * FROM profil_aplikasi ORDER BY nama_aplikasi DESC LIMIT 1");
$data_aplikasi = mysqli_fetch_array($hasil_aplikasi);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title><?php echo htmlspecialchars($data_aplikasi['nama_aplikasi']);?></title>
        <link href="../src/templates/css/styles.css" rel="stylesheet" />
        <link href="../src/plugin/bootstrap/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
        <link href="../src/plugin/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="../src/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
        <script src="../src/js/jquery/jquery-3.5.1.min.js"></script>
        <script src="../src/plugin/chart/Chart.js"></script>
        <script src="../src/plugin/datatables/jquery.dataTables.min.js"></script>
        <script src="../src/plugin/datatables/dataTables.bootstrap4.min.js"></script>
    </head>
    
    <body class="<?php echo ($_SESSION['level'] == 'Pelanggan' || $_SESSION['level'] == 'pelanggan') ? '' : 'sb-nav-fixed'; ?>">
        <nav class="sb-topnav navbar navbar-expand navbar-dark" style="background-color:rgb(66, 138, 155);">
            <a class="navbar-brand pl-3" href="index.php?page=dashboard"><?php echo htmlspecialchars($data_aplikasi['nama_aplikasi']);?></a>
            
            <?php if ($_SESSION['level']!='Pelanggan' && $_SESSION['level']!='pelanggan'): ?>
                <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
            <?php endif; ?>

            <?php if (isset($_SESSION['level']) && ($_SESSION['level']=='Pelanggan' || $_SESSION['level']=='pelanggan')): ?>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="index.php?page=dashboard"><i class="fas fa-home mr-1"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="index.php?page=barang"><i class="fas fa-store mr-1"></i>Katalog Barang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="index.php?page=chat"><i class="fas fa-comments mr-1"></i>Chat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="index.php?page=keranjang">
                            <i class="fas fa-shopping-cart mr-1"></i>Keranjang
                            <?php if ($jumlah_keranjang > 0): ?>
                                <span id="keranjang-badge" class="badge badge-danger"><?php echo $jumlah_keranjang; ?></span>
                            <?php else: ?>
                                <span id="keranjang-badge" class="badge badge-danger" style="display: none;">0</span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="index.php?page=riwayat-kebaikan"><i class="fas fa-history mr-1"></i>Riwayat</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle mx-2" href="#" id="navbarDropdownKebaikan" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-star mr-1"></i>Program Kebaikan
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownKebaikan">
                            <a class="dropdown-item" href="index.php?page=donasi"><i class="fas fa-donate text-primary fa-fw mr-2"></i>Donasi</a>
                            <a class="dropdown-item" href="index.php?page=zakat"><i class="fas fa-hand-holding-heart text-success fa-fw mr-2"></i>Zakat</a>
                            <a class="dropdown-item" href="index.php?page=wakaf"><i class="fas fa-mosque text-info fa-fw mr-2"></i>Wakaf</a>
                            <a class="dropdown-item" href="index.php?page=ocr-halal/ocr.php"><i class="fas fa-camera text-warning fa-fw mr-2"></i>Scan Halal/Haram</a>
                        </div>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
            <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0"></form>
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="index.php?page=profil">Profil</a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal" >Logout</a>
                    </div>
                </li>
            </ul>
        </nav>

        <div id="layoutSidenav">
            <?php if (isset($_SESSION['level']) && $_SESSION['level']!='Pelanggan' && $_SESSION['level']!='pelanggan'): ?>
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">

                        <?php if (isset($_SESSION['level']) && ($_SESSION['level']=='Admin' || $_SESSION['level']=='admin')): ?>
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu Utama</div>
                            <a class="nav-link" href="index.php?page=dashboard">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>Dashboard
                            </a>
                            <a class="nav-link" href="index.php?page=daftar-transaksi">
                                <div class="sb-nav-link-icon"><i class="fas fa-list-ol"></i></div>Transaksi Produk
                            </a>
                             <a class="nav-link" href="index.php?page=konfirmasi-pembayaran">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-double"></i></div>Konfirmasi Pembayaran
                            </a>
                             <a class="nav-link" href="index.php?page=verifikasi-sertifikat">
                                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>Verifikasi Sertifikat
                            </a>

                            <div class="sb-sidenav-menu-heading">Program Sosial</div>
                             <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseDonasi">
                                <div class="sb-nav-link-icon"><i class="fas fa-donate"></i></div>Kelola Donasi<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseDonasi" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=manajemen-donasi">Kampanye</a>
                                    <a class="nav-link" href="index.php?page=laporan-donasi">Laporan</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseZakat">
                                <div class="sb-nav-link-icon"><i class="fas fa-hand-holding-heart"></i></div>Kelola Zakat<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseZakat" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=manajemen-zakat">Penyaluran Zakat</a>
                                    <a class="nav-link" href="index.php?page=laporan-zakat">Laporan</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWakaf">
                                <div class="sb-nav-link-icon"><i class="fas fa-mosque"></i></div>Kelola Wakaf<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseWakaf" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=manajemen-wakaf">Proyek Wakaf</a>
                                    <a class="nav-link" href="index.php?page=laporan-wakaf">Laporan</a>
                                </nav>
                            </div>

                            <div class="sb-sidenav-menu-heading">Data Master</div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePustaka">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>Barang<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePustaka" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=barang">Barang</a>
                                    <a class="nav-link" href="index.php?page=kategori">Kategori</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePengguna">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>Pengguna<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePengguna" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=pelanggan">Pelanggan</a>
                                    <a class="nav-link" href="index.php?page=penjual">Penjual</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>Laporan Produk<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLaporan" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=laporan-transaksi">Transaksi</a>
                                    <a class="nav-link" href="index.php?page=laporan-barang">Barang</a>
                                    <a class="nav-link" href="index.php?page=laporan-pelanggan">Pelanggan</a>
                                </nav>
                            </div>

                            <div class="sb-sidenav-menu-heading">Lainnya</div>
                             <a class="nav-link" href="index.php?page=aplikasi">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>Pengaturan Aplikasi
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['level']) && ($_SESSION['level']=='Penjual' || $_SESSION['level']=='penjual')): ?>
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu</div>
                            <a class="nav-link" href="index.php?page=dashboard">
                                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>Dashboard
                            </a>
                            <a class="nav-link" href="index.php?page=daftar-transaksi">
                                <div class="sb-nav-link-icon"><i class="fas fa-list-ol"></i></div>Transaksi
                            </a>
                            <a class="nav-link" href="index.php?page=chat">
                                <div class="sb-nav-link-icon"><i class="fas fa-comments"></i></div>Chat
                            </a>
                            <a class="nav-link" href="index.php?page=sertifikat-halal">
                                <div class="sb-nav-link-icon"><i class="fas fa-certificate"></i></div>Sertifikat Halal
                            </a>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePustaka">
                                <div class="sb-nav-link-icon"><i class="fas fa-box-open"></i></div>Barang<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePustaka" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="index.php?page=barang">Barang</a>
                                    <a class="nav-link" href="index.php?page=kategori">Kategori</a>
                                </nav>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>

                    <div class="sb-sidenav-footer">
                        <div class="small">Login Sebagai:</div>
                        <strong><?php echo htmlspecialchars($_SESSION["level"]); ?></strong> (<?php echo htmlspecialchars($_SESSION["username"]); ?>)
                    </div>
                </nav>
            </div>
            <?php endif; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
 
                        <?php
                        if (isset($_GET['page'])) {
                            $page = $_GET['page'];
                            switch ($page) {
                                // HALAMAN UTAMA
                                case 'dashboard': include "dashboard/index.php"; break;
                                case 'profil': include "profil/index.php"; break;
                                
                                // HALAMAN MASTER DATA & USER
                                case 'pelanggan': include "pelanggan/index.php"; break;
                                case 'penjual': include "penjual/index.php"; break;
                                case 'barang': include "barang/index.php"; break;
                                case 'kategori': include "barang/kategori/index.php"; break;
                                
                                // HALAMAN TRANSAKSI & KERANJANG
                                case 'input-transaksi': include "transaksi/input-transaksi.php"; break;
                                case 'daftar-transaksi': include "transaksi/index.php"; break;
                                case 'detail-transaksi': include "transaksi/detail-transaksi.php"; break;
                                case 'keranjang': include "keranjang/index.php"; break;
                                case 'booking': include "keranjang/booking.php"; break;
                                case 'riwayat-kebaikan': include "riwayat/index.php"; break;
                                
                                // HALAMAN ADMIN KHUSUS
                                case 'konfirmasi-pembayaran': include "konfirmasi/index.php"; break;
                                case 'verifikasi-sertifikat': include "sertifikat/verifikasi.php"; break;

                                // HALAMAN SERTIFIKAT (PENJUAL)
                                case 'sertifikat-halal': include "sertifikat/index.php"; break;

                                // HALAMAN DONASI (Terpisah)
                                case 'donasi': include "donasi/index.php"; break;
                                case 'detail-donasi': include "donasi/detail.php"; break;
                                case 'manajemen-donasi': include "donasi/admin.php"; break;
                                case 'laporan-donasi': include "donasi/laporan.php"; break;
                                
                                // HALAMAN ZAKAT (Terpisah)
                                case 'zakat': include "zakat/index.php"; break;
                                case 'bayar-zakat': include "zakat/bayar.php"; break;
                                case 'manajemen-zakat': include "zakat/admin.php"; break;
                                case 'laporan-zakat': include "zakat/laporan.php"; break;
                                
                                // HALAMAN WAKAF (Terpisah)
                                case 'wakaf': include "wakaf/index.php"; break;
                                case 'detail-wakaf': include "wakaf/detail.php"; break;
                                case 'manajemen-wakaf': include "wakaf/admin.php"; break;
                                case 'laporan-wakaf': include "wakaf/laporan.php"; break;
                                
                                // HALAMAN LAPORAN UMUM & PENGATURAN
                                case 'laporan-transaksi': include "laporan/transaksi/laporan-transaksi.php"; break;
                                case 'laporan-barang': include "laporan/barang/laporan-barang.php"; break;
                                case 'laporan-pelanggan': include "laporan/pelanggan/laporan-pelanggan.php"; break;
                                case 'aplikasi': include "aplikasi/index.php"; break;
                                case 'chat': include "chat/index.php"; break;
                                case 'ocr-halal/ocr.php': include "ocr-halal/ocr.php"; break;
                                case 'ocr-halal/ocr-proses.php': include "ocr-halal/ocr-proses.php"; break;
                                
                                // JIKA HALAMAN TIDAK DITEMUKAN
                                default: echo "<div class='mt-4'><center><h3>Maaf. Halaman tidak ditemukan!</h3></center></div>"; break;
                            }
                        } else {
                            include "dashboard/index.php";
                        }
                        ?>

                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; <?php echo htmlspecialchars($data_aplikasi['nama_aplikasi']);?> <?php echo date('Y');?></div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="../src/js/scripts.js"></script>
        <script src="../src/plugin/select2/select2.min.js"></script>
        <link href="../src/plugin/select2/select2.min.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="../src/js/jquery-ui/jquery-ui.js"></script>
        <link href="../src/js/jquery-ui/jquery-ui.css" rel="stylesheet" crossorigin="anonymous" />
        <script src="../src/plugin/bootstrap/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Keluar Aplikasi</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <a class="btn" href="logout.php" style="background-color: rgb(31, 124, 161); color: white;">Logout</a>
                </div>
            </div>
            </div>
        </div>
    </body>
</html> -->