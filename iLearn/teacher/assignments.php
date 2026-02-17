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
   ADD ASSIGNMENT (MEDIA OPTIONAL)
========================= */
if (isset($_POST['add_assignment'])) {
    $subject_id  = $_POST['subject_id'];
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date    = $_POST['due_date'];

    $mediaName = null;
    $mediaType = null;

    // Handle optional media
    if (!empty($_FILES['media']['name'])) {
        $imageExt = ['jpg','jpeg','png','gif'];
        $videoExt = ['mp4','mov','avi','webm'];
        $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $imageExt)) $mediaType = 'image';
        elseif (in_array($ext, $videoExt)) $mediaType = 'video';
        else die("Invalid media type.");

        $folder = "../uploads/assignments/$mediaType/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $mediaName = time() . "_" . basename($_FILES['media']['name']);
        move_uploaded_file($_FILES['media']['tmp_name'], $folder . $mediaName);
    }

    if ($subject_id && $title && $due_date) {
        $stmt = $pdo->prepare("
            INSERT INTO assignments 
            (subject_id, title, description, due_date, teacher_id, media_file, media_type)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $subject_id,
            $title,
            $description,
            $due_date,
            $teacher_id,
            $mediaName,
            $mediaType
        ]);

        header("Location: assignments.php");
        exit();
    }
}

/* =========================
   DELETE ASSIGNMENT
========================= */
if (isset($_GET['delete'])) {
    $assignment_id = (int)$_GET['delete'];

    // Fetch media file if exists
    $stmt = $pdo->prepare("SELECT media_file, media_type FROM assignments a JOIN subjects s ON a.subject_id = s.subject_id WHERE a.assignment_id = ? AND s.teacher_id = ?");
    $stmt->execute([$assignment_id, $teacher_id]);
    $a = $stmt->fetch();
    if ($a && $a['media_file']) {
        $path = "../uploads/assignments/{$a['media_type']}/{$a['media_file']}";
        if (file_exists($path)) unlink($path);
    }

    $stmt = $pdo->prepare("DELETE a FROM assignments a JOIN subjects s ON a.subject_id = s.subject_id WHERE a.assignment_id = ? AND s.teacher_id = ?");
    $stmt->execute([$assignment_id, $teacher_id]);
    header("Location: assignments.php");
    exit();
}

/* =========================
   FETCH ASSIGNMENTS
========================= */
$stmt = $pdo->prepare("
    SELECT a.*, s.subject_name
    FROM assignments a
    JOIN subjects s ON a.subject_id = s.subject_id
    WHERE s.teacher_id = ?
    ORDER BY a.due_date ASC
");
$stmt->execute([$teacher_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FETCH SUBMISSIONS
========================= */
$selected_assignment_id = $_GET['view_submissions'] ?? null;
$submissions = [];

if ($selected_assignment_id) {
    $stmt = $pdo->prepare("
        SELECT sub.*, st.fullname AS student_name
        FROM submissions sub
        JOIN students st ON sub.student_id = st.student_id
        JOIN assignments a ON sub.assignment_id = a.assignment_id
        WHERE a.teacher_id = ? AND a.assignment_id = ?
        ORDER BY sub.submitted_at DESC
    ");
    $stmt->execute([$teacher_id, $selected_assignment_id]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Assignments | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        .cards { display:flex; flex-wrap:wrap; gap:15px; }
        .card { background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); flex:1 1 300px; }
        img, video { max-width:100%; border-radius:6px; margin-top:5px; }
        .submission-preview { margin-top:10px; }
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
<h1>📝 Assignments</h1>

<!-- ADD ASSIGNMENT FORM -->
<form method="post" enctype="multipart/form-data" class="card">
    <h3>Create Assignment</h3>
    <select name="subject_id" required>
        <option value="">Select Subject</option>
        <?php foreach ($subjects as $s): ?>
            <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <input type="text" name="title" placeholder="Assignment Title" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <input type="date" name="due_date" required><br><br>

    <input type="file" name="media" accept="image/*,video/*"><br><br>
    <button type="submit" name="add_assignment">Add Assignment</button>
</form>

<!-- ASSIGNMENTS LIST -->
<div class="cards">
<?php foreach ($assignments as $a): ?>
    <div class="card">
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <p><strong>Subject:</strong> <?= htmlspecialchars($a['subject_name']) ?></p>
        <p><strong>Due:</strong> <?= $a['due_date'] ?></p>
        <p><?= nl2br(htmlspecialchars($a['description'])) ?></p>

        <?php if ($a['media_file']): ?>
            <?php if ($a['media_type'] === 'image'): ?>
                <img src="../uploads/assignments/image/<?= $a['media_file'] ?>" alt="Media">
            <?php else: ?>
                <video controls>
                    <source src="../uploads/assignments/video/<?= $a['media_file'] ?>">
                </video>
            <?php endif; ?>
        <?php endif; ?>

        <a href="?view_submissions=<?= $a['assignment_id'] ?>">View Submissions</a> |
        <a href="?delete=<?= $a['assignment_id'] ?>" onclick="return confirm('Delete assignment?')" style="color:#991b1b;">Delete</a>
    </div>
<?php endforeach; ?>
</div>

<!-- STUDENT SUBMISSIONS -->
<?php if ($selected_assignment_id): ?>
<h2>📥 Student Submissions</h2>
<div class="cards">
<?php foreach ($submissions as $sub): ?>
    <div class="card">
        <p><strong><?= htmlspecialchars($sub['student_name']) ?></strong></p>
        <p>Submitted: <?= $sub['submitted_at'] ?></p>

        <?php 
        $filePath = "../uploads/submissions/{$sub['file_name']}";
        $ext = strtolower(pathinfo($sub['file_name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
            <img src="<?= $filePath ?>" style="max-width:100%;">
        <?php elseif (in_array($ext, ['mp4','mov','avi','webm'])): ?>
            <video controls style="max-width:100%;">
                <source src="<?= $filePath ?>">
            </video>
        <?php else: ?>
            <p>File: <?= htmlspecialchars($sub['file_name']) ?></p>
        <?php endif; ?>

        <a href="<?= $filePath ?>" target="_blank" style="display:inline-block; margin-top:5px;">Download</a>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

</main>
</div>
</body>
</html>
