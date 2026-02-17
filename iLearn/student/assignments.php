<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* =========================
   FETCH ASSIGNMENTS
========================= */
$stmt = $pdo->query("
    SELECT a.*, s.subject_name
    FROM assignments a
    JOIN subjects s ON a.subject_id = s.subject_id
    ORDER BY a.due_date ASC
");
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FETCH SUBMISSIONS
========================= */
$sub_stmt = $pdo->prepare("SELECT * FROM submissions WHERE student_id = ?");
$sub_stmt->execute([$student_id]);
$submissions = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
$submitted_assignments = array_column($submissions, 'assignment_id');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assignments | iLearn</title>
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
<h1>📝 Assignments</h1>

<div class="cards">
<?php foreach ($assignments as $a): ?>
    <div class="card">
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <p><strong>Subject:</strong> <?= htmlspecialchars($a['subject_name']) ?></p>
        <p><strong>Due:</strong> <?= $a['due_date'] ?></p>
        <p><?= nl2br(htmlspecialchars($a['description'])) ?></p>

        <!-- 🔥 TEACHER MEDIA (FIXED PATH) -->
        <?php if (!empty($a['media_file'])): ?>
            <?php
                $base = "../uploads/assignments/{$a['media_type']}/{$a['media_file']}";
            ?>
            <?php if ($a['media_type'] === 'image'): ?>
                <img src="<?= $base ?>" style="max-width:100%;border-radius:8px;">
            <?php elseif ($a['media_type'] === 'video'): ?>
                <video controls style="max-width:100%;">
                    <source src="<?= $base ?>">
                </video>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (in_array($a['assignment_id'], $submitted_assignments)): ?>
            <p style="color:green;font-weight:600;">✅ Already Submitted</p>
        <?php else: ?>
            <form action="submit_assignment.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="assignment_id" value="<?= $a['assignment_id'] ?>">
                <input type="file" name="submission_file" required
                       accept=".pdf,.doc,.docx,.zip,.jpg,.png,.mp4,.webm">
                <br><br>
                <button type="submit" name="submit_assignment">Submit</button>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
</main>
</div>
</body>
</html>
