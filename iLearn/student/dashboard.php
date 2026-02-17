<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_name = $_SESSION['student_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | iLearn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* CARD GRID */
        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            flex: 1 1 200px;
            background: #f4f4f4;
            color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Colors for each card */
        .card.assignments { background: #6c63ff; }
        .card.materials    { background: #00bcd4; }
        .card.subjects     { background: #ff9800; }
        .card.modules      { background: #8bc34a; }

        .card h3 {
            margin-top: 0;
            font-size: 1.4rem;
        }

        .card p {
            font-size: 1rem;
            margin: 10px 0 15px;
        }

        .card .btn {
            display: inline-block;
            background: rgba(255,255,255,0.9);
            color: #000;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .card .btn:hover {
            background: #fff;
        }

        /* Optional: make dashboard content scrollable if needed */
        main.content {
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="dashboard">

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

    <!-- MAIN CONTENT -->
    <main class="content">
        <h1>Welcome, <?= htmlspecialchars($student_name) ?> 👋</h1>

        <div class="cards">
            <div class="card assignments">
                <h3>📝 Assignments</h3>
                <p>View & submit your assignments</p>
                <a href="assignments.php" class="btn">Go</a>
            </div>
            <div class="card materials">
                <h3>📂 Materials</h3>
                <p>Access PDFs, Docs, Worksheets, and Modules</p>
                <a href="materials.php" class="btn">Go</a>
            </div>
            <div class="card subjects">
                <h3>📚 Subjects</h3>
                <p>View your enrolled subjects</p>
                <a href="subjects.php" class="btn">Go</a>
            </div>
            <div class="card modules">
                <h3>🧩 Modules</h3>
                <p>Access learning modules</p>
                <a href="modules.php" class="btn">Go</a>
            </div>
        </div>
    </main>

</div>

</body>
</html>
