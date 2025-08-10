<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['kodePengguna'])) {
    exit;
}

$idSaya  = $_SESSION['kodePengguna'];
$idTeman = $_GET['idTeman'];

$sql = "SELECT * FROM chat 
        WHERE (idPengirim = '$idSaya' AND idPenerima = '$idTeman') 
           OR (idPengirim = '$idTeman' AND idPenerima = '$idSaya')
        ORDER BY waktu ASC";
$res = mysqli_query($kon, $sql);

while ($row = mysqli_fetch_assoc($res)) {
    $class = ($row['idPengirim'] == $idSaya) ? 'me' : 'them';
    echo "<div class='message $class'>" . htmlspecialchars($row['pesan']) . "</div>";
}
