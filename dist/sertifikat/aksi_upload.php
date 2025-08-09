<?php
session_start();
include '../../config/database.php'; // Sesuaikan path ke file koneksi database

// Cek apakah pengguna adalah penjual dan form telah disubmit
if (!isset($_SESSION['idPengguna']) || ($_SESSION['level'] != 'Penjual' && $_SESSION['level'] != 'penjual') || !isset($_POST['submit'])) {
    header("Location: ../index.php?page=sertifikat-halal&status=gagal&pesan=" . urlencode("Akses tidak sah."));
    exit();
}

// Ambil data dari form
$idPenjual = $_SESSION['idPengguna'];
$nomor_sertifikat = mysqli_real_escape_string($kon, $_POST['nomor_sertifikat']);
$penerbit = mysqli_real_escape_string($kon, $_POST['penerbit']);
$tanggal_berlaku = mysqli_real_escape_string($kon, $_POST['tanggal_berlaku']);

// Proses Upload File
$pesan_error = '';
if (isset($_FILES['file_sertifikat']) && $_FILES['file_sertifikat']['error'] == 0) {
    $target_dir = "../../uploads/sertifikat/"; // Path relatif dari file aksi_upload.php
    $nama_file_asli = basename($_FILES["file_sertifikat"]["name"]);
    $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
    $nama_file_unik = uniqid() . '-' . time() . '.' . $ekstensi_file;
    $target_file = $target_dir . $nama_file_unik;

    // 1. Validasi Ekstensi
    $ekstensi_diizinkan = array('jpg', 'jpeg', 'png', 'pdf');
    if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
        $pesan_error = "Format file tidak diizinkan. Hanya JPG, PNG, dan PDF yang diperbolehkan.";
    }

    // 2. Validasi Ukuran File (misal, maks 2MB)
    if ($_FILES["file_sertifikat"]["size"] > 2000000) {
        $pesan_error = "Ukuran file terlalu besar. Maksimal 2 MB.";
    }

    // Jika tidak ada error, pindahkan file
    if (empty($pesan_error)) {
        if (!move_uploaded_file($_FILES["file_sertifikat"]["tmp_name"], $target_file)) {
            $pesan_error = "Terjadi kesalahan saat mengunggah file.";
        }
    }
} else {
    $pesan_error = "File sertifikat wajib diunggah.";
}

// Jika ada error saat upload file, redirect kembali
if (!empty($pesan_error)) {
    header("Location: ../index.php?page=sertifikat-halal&status=gagal&pesan=" . urlencode($pesan_error));
    exit();
}

// Jika file berhasil diunggah, simpan data ke database
$stmt = mysqli_prepare($kon, "INSERT INTO sertifikat_halal (idPenjual, nomor_sertifikat, penerbit, tanggal_berlaku, file_sertifikat, status_verifikasi) VALUES (?, ?, ?, ?, ?, 'Menunggu Verifikasi')");

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "issss", $idPenjual, $nomor_sertifikat, $penerbit, $tanggal_berlaku, $nama_file_unik);
    if (mysqli_stmt_execute($stmt)) {
        // Berhasil
        mysqli_stmt_close($stmt);
        mysqli_close($kon);
        header("Location: ../index.php?page=sertifikat-halal&status=sukses");
        exit();
    } else {
        // Gagal eksekusi query
        $pesan_error = "Gagal menyimpan data ke database: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    // Gagal prepare statement
    $pesan_error = "Gagal mempersiapkan query: " . mysqli_error($kon);
}

mysqli_close($kon);
// Jika sampai di sini, berarti ada error database
header("Location: ../index.php?page=sertifikat-halal&status=gagal&pesan=" . urlencode($pesan_error));
exit();

?>