<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$chats = $pdo->query("SELECT * FROM chat ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);

foreach($chats as $c) {
    echo '<div class="chat-message '.$c['sender_role'].'"><strong>'.ucfirst($c['sender_role']).':</strong> '.htmlspecialchars($c['message']).' <span style="font-size:10px;color:#6b7280;">('.$c['created_at'].')</span></div>';
}
