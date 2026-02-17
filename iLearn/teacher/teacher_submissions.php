<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Check teacher login
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Get assignments for this teacher
$stmt = $pdo->prepare("
    SELECT assignment_id, title
    FROM assignments
    WHERE teacher_id = ?
    ORDER BY due_date ASC
");
$stmt->execute([$teacher_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If a specific assignment is selected, fetch its submissions
$selected_assignment_id = $_GET['assignment_id'] ?? null;
$submissions = [];

if ($selected_assignment_id) {
    $stmt = $pdo->prepare("
        SELECT sub.*, st.fullname AS student_name, a.title AS assignment_title
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Submissions | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard">

    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>📘 iLearn</h2>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="subjects.php">Subjects</a></li>
            <li><a href="assignments.php">Assignments</a></li>
            <li><a href="materials.php">Materials</a></li>
            <li><a href="modules.php">Modules</a></li>
            <li><a class="active" href="teacher_submissions.php">Submissions</a></li>
            <li class="logout"><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="content">
        <h1>📄 Student Submissions</h1>

        <!-- Assignments list -->
        <div class="cards">
            <?php if ($assignments): ?>
                <?php foreach ($assignments as $a): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($a['title']) ?></h3>
                        <a href="?assignment_id=<?= $a['assignment_id'] ?>">View Submissions</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No assignments created yet.</p>
            <?php endif; ?>
        </div>

        <!-- Submissions list -->
        <?php if ($selected_assignment_id): ?>
            <h2>Submissions for Assignment: <?= htmlspecialchars($submissions[0]['assignment_title'] ?? '') ?></h2>
            <div class="cards">
                <?php if ($submissions): ?>
                    <?php foreach ($submissions as $sub): ?>
                        <div class="card">
                            <p><strong>Student:</strong> <?= htmlspecialchars($sub['student_name']) ?></p>
                            <p><strong>Submitted at:</strong> <?= $sub['submitted_at'] ?></p>
                            <a href="../uploads/submissions/<?= htmlspecialchars($sub['file_name']) ?>" target="_blank">Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No submissions yet for this assignment.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

</body>
</html>
