<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['kodePengguna'])) {
    die("Silakan login terlebih dahulu.");
}

$idSaya = $_SESSION['kodePengguna'];
$idTeman = $_GET['idTeman'] ?? null;

if (!$idTeman) {
    die("Teman tidak ditemukan.");
}

// Ambil semua pesan antara saya dan teman
$sql = "SELECT * FROM chat 
        WHERE (idPengirim = '".mysqli_real_escape_string($kon, $idSaya)."' AND idPenerima = '".mysqli_real_escape_string($kon, $idTeman)."')
           OR (idPengirim = '".mysqli_real_escape_string($kon, $idTeman)."' AND idPenerima = '".mysqli_real_escape_string($kon, $idSaya)."')
        ORDER BY waktu ASC";

$q = mysqli_query($kon, $sql);

while ($row = mysqli_fetch_assoc($q)) {
    $isSaya = ($row['idPengirim'] === $idSaya);
    $class  = $isSaya ? 'me' : 'friend';
    echo "<div class='message {$class}'>";
    echo "<span class='text'>" . htmlspecialchars($row['pesan']) . "</span>";
    echo "<span class='time'>" . date('H:i', strtotime($row['waktu'])) . "</span>";
    echo "</div>";
}
