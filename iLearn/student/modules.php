<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* =========================
   FETCH MODULES
========================= */
$stmt = $pdo->prepare("
    SELECT m.id, m.title, m.content, m.file, m.created_at, 
           t.fullname AS teacher_name
    FROM modules m
    JOIN teachers t ON m.teacher_id = t.teacher_id
    ORDER BY m.created_at DESC
");
$stmt->execute();
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Modules | iLearn</title>
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
<h1>📚 Learning Modules</h1>

<div class="cards">

<?php foreach($modules as $m): ?>
<div class="card">
<h3><?= htmlspecialchars($m['title']) ?></h3>
<small>By <?= htmlspecialchars($m['teacher_name']) ?> | <?= $m['created_at'] ?></small>

<?php if(!empty($m['content'])): ?>
<p><?= nl2br(htmlspecialchars($m['content'])) ?></p>
<?php endif; ?>

<?php if(!empty($m['file'])): ?>
    <?php
        $filePath = "../uploads/modules/documents/" . $m['file'];
        $publicUrl = "http://localhost/iLearn/uploads/modules/documents/" . $m['file'];
        $ext = strtolower(pathinfo($m['file'], PATHINFO_EXTENSION));
    ?>

    <button onclick="document.getElementById('view<?= $m['id'] ?>').style.display='block'">
        👁 View Content
    </button>

    <a href="<?= $filePath ?>" download>⬇ Download Content</a>

    <div id="view<?= $m['id'] ?>" style="display:none; margin-top:15px;">
        <?php if(in_array($ext, ['pdf'])): ?>
            <iframe src="<?= $filePath ?>" style="width:100%; height:500px;"></iframe>

        <?php elseif(in_array($ext, ['doc','docx','ppt','pptx'])): ?>
            <iframe
  src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($publicUrl) ?>"
  style="width:100%; height:500px;"
  frameborder="0">
</iframe>

        <?php else: ?>
            <p>❌ No preview available for this file.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

</div>
<?php endforeach; ?>

</div>
</main>
</div>
</body>
</html>