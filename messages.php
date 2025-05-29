<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiver_id = $_POST["receiver_id"];
    $message = $_POST["message"];
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, date) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$_SESSION["user_id"], $receiver_id, $message]);
}

// Afficher les messages échangés avec l’admin
$stmt = $pdo->prepare("SELECT * FROM messages WHERE (sender_id = ? OR receiver_id = ?) ORDER BY date DESC");
$stmt->execute([$_SESSION["user_id"], $_SESSION["user_id"]]);
$messages = $stmt->fetchAll();
?>
<h2>Messagerie</h2>
<form method="post">
    Destinataire (ID): <input type="number" name="receiver_id" required>
    <br>
    Message: <input type="text" name="message" required>
    <button type="submit">Envoyer</button>
</form>
<ul>
<?php foreach ($messages as $msg): ?>
    <li>
        <strong><?php echo $msg["sender_id"] == $_SESSION["user_id"] ? "Moi" : "Autre"; ?>:</strong>
        <?php echo htmlspecialchars($msg["message"]); ?> (<?php echo $msg["date"]; ?>)
    </li>
<?php endforeach; ?>
</ul>