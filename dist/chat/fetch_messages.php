<?php
session_start();
include '../../config/database.php';

$idBarang = $_GET['barang'] ?? '';
$idUser   = $_SESSION['kodePengguna'] ?? '';

$sql = "SELECT b.kodePenjual FROM barang b WHERE b.idBarang = '$idBarang' LIMIT 1";
$res = mysqli_query($kon, $sql);
$data = mysqli_fetch_assoc($res);
if (!$data) exit;

$idPenjual = $data['kodePenjual'];

$q = "SELECT c.*, u.username 
      FROM chat c
      JOIN pengguna u ON u.kodePengguna = c.idPengirim
      WHERE 
          (c.idPengirim = '$idUser' AND c.idPenerima = '$idPenjual')
          OR
          (c.idPengirim = '$idPenjual' AND c.idPenerima = '$idUser')
      ORDER BY c.waktu ASC";
$res = mysqli_query($kon, $q);

while ($row = mysqli_fetch_assoc($res)) {
    $class = ($row['idPengirim'] === $idUser) ? 'me' : 'them';
    echo '<div class="bubble '.$class.'">';
    echo '<strong>'.htmlspecialchars($row['username']).':</strong><br>';
    echo htmlspecialchars($row['pesan']);
    echo '<div class="small text-muted">'.date('H:i', strtotime($row['waktu'])).'</div>';
    echo '</div>';
}
