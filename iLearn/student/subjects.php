<?php
session_start();
require_once "../includes/db.php";

// ---------------------------
// STUDENT SESSION CHECK
// ---------------------------
if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'] ?? "";

// ---------------------------
// FETCH SUBJECTS ASSIGNED TO STUDENT
// If no assignments table yet, fetch all subjects
// ---------------------------
try {
    $stmt = $pdo->prepare("
        SELECT s.subject_id, s.subject_name, t.fullname AS teacher_name
        FROM subjects s
        JOIN teachers t ON s.teacher_id = t.teacher_id
        -- If you have a student_subjects table:
        -- JOIN student_subjects ss ON s.subject_id = ss.subject_id
        -- WHERE ss.student_id = ?
        ORDER BY s.subject_name ASC
    ");
    // For now, we fetch all subjects
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subjects | iLearn Student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="dashboard">

    <!-- Sidebar -->
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

    <!-- Main Content -->
    <main class="content">
        <h1>📚 Subjects</h1>

        <div class="cards">
            <?php if (!empty($subjects)): ?>
                <?php foreach ($subjects as $sub): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($sub['subject_name']) ?></h3>
                        <p><strong>Teacher:</strong> <?= htmlspecialchars($sub['teacher_name']) ?></p>
                        <a href="modules.php?subject_id=<?= $sub['subject_id'] ?>"> Modules</a>
                        <a href="materials.php?subject_id=<?= $sub['subject_id'] ?>">Activities</a>
                        <a href="assignments.php?subject_id=<?= $sub['subject_id'] ?>"> Assignments</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No subjects available.</p>
            <?php endif; ?>
        </div>

    </main>
</div>

</body>
</html>
