<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$teacher_id = $_SESSION['teacher_id']; // or student_id

$q = trim($_GET['q'] ?? '');

$results = [];

if ($q !== '') {
    $stmt = $pdo->prepare("
        SELECT a.*, s.subject_name
        FROM assignments a
        JOIN subjects s ON a.subject_id = s.subject_id
        WHERE s.teacher_id = ? AND (a.title LIKE ? OR a.description LIKE ?)
        ORDER BY a.due_date ASC
    ");
    $stmt->execute([$teacher_id, "%$q%", "%$q%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<h1>Search Results for "<?= htmlspecialchars($q) ?>"</h1>

<?php if ($results): ?>
    <ul>
        <?php foreach ($results as $r): ?>
            <li>
                <?= htmlspecialchars($r['title']) ?> - <?= htmlspecialchars($r['subject_name']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No results found.</p>
<?php endif; ?>
