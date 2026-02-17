<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/teacher_login.php");
    exit();
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) die("No quiz selected.");

/* =========================
   FETCH QUIZ
========================= */
$stmt = $pdo->prepare("SELECT title FROM quizzes WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) die("Quiz not found.");

/* =========================
   FETCH ATTEMPTS
========================= */
$stmt = $pdo->prepare("
    SELECT a.score, a.attempted_at, s.fullname
    FROM quiz_attempts a
    JOIN students s ON a.student_id = s.student_id
    WHERE a.quiz_id = ?
    ORDER BY a.attempted_at DESC
");
$stmt->execute([$quiz_id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quiz Results | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="dashboard">
<aside class="sidebar">
    <h2>📘B.E.S. iLearn</h2>
    <ul>
        <li><a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="subjects.php" class="<?= $currentPage == 'subjects.php' ? 'active' : '' ?>">Subjects</a></li>
        <li><a href="assignments.php" class="<?= $currentPage == 'assignments.php' ? 'active' : '' ?>">Assignments</a></li>
        <li><a href="materials.php" class="<?= $currentPage == 'materials.php' ? 'active' : '' ?>">Activities</a></li>
        <li><a href="modules.php" class="<?= $currentPage == 'modules.php' ? 'active' : '' ?>">Modules</a></li>
        <li><a href="quizzes.php" class="<?= $currentPage == 'quizzes.php' ? 'active' : '' ?>">Quizzes</a></li>
        <li><a href="progress.php" class="<?= $currentPage == 'progress.php' ? 'active' : '' ?>">Student Progress</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>📊 Results: <?= htmlspecialchars($quiz['title']) ?></h1>

<table class="card">
<tr>
    <th>Student</th>
    <th>Score (%)</th>
    <th>Date</th>
</tr>
<?php foreach ($results as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['fullname']) ?></td>
    <td><?= $r['score'] ?>%</td>
    <td><?= $r['attempted_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</main>
</div>
</body>
</html>
