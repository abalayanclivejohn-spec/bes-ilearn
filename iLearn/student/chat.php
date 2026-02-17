<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Send message
if (isset($_POST['send'])) {
    $msg = trim($_POST['message']);
    if ($msg !== "") {
        $stmt = $pdo->prepare(
            "INSERT INTO chat (sender_role, sender_id, message) VALUES ('student', ?, ?)"
        );
        $stmt->execute([$student_id, $msg]);
    }
}

// Fetch all chats
$chats = $pdo->query("SELECT * FROM chat ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat | iLearn</title>
    <link rel="stylesheet" href="../assets/css/chat.css">
</head>
<body>

<div class="dashboard">

<aside class="sidebar">
    <h2>👨‍🎓 iLearn</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="assignments.php">Assignments</a></li>
        <li><a href="materials.php">Materials</a></li>
        <li><a class="active" href="chat.php">Chat</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>💬 Student Chat</h1>

<div class="chat-container">
    <div id="chat-box" class="chat-box">
        <?php foreach($chats as $c): ?>
            <div class="chat-message <?= $c['sender_role'] ?>">
    <strong><?= ucfirst($c['sender_role']) ?>:</strong>
    <?= htmlspecialchars($c['message']) ?>
    <span><?= $c['created_at'] ?></span>
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
