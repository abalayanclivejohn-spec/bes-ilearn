<?php
// Start session once at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../includes/db.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch counts
$teacher_count = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$student_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$subject_count = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
$assignment_count = $pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
$material_count = $pdo->query("SELECT COUNT(*) FROM materials")->fetchColumn();
$submission_count = $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | iLearn</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        body { background: #f4f6f9; }
        .dashboard-container { display: flex; min-height: 100vh; }
        .sidebar { width: 220px; background: #1e3c72; color: #fff; padding: 25px; }
        .sidebar h2 { text-align: center; margin-bottom: 25px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { margin: 15px 0; }
        .sidebar ul li a {
            color: #d1d5db; text-decoration: none; display: block; padding: 10px; border-radius: 8px; transition: 0.3s;
        }
        .sidebar ul li a:hover, .sidebar ul li a.active { background: #111827; color: #fff; }
        .sidebar .logout a { background: #991b1b; color: #fff !important; }

        .content { flex: 1; padding: 40px; }
        .content h1 { margin-bottom: 30px; color: #111827; }

        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .card {
            background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            transition: transform 0.3s; text-align: center;
        }
        .card:hover { transform: translateY(-5px); }
        .card h3 { margin-bottom: 10px; color: #1f2937; font-size: 18px; }
        .card p { font-size: 24px; font-weight: 700; color: #2563eb; }
    </style>
</head>
<body>

<div class="dashboard-container">

    <aside class="sidebar">
        <h2>🛠 Admin Panel</h2>
        <div class="profile">
            <img src="../assets/img/admin.jpg" alt="Admin Profile">
            <h3><?= htmlspecialchars($_SESSION['admin_name']) ?></h3>
        </div>
        <ul>
            <li><a class="active" href="index.php">Dashboard</a></li>
            <li><a href="manage_teachers.php">Manage Teachers</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="manage_subjects.php">Manage Subjects</a></li>
            <li><a href="manage_assignments.php">Manage Assignments</a></li>
            <li><a href="manage_materials.php">Manage Materials</a></li>
            <li class="logout"><a href="admin_logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?> 👋</h1>

        <div class="cards">
            <a href="manage_teachers.php" class="card">
                <h3>👩‍🏫 Teachers</h3>
                <p><?= $teacher_count ?> registered</p>
            </a>
            <a href="manage_students.php" class="card">
                <h3>👨‍🎓 Students</h3>
                <p><?= $student_count ?> enrolled</p>
            </a>
            <a href="manage_subjects.php" class="card">
                <h3>📚 Subjects</h3>
                <p><?= $subject_count ?> subjects</p>
            </a>
            <a href="manage_assignments.php" class="card">
                <h3>📝 Assignments</h3>
                <p><?= $assignment_count ?> tasks</p>
            </a>
            <a href="manage_materials.php" class="card">
                <h3>📂 Materials</h3>
                <p><?= $material_count ?> files</p>
            </a>
            <div class="card">
                <h3>📤 Submissions</h3>
                <p><?= $submission_count ?> submitted</p>
            </div>
        </div>
    </main>

</div>

</body>
</html>
