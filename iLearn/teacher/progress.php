<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$teacher_id = $_SESSION['teacher_id'];

/* =========================
   FETCH STUDENT PROGRESS
========================= */
$stmt = $pdo->prepare("
    SELECT 
        st.student_id,
        st.fullname,

        -- Quizzes
        COUNT(DISTINCT qa.attempt_id) AS quizzes_completed,
        COUNT(DISTINCT q.quiz_id) AS total_quizzes,

        -- Activities
        SUM(CASE WHEN smp.is_completed=1 THEN 1 ELSE 0 END) AS activities_completed,
        COUNT(DISTINCT m.material_id) AS total_activities,

        -- Assignments
        COUNT(DISTINCT sub.submission_id) AS assignments_submitted,
        COUNT(DISTINCT a.assignment_id) AS total_assignments

    FROM students st
    LEFT JOIN quiz_attempts qa ON st.student_id = qa.student_id
    LEFT JOIN quizzes q ON qa.quiz_id = q.quiz_id

    LEFT JOIN student_material_progress smp ON st.student_id = smp.student_id
    LEFT JOIN materials m ON smp.material_id = m.material_id

    LEFT JOIN submissions sub ON st.student_id = sub.student_id
    LEFT JOIN assignments a ON sub.assignment_id = a.assignment_id

    GROUP BY st.student_id
");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Progress | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
    .progress-bar-container {
        background: #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        height: 18px;
        width: 100%;
        margin-top:5px;
    }
    .progress-bar {
        height: 100%;
        border-radius: 12px;
        background: linear-gradient(90deg,#3b82f6,#8b5cf6);
        text-align: right;
        padding-right:5px;
        color:#fff;
        font-size:0.75rem;
        line-height:18px;
        transition: width 0.4s ease-in-out;
    }
    .status-good { color:#10b981; font-weight:600; }
    .status-bad { color:#ef4444; font-weight:600; }
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
    <h1>📊 Student Progress</h1>

    <div class="cards">
        <?php foreach ($students as $s): 
            $quizPercent = ($s['total_quizzes']>0) ? round(($s['quizzes_completed']/$s['total_quizzes'])*100) : 0;
            $activityPercent = ($s['total_activities']>0) ? round(($s['activities_completed']/$s['total_activities'])*100) : 0;
            $assignmentPercent = ($s['total_assignments']>0) ? round(($s['assignments_submitted']/$s['total_assignments'])*100) : 0;

            $status = ($quizPercent>=75 && $activityPercent>=75 && $assignmentPercent>=75) ? "On Track" : "At Risk";
            $statusClass = ($status=="On Track") ? "status-good" : "status-bad";
        ?>
        <div class="card">
            <h3><?= htmlspecialchars($s['fullname']) ?></h3>

            <p><strong>Quizzes Completed:</strong> <?= $s['quizzes_completed'] ?>/<?= $s['total_quizzes'] ?></p>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width:<?= $quizPercent ?>%"><?= $quizPercent ?>%</div>
            </div>

            <p><strong>Activities Completed:</strong> <?= $s['activities_completed'] ?>/<?= $s['total_activities'] ?></p>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width:<?= $activityPercent ?>%"><?= $activityPercent ?>%</div>
            </div>

            <p><strong>Assignments Completed:</strong> <?= $s['assignments_submitted'] ?>/<?= $s['total_assignments'] ?></p>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width:<?= $assignmentPercent ?>%"><?= $assignmentPercent ?>%</div>
            </div>

            <p>Status: <span class="<?= $statusClass ?>"><?= $status ?></span></p>
        </div>
        <?php endforeach; ?>
    </div>

</main>
</div>
</body>
</html>
