<?php
session_start();
include '../../config/database.php';

// Cek apakah pengguna login dan merupakan pelanggan
if (!isset($_SESSION['idPengguna']) || strtolower($_SESSION['level']) !== 'pelanggan') {
    header("Location: ../../index.php"); // Arahkan ke halaman utama jika bukan pelanggan
    exit();
}

// Cek apakah form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idPengguna = $_SESSION['idPengguna'];
    $idBarang = filter_input(INPUT_POST, 'idBarang', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $ulasan = htmlspecialchars($_POST['ulasan']);

    // Validasi input
    if ($idBarang && $rating >= 1 && $rating <= 5) {
        
        // Gunakan prepared statement untuk keamanan
        $stmt = $kon->prepare("INSERT INTO ulasan_produk (idBarang, idPengguna, rating, ulasan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $idBarang, $idPengguna, $rating, $ulasan);
        
        if ($stmt->execute()) {
            // Jika berhasil, arahkan kembali ke halaman detail produk dengan notifikasi sukses
            header("Location: ../index.php?page=detail-barang&idBarang=" . $idBarang . "&ulasan=sukses");
        } else {
            // Jika gagal
            header("Location: ../index.php?page=detail-barang&idBarang=" . $idBarang . "&ulasan=gagal");
        }
        $stmt->close();
    } else {
        // Jika data tidak valid
        header("Location: ../index.php?page=detail-barang&idBarang=" . $idBarang . "&ulasan=gagal");
    }
}
$kon->close();
exit();