<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Fetch teacher info
$stmt = $pdo->prepare("SELECT fullname, profile_pic FROM teachers WHERE teacher_id = ?");
$stmt->execute([$_SESSION['teacher_id']]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <!-- Favicon for the browser tab -->
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    
    <!-- Optional: Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
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
        <li><a href="learning.php" class="<?= $currentPage == 'learning.php' ? 'active' : '' ?>">Basic Learnings</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        </ul>
    </aside>

    <main class="content">
        <h1>Welcome, <?= htmlspecialchars($teacher['fullname']) ?> 👋</h1>

        <div class="cards">
            <a href="subjects.php" class="card">
                <h3>📚 Subjects</h3>
                <p>Manage your subjects</p>
            </a>

            <a href="assignments.php" class="card">
                <h3>📝 Assignments</h3>
                <p>Create & assign tasks</p>
            </a>

            <a href="materials.php" class="card">
                <h3>📂 Activities</h3>
                <p>Upload learning files</p>
            </a>

            <a href="modules.php" class="card">
                <h3>🧩 Modules</h3>
                <p>Manage learning modules</p>
            </a>

            <a href="quiz.php" class="card"> 
                <h3>📚 Quizzes</h3>
                <p>Manage your Quizzes</p>
            </a>

            <a href="progress.php" class="card">
                <h3>📚 Student Progress</h3>
                <p>Manage your Student Progress</p>
            </a>
        </div>
    </main>

</div>

</body>
</html>
