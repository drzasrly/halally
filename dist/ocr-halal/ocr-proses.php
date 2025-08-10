<?php
session_start();
include '../config/database.php';

require_once __DIR__ . '/vendor/autoload.php';
use thiagoalessio\TesseractOCR\TesseractOCR;

if (!isset($_SESSION['kodePengguna'])) {
    die("Silakan login terlebih dahulu.");
}
$kodePengguna = $_SESSION['kodePengguna'];

if (!empty($_FILES['image']['tmp_name'])) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        // Jalankan OCR
        $text = (new TesseractOCR($filePath))->lang('eng')->run();

        // Deteksi status Halal/Haram
        $textLower = strtolower($text);
        if (strpos($textLower, 'pork') !== false || strpos($textLower, 'babi') !== false) {
            $status = 'Haram';
        } elseif (strpos($textLower, 'halal') !== false) {
            $status = 'Halal';
        } else {
            $status = 'Tidak Diketahui';
        }

        // Simpan ke database
        $stmt = $kon->prepare("INSERT INTO ocr_scans (kodePengguna, filename, text_result, status, scan_time) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $kodePengguna, $fileName, $text, $status);
        $stmt->execute();

        echo "<script>window.parent.showPopup('<b>Hasil Scan:</b><br>" . htmlspecialchars($text) . "<br><b>Status:</b> $status');</script>";
    } else {
        echo "<script>window.parent.showPopup('Gagal mengupload file.');</script>";
    }
} else {
    echo "<script>window.parent.showPopup('Tidak ada file yang diupload.');</script>";
}
