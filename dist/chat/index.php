<?php
//session_start();
include '../config/database.php';

if (!isset($_SESSION['kodePengguna']) || !isset($_SESSION['level'])) {
    die("Silakan login terlebih dahulu.");
}

$idSaya = $_SESSION['kodePengguna']; // contoh: plg001 atau pnjl001
$level  = strtolower($_SESSION['level']); // pelanggan atau penjual

if ($level === "pelanggan") {
    $sql = "
        SELECT 
            CASE WHEN c.idPengirim = '$idSaya' THEN c.idPenerima ELSE c.idPengirim END AS idTeman,
            u.username,
            MAX(c.waktu) AS last_time,
            SUBSTRING_INDEX(GROUP_CONCAT(c.pesan ORDER BY c.waktu DESC SEPARATOR '||'), '||', 1) AS last_message
        FROM chat c
        JOIN pengguna u 
          ON u.kodePengguna = CASE WHEN c.idPengirim = '$idSaya' THEN c.idPenerima ELSE c.idPengirim END
        WHERE (c.idPengirim = '$idSaya' OR c.idPenerima = '$idSaya') 
          AND LOWER(u.level) = 'penjual'
        GROUP BY idTeman, u.username
        ORDER BY last_time DESC
    ";
} elseif ($level === "penjual") {
    $sql = "
        SELECT 
            CASE WHEN c.idPengirim = '$idSaya' THEN c.idPenerima ELSE c.idPengirim END AS idTeman,
            u.username,
            MAX(c.waktu) AS last_time,
            SUBSTRING_INDEX(GROUP_CONCAT(c.pesan ORDER BY c.waktu DESC SEPARATOR '||'), '||', 1) AS last_message
        FROM chat c
        JOIN pengguna u 
          ON u.kodePengguna = CASE WHEN c.idPengirim = '$idSaya' THEN c.idPenerima ELSE c.idPengirim END
        WHERE (c.idPengirim = '$idSaya' OR c.idPenerima = '$idSaya') 
          AND LOWER(u.level) = 'pelanggan'
        GROUP BY idTeman, u.username
        ORDER BY last_time DESC
    ";
} else {
    die("Role tidak dikenali.");
}

$result = mysqli_query($kon, $sql);
if (!$result) {
    die("Query Error: " . mysqli_error($kon));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Chat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .chat-list-container { max-width: 600px; margin: 20px auto; }
        .chat-list-item { display: block; padding: 10px; border-bottom: 1px solid #ccc; text-decoration: none; color: black; }
        .chat-list-item:hover { background: #f5f5f5; }
        .chat-user { font-weight: bold; }
        .chat-last { color: #555; font-size: 0.9em; }
        .chat-time { color: #999; font-size: 0.8em; text-align: right; }
    </style>
</head>
<body>
<div class="chat-list-container">
    <h2>Pesan</h2>
    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <a href="chat/chat.php?idTeman=<?= urlencode($row['idTeman']) ?>" class="chat-list-item">
                <div class="chat-user"><?= htmlspecialchars($row['username']) ?></div>
                <div class="chat-last"><?= htmlspecialchars($row['last_message']) ?></div>
                <div class="chat-time"><?= htmlspecialchars($row['last_time']) ?></div>
            </a>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Belum ada percakapan.</p>
    <?php endif; ?>
</div>
</body>
</html>
