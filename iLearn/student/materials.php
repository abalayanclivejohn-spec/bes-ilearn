<?php
session_start();
require_once "../includes/db.php";

/* ---------------------------
   STUDENT SESSION CHECK
--------------------------- */
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* ---------------------------
   FETCH MATERIALS
--------------------------- */
$stmt = $pdo->prepare("
    SELECT m.*, s.subject_name, t.fullname AS teacher_name
    FROM materials m
    JOIN subjects s ON m.subject_id = s.subject_id
    JOIN teachers t ON s.teacher_id = t.teacher_id
    ORDER BY m.uploaded_at DESC
");
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------
   FETCH ACTIVITIES
--------------------------- */
$act = $pdo->prepare("
    SELECT a.*, s.subject_name, t.fullname AS teacher_name
    FROM activities a
    JOIN subjects s ON a.subject_id = s.subject_id
    JOIN teachers t ON a.teacher_id = t.teacher_id
    ORDER BY a.created_at DESC
");
$act->execute();
$activities = $act->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------
   HANDLE SUBMISSION UPLOAD
--------------------------- */
if (isset($_POST['submit_activity'])) {
    $activity_id = $_POST['activity_id'];
    $file = $_FILES['submission_file'] ?? null;

    $folder = "../uploads/submissions/";
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $fileName = null;

    if ($file && $file['error'] === 0) {
        $fileName = time() . "_" . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $folder . $fileName);
    }

    if ($fileName) {
        $stmt = $pdo->prepare("
            INSERT INTO submissions (activity_id, student_id, file_name)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$activity_id, $student_id, $fileName]);
        header("Location: materials.php");
        exit();
    } else {
        $error = "Please upload a file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Activities | iLearn Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/student.css">
<link rel="stylesheet" href="../assets/css/zoom.css">
<link rel="icon" type="image/png" href="../assets/img/fav.jpg">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
<div class="dashboard">

<!-- SIDEBAR -->
<?php
$currentPage = basename($_SERVER['PHP_SELF']); // gets the current page filename
?>
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

<!-- CONTENT -->
<main class="content">
<h1>📂 Learning Activities</h1> 

<div class="cards">
<?php foreach ($materials as $m): 
    $path = "../uploads/materials/";
    if ($m['file_type'] === 'image') $path .= "image/";
    elseif ($m['file_type'] === 'video') $path .= "video/";
    else $path .= "file/";

    if (!file_exists($path . $m['file_name'])) $path = "../uploads/materials/";
?>
    <div class="card">
        <h3><?= htmlspecialchars($m['title']) ?></h3>
        <p><strong>Subject:</strong> <?= htmlspecialchars($m['subject_name']) ?></p>
        <p><strong>Teacher:</strong> <?= htmlspecialchars($m['teacher_name']) ?></p>
        <p><strong>Type:</strong> <?= strtoupper($m['file_type']) ?></p>

        <?php if ($m['file_type'] === 'image'): ?>
            <img src="<?= $path . htmlspecialchars($m['file_name']) ?>" class="zoomable" style="max-width:100%; border-radius:8px;">
            <div style="margin-top:8px;">
                <a href="<?= $path . htmlspecialchars($m['file_name']) ?>" target="_blank" class="btn">View Image</a>
                <a href="<?= $path . htmlspecialchars($m['file_name']) ?>" download class="btn">Download</a>
            </div>
        <?php elseif ($m['file_type'] === 'video'): ?>
            <video src="<?= $path . htmlspecialchars($m['file_name']) ?>" class="zoomable" controls style="max-width:100%; border-radius:8px;"></video>
            <div style="margin-top:8px;">
                <a href="<?= $path . htmlspecialchars($m['file_name']) ?>" target="_blank" class="btn">View Video</a>
                <a href="<?= $path . htmlspecialchars($m['file_name']) ?>" download class="btn">Download</a>
            </div>
        <?php else: ?>
            <a href="<?= $path . htmlspecialchars($m['file_name']) ?>" download class="btn">Download File</a>
        <?php endif; ?>
    </div>
    
<?php endforeach; ?>
</div>

<!-- Teacher Activities -->
<div class="cards">
<?php foreach ($activities as $a): 
    $path = "../uploads/activities/";
    if ($a['file_type'] === 'image') $path .= "image/";
    elseif ($a['file_type'] === 'video') $path .= "video/";
    else $path .= "file/";

    if (!file_exists($path . $a['file_name'])) $path = "../uploads/activities/";
?>
    <div class="card">
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <p><strong>Subject:</strong> <?= htmlspecialchars($a['subject_name']) ?></p>
        <p><strong>Teacher:</strong> <?= htmlspecialchars($a['teacher_name']) ?></p>

        <?php if ($a['file_type'] === 'image'): ?>
            <img src="<?= $path . htmlspecialchars($a['file_name']) ?>" class="zoomable" style="max-width:100%; border-radius:8px;">
            <div style="margin-top:8px;">
                <a href="<?= $path . htmlspecialchars($a['file_name']) ?>" target="_blank" class="btn">View Image</a>
                <a href="<?= $path . htmlspecialchars($a['file_name']) ?>" download class="btn">Download</a>
            </div>
        <?php elseif ($a['file_type'] === 'video'): ?>
            <video src="<?= $path . htmlspecialchars($a['file_name']) ?>" class="zoomable" controls style="max-width:100%; border-radius:8px;"></video>
            <div style="margin-top:8px;">
                <a href="<?= $path . htmlspecialchars($a['file_name']) ?>" target="_blank" class="btn">View Video</a>
                <a href="<?= $path . htmlspecialchars($a['file_name']) ?>" download class="btn">Download</a>
            </div>
        <?php else: ?>
            <a href="<?= $path . htmlspecialchars($a['file_name']) ?>" download class="btn">Download File</a>
        <?php endif; ?>

        <!-- Submission form -->
        <form method="post" enctype="multipart/form-data" style="margin-top:10px;">
            <input type="hidden" name="activity_id" value="<?= $a['activity_id'] ?>">
            <input type="file" name="submission_file" accept="image/*,video/*,application/pdf" required>
            <button type="submit" name="submit_activity" style="margin-top:8px;">Submit</button>
        </form>
        
    </div>
<?php endforeach; ?>
</div>

</main>
</div>

<script src="../assets/js/zoom.js"></script>
<script>
// Fullscreen zoom for images & videos
document.querySelectorAll('.zoomable').forEach(el => {
    el.style.cursor = 'zoom-in';
    el.addEventListener('dblclick', () => {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            el.requestFullscreen();
        }
    });
});
</script>

</body>
</html>
