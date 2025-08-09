<?php
session_start();
include '../../config/database.php';

// Cek akses admin
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Admin' && $_SESSION['level'] != 'admin')) {
    header("Location: ../index.php"); exit();
}

$aksi = $_REQUEST['aksi'] ?? '';

// AKSI HAPUS
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Ambil nama file gambar untuk dihapus
    $q_gambar = mysqli_query($kon, "SELECT gambar_kampanye FROM kampanye_sosial WHERE idKampanye=$id");
    if ($data = mysqli_fetch_assoc($q_gambar)) {
        $file_gambar = $data['gambar_kampanye'];
        if (!empty($file_gambar) && file_exists("/uploads/kampanye/$file_gambar")) {
            unlink("../uploads/kampanye_donasi/$file_gambar");
        }
    }
    mysqli_query($kon, "DELETE FROM kampanye_sosial WHERE idKampanye=$id");
    header("Location: ../index.php?page=manajemen-donasi&status=hapus_sukses");
    exit();
}

// AKSI TAMBAH ATAU EDIT (dari form POST)
if (isset($_POST['submit'])) {
    $idKampanye = intval($_POST['idKampanye'] ?? 0);
    $judul = mysqli_real_escape_string($kon, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($kon, $_POST['deskripsi']);
    $tipe = mysqli_real_escape_string($kon, $_POST['tipe']);
    $target_dana = intval($_POST['target_dana']);
    $status = mysqli_real_escape_string($kon, $_POST['status']);
    
    $nama_file_gambar = $_POST['gambar_lama'] ?? ''; // Untuk edit

    // Proses upload gambar jika ada file baru
    if (isset($_FILES['gambar_kampanye']) && $_FILES['gambar_kampanye']['error'] == 0) {
        $target_dir = "../uploads/kampanye_donasi/";
        $nama_file_asli = basename($_FILES["gambar_kampanye"]["name"]);
        $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
        $nama_file_gambar = uniqid() . '-kampanye-' . time() . '.' . $ekstensi_file;
        $target_file = $target_dir . $nama_file_gambar;
        move_uploaded_file($_FILES["gambar_kampanye"]["tmp_name"], $target_file);
    }

    if ($aksi == 'tambah') {
        $stmt = mysqli_prepare($kon, "INSERT INTO kampanye_sosial (judul, deskripsi, tipe, target_dana, status, gambar_kampanye) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssiss", $judul, $deskripsi, $tipe, $target_dana, $status, $nama_file_gambar);
    } elseif ($aksi == 'edit' && $idKampanye > 0) {
        $stmt = mysqli_prepare($kon, "UPDATE kampanye_sosial SET judul=?, deskripsi=?, tipe=?, target_dana=?, status=?, gambar_kampanye=? WHERE idKampanye=?");
        mysqli_stmt_bind_param($stmt, "sssissi", $judul, $deskripsi, $tipe, $target_dana, $status, $nama_file_gambar, $idKampanye);
    }
    
    if (isset($stmt) && mysqli_stmt_execute($stmt)) {
        header("Location: ../index.php?page=manajemen-donasi&status=sukses");
    } else {
        header("Location: ../index.php?page=manajemen-donasi&status=gagal");
    }
    exit();
}
?>