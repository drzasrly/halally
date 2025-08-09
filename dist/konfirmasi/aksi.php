<?php
session_start();
include '../../config/database.php';

// Keamanan: Hanya admin yang bisa eksekusi & pastikan parameter ada
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin') || !isset($_GET['id']) || !isset($_GET['aksi'])) {
    header("Location: ../index.php?page=konfirmasi-pembayaran&status=gagal&pesan=Akses tidak sah");
    exit();
}

$idTransaksi = intval($_GET['id']);
$aksi = $_GET['aksi'];

if ($aksi == 'konfirmasi') {
    // --- PROSES KONFIRMASI ---
    
    // Mulai transaksi database untuk memastikan kedua update berhasil
    mysqli_begin_transaction($kon);

    try {
        // Ambil data transaksi yang akan dikonfirmasi
        $stmt_select = mysqli_prepare($kon, "SELECT jumlah, idKampanye FROM transaksi_sosial WHERE idTransaksi = ?");
        mysqli_stmt_bind_param($stmt_select, "i", $idTransaksi);
        mysqli_stmt_execute($stmt_select);
        $result_select = mysqli_stmt_get_result($stmt_select);
        $transaksi = mysqli_fetch_assoc($result_select);

        if (!$transaksi) {
            throw new Exception("Transaksi tidak ditemukan.");
        }
        
        $jumlah = $transaksi['jumlah'];
        $idKampanye = $transaksi['idKampanye'];

        // 1. Update status transaksi menjadi 'Dikonfirmasi'
        $stmt_update_transaksi = mysqli_prepare($kon, "UPDATE transaksi_sosial SET status_pembayaran = 'Dikonfirmasi' WHERE idTransaksi = ?");
        mysqli_stmt_bind_param($stmt_update_transaksi, "i", $idTransaksi);
        mysqli_stmt_execute($stmt_update_transaksi);

        // 2. JIKA transaksi ini terikat pada sebuah kampanye, update dana terkumpul di kampanye tersebut
        if (!empty($idKampanye)) {
            $stmt_update_kampanye = mysqli_prepare($kon, "UPDATE kampanye_sosial SET dana_terkumpul = dana_terkumpul + ? WHERE idKampanye = ?");
            mysqli_stmt_bind_param($stmt_update_kampanye, "di", $jumlah, $idKampanye);
            mysqli_stmt_execute($stmt_update_kampanye);
        }

        // Jika semua query berhasil, commit transaksi
        mysqli_commit($kon);
        header("Location: ../index.php?page=konfirmasi-pembayaran&status=sukses&pesan=Pembayaran berhasil dikonfirmasi.");

    } catch (Exception $e) {
        // Jika ada error, batalkan semua perubahan
        mysqli_rollback($kon);
        header("Location: ../index.php?page=konfirmasi-pembayaran&status=gagal&pesan=" . urlencode($e->getMessage()));
    }

} elseif ($aksi == 'tolak') {
    // --- PROSES TOLAK ---
    $stmt = mysqli_prepare($kon, "UPDATE transaksi_sosial SET status_pembayaran = 'Ditolak' WHERE idTransaksi = ?");
    mysqli_stmt_bind_param($stmt, "i", $idTransaksi);
    mysqli_stmt_execute($stmt);
    header("Location: ../index.php?page=konfirmasi-pembayaran&status=sukses&pesan=Pembayaran telah ditolak.");

} else {
    header("Location: ../index.php?page=konfirmasi-pembayaran&status=gagal&pesan=Aksi tidak valid.");
}

exit();
?>