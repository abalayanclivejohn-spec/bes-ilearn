<?php
session_start();
require_once "../includes/db.php";

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin data
$stmt = $pdo->prepare("SELECT * FROM admins WHERE admin_id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch counts
$teacher_count = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$student_count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$subject_count = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
$assignment_count = $pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
$submission_count = $pdo->query("SELECT COUNT(*) FROM submissions")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | iLearn</title>
    <link rel="stylesheet" href="../assets/css/admin_panel.css">
    <link rel="icon" type="image/png" href="../assets/img/admin-icon.jpg">
</head>
<body>

<div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <<div class="profile">
    <img src="../assets/img/admin.jpg" alt="Admin Profile">
    <h3><?= htmlspecialchars($admin['fullname']) ?></h3>
</div>


        <ul>
            <li><a class="active" href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="manage_teachers.php">Manage Teachers</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="manage_subjects.php">Manage Subjects</a></li>
            <li><a href="manage_assignments.php">Manage Assignments</a></li>
            <li><a href="manage_materials.php">Manage Materials</a></li>
            <li class="logout"><a href="admin_logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="content" style="background-image: url('../assets/img/<?= htmlspecialchars($admin['background_image']) ?>');">
        <!-- Sidebar toggle button -->
        <button id="sidebarToggle" class="toggle-btn">☰</button>

        <h1>Welcome, <?= htmlspecialchars($admin['fullname']) ?> 👋</h1>

        <div class="cards">
            <div class="card">
                <h3>Teachers</h3>
                <p><?= $teacher_count ?></p>
            </div>
            <div class="card">
                <h3>Students</h3>
                <p><?= $student_count ?></p>
            </div>
            <div class="card">
                <h3>Subjects</h3>
                <p><?= $subject_count ?></p>
            </div>
            <div class="card">
                <h3>Assignments</h3>
                <p><?= $assignment_count ?></p>
            </div>
            <div class="card">
                <h3>Submissions</h3>
                <p><?= $submission_count ?></p>
            </div>
        </div>

       
</div>

<script>
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const content = document.querySelector('.content');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
    });
</script>

</body>
</html>
