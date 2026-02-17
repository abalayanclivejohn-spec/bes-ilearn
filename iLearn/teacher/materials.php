<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$teacher_id = $_SESSION['teacher_id'];

/* =========================
   FETCH SUBJECTS
========================= */
$subjectsStmt = $pdo->prepare("SELECT * FROM subjects WHERE teacher_id = ?");
$subjectsStmt->execute([$teacher_id]);
$subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   UPLOAD ACTIVITY (PHOTO / VIDEO / FILE)
========================= */
if (isset($_POST['add_activity'])) {
    $subject_id = $_POST['subject_id'];
    $title      = trim($_POST['title']);
    $file       = $_FILES['activity_file'];

    if ($file['error'] !== 0) die("Upload error.");

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    $imageExt = ['jpg','jpeg','png','gif','webp'];
    $videoExt = ['mp4','mov','avi','webm','mkv'];
    $type = 'file';

    if (in_array($ext, $imageExt)) {
        $type = 'image';
        $folder = "../uploads/materials/image/";
    } elseif (in_array($ext, $videoExt)) {
        $type = 'video';
        $folder = "../uploads/materials/video/";
    } else {
        $folder = "../uploads/materials/file/";
    }

    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $newName = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($file['name']));

    if (move_uploaded_file($file['tmp_name'], $folder . $newName)) {
        $stmt = $pdo->prepare("
            INSERT INTO materials (subject_id, teacher_id, title, file_name, file, file_type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $subject_id,
            $teacher_id,
            $title,
            $newName,
            $file['name'],   // original name
            $type
        ]);
        header("Location: materials.php");
        exit();
    }
}

/* =========================
   DELETE MATERIAL
========================= */
if (isset($_GET['delete_material'])) {
    $id = (int)$_GET['delete_material'];

    $stmt = $pdo->prepare("SELECT * FROM materials WHERE material_id = ? AND teacher_id = ?");
    $stmt->execute([$id, $teacher_id]);
    $m = $stmt->fetch();

    if ($m) {
        $folder = match($m['file_type']) {
            'image' => "../uploads/materials/image/",
            'video' => "../uploads/materials/video/",
            default => "../uploads/materials/file/",
        };

        $path = $folder . $m['file_name'];
        if (file_exists($path)) unlink($path);

        $pdo->prepare("DELETE FROM materials WHERE material_id = ?")->execute([$id]);
    }
    header("Location: materials.php");
    exit();
}

/* =========================
   FETCH ACTIVITIES
========================= */
$actStmt = $pdo->prepare("
    SELECT m.*, s.subject_name
    FROM materials m
    JOIN subjects s ON m.subject_id = s.subject_id
    WHERE m.teacher_id = ?
    ORDER BY m.material_id DESC
");
$actStmt->execute([$teacher_id]);
$activities = $actStmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FETCH SUBMISSIONS FOR ACTIVITIES
========================= */
$submissionsStmt = $pdo->prepare("
    SELECT sub.*, st.fullname AS student_name, m.material_id, m.title AS activity_title
    FROM submissions sub
    JOIN students st ON sub.student_id = st.student_id
    JOIN materials m ON sub.activity_id = m.material_id
    WHERE m.teacher_id = ?
    ORDER BY sub.submitted_at DESC
");
$submissionsStmt->execute([$teacher_id]);
$submissions = $submissionsStmt->fetchAll(PDO::FETCH_ASSOC);

/* GROUP SUBMISSIONS BY ACTIVITY */
$subsByActivity = [];
foreach ($submissions as $sub) {
    $subsByActivity[$sub['activity_id']][] = $sub;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activities | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .cards { display:flex; flex-wrap:wrap; gap:15px; }
        .card { background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); flex:1 1 300px; }
        img, video { max-width:100%; border-radius:10px; margin-top:10px; border:3px solid #6c63ff; }
        video { max-height:500px; }
        .activity-form { background:#f4f4f4; padding:20px; border-radius:10px; margin-bottom:25px; }
        .submissions-preview { margin-top:10px; }
        .submission-card { background:#f9f9f9; padding:10px; margin-bottom:10px; border-radius:8px; border:1px solid #ddd; }
        button { background:#6c63ff; color:white; padding:8px 15px; border:none; border-radius:6px; cursor:pointer; }
        button:hover { background:#574fd6; }
        select, input[type="text"], input[type="file"] { padding:8px; width:100%; max-width:400px; margin-bottom:10px; border-radius:5px; border:1px solid #ccc; }
        .btn { display:inline-block; padding:6px 12px; margin-right:5px; background:#7b51c5; color:#fff; border-radius:6px; text-decoration:none; font-size:0.9rem; transition:0.3s; }
        .btn:hover { background:#6229a6; transform:scale(1.05); }
    </style>
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
<h1>🎯 Learning Activities</h1>

<div class="activity-form">
    <h3>Add Activity (Image, Video, or File)</h3>
    <form method="post" enctype="multipart/form-data">
        <select name="subject_id" required>
            <option value="">Select Subject</option>
            <?php foreach ($subjects as $s): ?>
                <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
            <?php endforeach; ?>
        </select><br>
        <input type="text" name="title" placeholder="Activity Title" required><br>
        <input type="file" name="activity_file" required><br>
        <button type="submit" name="add_activity">Upload Activity</button>
    </form>
</div>

<h2>📂 Uploaded Activities</h2>
<div class="cards">
<?php foreach ($activities as $a): ?>
    <div class="card">
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <p><strong>Subject:</strong> <?= htmlspecialchars($a['subject_name']) ?></p>

        <?php 
        $filePath = '';
        if ($a['file_type'] === 'image') {
            $filePath = "../uploads/materials/image/{$a['file_name']}";
            echo "<img src='{$filePath}' alt='Activity Image'>";
        } elseif ($a['file_type'] === 'video') {
            $filePath = "../uploads/materials/video/{$a['file_name']}";
            echo "<video controls preload='metadata'><source src='{$filePath}' type='video/mp4'>Your browser does not support the video tag.</video>";
        } else {
            $filePath = "../uploads/materials/file/{$a['file_name']}";
            echo "<p>File: <a href='{$filePath}' target='_blank'>{$a['file_name']}</a></p>";
        }
        ?>
        <div style="margin-top:8px;">
            <a href="<?= $filePath ?>" target="_blank" class="btn">View</a>
            <a href="<?= $filePath ?>" download class="btn">Download</a>
            <a href="?delete_material=<?= $a['material_id'] ?>" onclick="return confirm('Delete this activity?')" class="btn" style="background:#991b1b;">Delete</a>
        </div>

        <!-- SUBMISSIONS -->
        <?php if (isset($subsByActivity[$a['material_id']])): ?>
        <h4 style="margin-top:10px;">📥 Student Submissions</h4>
        <div class="submissions-preview">
            <?php foreach ($subsByActivity[$a['material_id']] as $sub): ?>
                <div class="submission-card">
                    <p><strong><?= htmlspecialchars($sub['student_name']) ?></strong> - <em><?= $sub['submitted_at'] ?></em></p>
                    <?php 
                    $subPath = "../uploads/submissions/{$sub['file_name']}";
                    $ext = strtolower(pathinfo($sub['file_name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) echo "<img src='{$subPath}' style='max-width:400px; border:2px solid #6c63ff; border-radius:8px; margin-top:5px;'>";
                    elseif (in_array($ext, ['mp4','mov','avi','webm','mkv'])) echo "<video controls preload='metadata' style='max-width:400px; max-height:300px; border:2px solid #6c63ff; border-radius:8px; margin-top:5px;'><source src='{$subPath}'></video>";
                    else echo "<p>File: <a href='{$subPath}' target='_blank'>{$sub['file_name']}</a></p>";
                    ?>
                    <div style="margin-top:5px;">
                        <a href="<?= $subPath ?>" download class="btn">Download</a>
                        <a href="delete_submission.php?id=<?= $sub['submission_id'] ?>" onclick="return confirm('Delete this submission?');" class="btn" style="background:#991b1b;">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>

</main>
</div>
</body>
</html>
