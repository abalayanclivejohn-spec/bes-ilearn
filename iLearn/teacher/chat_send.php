<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if(isset($_POST['message']) && trim($_POST['message']) !== "") {
    $stmt = $pdo->prepare("INSERT INTO chat (sender_role, sender_id, message) VALUES ('teacher', ?, ?)");
    $stmt->execute([$_SESSION['teacher_id'], trim($_POST['message'])]);
}
