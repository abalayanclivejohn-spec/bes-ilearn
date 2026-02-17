<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

/* =========================
   DELETE
========================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM modules WHERE id=? AND teacher_id=?")
        ->execute([$id, $teacher_id]);
    header("Location: modules.php");
    exit();
}

/* =========================
   CREATE
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');

    if ($title === '') die("Module title is required.");

    $fileName = null;

    if (!empty($_FILES['file']['name'])) {
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf','doc','docx','ppt','pptx'];

        if (!in_array($ext, $allowed)) {
            die("Only PDF, Word, PPT allowed.");
        }

        $folder = "../uploads/modules/documents/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $fileName = time() . "_" . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $folder . $fileName);
    }

    $stmt = $pdo->prepare("
        INSERT INTO modules (teacher_id, subject_id, title, file)
        VALUES (?, 0, ?, ?)
    ");
    $stmt->execute([$teacher_id, $title, $fileName]);

    header("Location: modules.php");
    exit();
}

/* =========================
   FETCH
========================= */
$stmt = $pdo->prepare("SELECT * FROM modules WHERE teacher_id=? ORDER BY created_at DESC");
$stmt->execute([$teacher_id]);
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <title>Modules | iLearn</title>
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
        <li><a href="quiz.php" class="<?= $currentPage == 'quiz.php' ? 'active' : '' ?>">Quizzes</a></li>
        <li><a href="progress.php" class="<?= $currentPage == 'progress.php' ? 'active' : '' ?>">Student Progress</a></li>
        <li><a href="learning.php" class="<?= $currentPage == 'learning.php' ? 'active' : '' ?>">Basic Learning</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>📚 Modules</h1>

<div class="card" style="max-width:500px;">
<h3>Add Module</h3>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Module Title" required>
    <br><br>
    <input type="file" name="file" required>
    <br><br>
    <button type="submit">Upload</button>
</form>
</div>

<div class="cards">
<?php foreach($modules as $m): ?>
<div class="card">
    <h3><?= htmlspecialchars($m['title']) ?></h3>

    <?php if($m['file']): ?>
        <?php
            $filePath = "../uploads/modules/documents/" . $m['file'];
            $publicUrl = "http://localhost/iLearn/uploads/modules/documents/" . $m['file'];
            $ext = strtolower(pathinfo($m['file'], PATHINFO_EXTENSION));
        ?>

        <button onclick="document.getElementById('view<?= $m['id'] ?>').style.display='block'">
            👁 View Content
        </button>

        <a href="<?= $filePath ?>" download>⬇ Download</a>

        <div id="view<?= $m['id'] ?>" style="display:none; margin-top:10px;">
            <?php if($ext === 'pdf'): ?>
                <iframe src="<?= $filePath ?>" style="width:100%; height:500px;"></iframe>

            <?php elseif(in_array($ext, ['doc','docx','ppt','pptx'])): ?>
                <iframe
  src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($publicUrl) ?>"
  style="width:100%; height:500px;"
  frameborder="0">
</iframe>
            <?php else: ?>
                <p>❌ No preview available.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <br><br>
    <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Delete this module?')" style="color:red;">
        🗑 Delete
    </a>
</div>
<?php endforeach; ?>
</div>

</main>
</div>
</body>
</html>
