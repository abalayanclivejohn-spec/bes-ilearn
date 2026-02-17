<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$currentPage = basename(__FILE__);

/* =========================
   FETCH QUIZZES
========================= */
$stmt = $pdo->query("
    SELECT q.quiz_id, q.title, q.due_date, s.subject_name
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.subject_id
    ORDER BY q.due_date ASC
");
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quizzes | iLearn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
</head>
<body>

<div class="dashboard">

<aside class="sidebar">
    <h2>👨‍🎓 B.E.S. iLearn</h2>
    <ul>
        <li><a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="subjects.php" class="<?= $currentPage == 'subjects.php' ? 'active' : '' ?>">Subjects</a></li>
        <li><a href="assignments.php" class="<?= $currentPage == 'assignments.php' ? 'active' : '' ?>">Assignments</a></li>
        <li><a href="materials.php" class="<?= $currentPage == 'materials.php' ? 'active' : '' ?>">Activities</a></li>
        <li><a href="modules.php" class="<?= $currentPage == 'modules.php' ? 'active' : '' ?>">Modules</a></li>
        <li><a href="quizzes.php" class="<?= $currentPage == 'quizzes.php' ? 'active' : '' ?>">Quizzes</a></li>
        <li><a href="learning.php" class="<?= $currentPage == 'learning.php' ? 'active' : '' ?>">Basic Learning</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>


<main class="content">
<h1>🧠 Available Quizzes</h1>

<div class="cards">
<?php foreach ($quizzes as $q): ?>
    <div class="card">
        <h3><?= htmlspecialchars($q['title']) ?></h3>
        <p><strong>Subject:</strong> <?= htmlspecialchars($q['subject_name']) ?></p>
        <p><strong>Due:</strong> <?= $q['due_date'] ?></p>

        <a href="quiz_student.php?quiz_id=<?= $q['quiz_id'] ?>" 
           style="font-weight:600; color:#0f0f0f;">
           Take Quiz
        </a>
    </div>
<?php endforeach; ?>
</div>

</main>
</div>
</body>
</html>
