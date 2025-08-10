<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['kodePengguna']) || !isset($_SESSION['level'])) {
    die("Silakan login terlebih dahulu.");
}

$idSaya   = $_SESSION['kodePengguna'];
$idTeman  = isset($_GET['idTeman']) ? $_GET['idTeman'] : '';

if (empty($idTeman)) {
    die("Teman tidak ditemukan.");
}

// Ambil data teman
$sqlTeman = "SELECT username FROM pengguna WHERE kodePengguna = '$idTeman' LIMIT 1";
$resTeman = mysqli_query($kon, $sqlTeman);
if (!$resTeman || mysqli_num_rows($resTeman) == 0) {
    die("Teman tidak ditemukan di sistem.");
}
$teman = mysqli_fetch_assoc($resTeman);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat dengan <?= htmlspecialchars($teman['username']) ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .chat-box { height: 400px; border: 1px solid #ccc; overflow-y: auto; padding: 10px; }
        .message { margin: 5px 0; }
        .me { text-align: right; color: blue; }
        .them { text-align: left; color: green; }
        .chat-input { margin-top: 10px; display: flex; gap: 5px; }
        .chat-input textarea { flex: 1; height: 50px; }
    </style>
</head>
<body>

<h2>Chat dengan <?= htmlspecialchars($teman['username']) ?></h2>

<div class="chat-box" id="chat-box"></div>

<div class="chat-input">
    <textarea id="pesan" placeholder="Tulis pesan..."></textarea>
    <button id="kirim">Kirim</button>
</div>

<script>
function loadMessages() {
    $.get("load_messages.php", { idTeman: "<?= $idTeman ?>" }, function(data) {
        $("#chat-box").html(data);
        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
    });
}

$("#kirim").click(function() {
    var pesan = $("#pesan").val().trim();
    if (pesan === "") return;
    $.post("send_message.php", {
        idTeman: "<?= $idTeman ?>",
        pesan: pesan
    }, function(res) {
        $("#pesan").val("");
        loadMessages();
    });
});

setInterval(loadMessages, 3000); // refresh tiap 3 detik
loadMessages();
</script>

</body>
</html>
