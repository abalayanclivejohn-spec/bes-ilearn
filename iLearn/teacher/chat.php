<?php
require_once "../includes/session.php";
require_once "../includes/db.php";
$teacher_id = $_SESSION['teacher_id'];

// Send message
if (isset($_POST['send'])) {
    $msg = trim($_POST['message']);
    if ($msg !== "") {
        $stmt = $pdo->prepare(
            "INSERT INTO chat (sender_role, sender_id, message) VALUES ('teacher', ?, ?)"
        );
        $stmt->execute([$teacher_id, $msg]);
    }
}

// Fetch chat messages
$chats = $pdo->query(
    "SELECT * FROM chat ORDER BY created_at ASC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat | iLearn</title>
    <link rel="stylesheet" href="../assets/css/chat.css">
    <style>
        .chat-container { max-width: 800px; margin: 0 auto; }
        .chat-box { height: 400px; overflow-y: scroll; border:1px solid #d1d5db; padding:10px; border-radius:10px; background:#f9fafb; margin-bottom:10px;}
        .chat-message { margin-bottom: 10px; }
        .chat-message.teacher { color: #2563eb; font-weight:600; }
        .chat-message.student { color: #16a34a; font-weight:600; }
        form { display: flex; gap:10px; }
        input[type=text] { flex:1; padding:10px; border-radius:8px; border:1px solid #d1d5db; }
        button { padding:10px 20px; border:none; background:#1f2937; color:#fff; border-radius:8px; cursor:pointer; }
    </style>
</head>
<body>

<div class="dashboard">

<aside class="sidebar">
    <h2>📘 iLearn</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="subjects.php">Subjects</a></li>
        <li><a href="assignments.php">Assignments</a></li>
        <li><a href="materials.php">Materials</a></li>
        <li><a class="active" href="chat.php">Chat</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>💬 Teacher Chat</h1>

<div class="chat-container">
    <div id="chat-box" class="chat-box">
        <?php foreach($chats as $c): ?>
            <div class="chat-message <?= $c['sender_role'] ?>">
                <strong><?= ucfirst($c['sender_role']) ?>:</strong>
                <?= htmlspecialchars($c['message']) ?>
                <span style="font-size:10px; color:#6b7280;">(<?= $c['created_at'] ?>)</span>
            </div>
        <?php endforeach; ?>
    </div>

    <form id="chat-form" method="post">
        <input type="text" name="message" placeholder="Type a message..." required>
        <button type="submit" name="send">Send</button>
    </form>
</div>

</main>
</div>

<script src="../assets/js/chat.js"></script>
</body>
</html>
