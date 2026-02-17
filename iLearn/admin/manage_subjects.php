<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Add Subject
if (isset($_POST['add_subject'])) {
    $name = trim($_POST['subject_name']);
    $desc = trim($_POST['description']);

    $stmt = $pdo->prepare("INSERT INTO subjects (subject_name, description) VALUES (?, ?)");
    $stmt->execute([$name, $desc]);
    header("Location: manage_subjects.php");
    exit();
}

// Handle Delete Subject
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM subjects WHERE subject_id = ?")->execute([$id]);
    header("Location: manage_subjects.php");
    exit();
}

// Fetch all subjects
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects | Admin iLearn</title>
    <link rel="stylesheet" href="../assets/css/admin_panel.css">
    <link rel="icon" type="image/png" href="../assets/img/admin-icon.jpg">
    <style>
        .content h1 { margin-bottom: 20px; }
        .card form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        .card input, .card textarea { padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; }
        .card button { padding: 10px 20px; border-radius: 25px; border: none; background: #2563eb; color: #fff; cursor: pointer; }
        .card button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        table th { background: #2563eb; color: #fff; }
        table a { color: #e11d48; text-decoration: none; font-weight: 600; }
        table a:hover { text-decoration: underline; }
        textarea { resize: vertical; min-height: 50px; }
    </style>
</head>
<body>

<div class="dashboard-container">

<aside class="sidebar">
    <h2>🛠 Admin Panel</h2>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_teachers.php">Manage Teachers</a></li>
        <li><a href="manage_students.php">Manage Students</a></li>
        <li><a class="active" href="manage_subjects.php">Manage Subjects</a></li>
        <li><a href="manage_assignments.php">Manage Assignments</a></li>
        <li><a href="manage_materials.php">Manage Materials</a></li>
        <li class="logout"><a href="admin_logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>Manage Subjects</h1>

<div class="card">
    <h3>Add New Subject</h3>
    <form method="post">
        <input type="text" name="subject_name" placeholder="Subject Name" required>
        <textarea name="description" placeholder="Description (optional)"></textarea>
        <button type="submit" name="add_subject">Add Subject</button>
    </form>

    <h3>All Subjects</h3>
    <?php if ($subjects): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subjects as $s): ?>
                <tr>
                    <td><?= $s['subject_id'] ?></td>
                    <td><?= htmlspecialchars($s['subject_name']) ?></td>
                    <td><?= $s['created_at'] ?></td>
                    <td>
                        <a href="manage_subjects.php?delete=<?= $s['subject_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No subjects added yet.</p>
    <?php endif; ?>
</div>

</main>
</div>

</body>
</html>
