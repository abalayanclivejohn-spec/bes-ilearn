<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$teacher_id = $_SESSION['teacher_id'];

// Add subject
if (isset($_POST['add_subject'])) {
    $subject_name = trim($_POST['subject_name']);

    if (!empty($subject_name)) {
        $stmt = $pdo->prepare(
            "INSERT INTO subjects (teacher_id, subject_name) VALUES (?, ?)"
        );
        $stmt->execute([$teacher_id, $subject_name]);
    }
}

// Delete subject
if (isset($_GET['delete'])) {
    $subject_id = (int)$_GET['delete'];

    $stmt = $pdo->prepare(
        "DELETE FROM subjects WHERE subject_id = ? AND teacher_id = ?"
    );
    $stmt->execute([$subject_id, $teacher_id]);
}

// Fetch subjects
$stmt = $pdo->prepare("
    SELECT * FROM subjects 
    WHERE teacher_id = ?
");
$stmt->execute([$teacher_id]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subjects | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    
    <!-- Favicon for the browser tab -->
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    
    <!-- Optional: Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="dashboard">

    <!-- Sidebar -->
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

    <!-- Content -->
    <main class="content">
        <h1>📚 My Subjects</h1>

        <!-- Add Subject -->
        <form method="post" class="card">
            <h3>Add New Subject</h3>
            <input type="text" name="subject_name" placeholder="Subject Name" required>
            <br><br>
            <button type="submit" name="add_subject">Add Subject</button>
        </form>

        <!-- Subjects List -->
        <div class="cards">
            <?php if (count($subjects) > 0): ?>
                <?php foreach ($subjects as $subject): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($subject['subject_name']) ?></h3>
                        <a href="?delete=<?= $subject['subject_id'] ?>"
                           onclick="return confirm('Delete this subject?')"
                           style="color:#991b1b;font-weight:bold;">
                           Delete
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No subjects added yet.</p>
            <?php endif; ?>
        </div>
    </main>

</div>

</body>
</html>
