<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Add Student
if (isset($_POST['add_student'])) {
    $student_id = trim($_POST['student_id']); // New student ID field
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO students (student_id, fullname, email, password) VALUES (?,?,?,?)");
    $stmt->execute([$student_id, $fullname, $email, $password]);
    header("Location: manage_students.php");
    exit();
}

// Handle Delete Student
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM students WHERE student_id = ?")->execute([$id]);
    header("Location: manage_students.php");
    exit();
}

// Fetch all students
$students = $pdo->query("SELECT * FROM students ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students | Admin iLearn</title>
    <link rel="stylesheet" href="../assets/css/admin_panel.css">
    <link rel="icon" type="image/png" href="../assets/img/admin-icon.jpg">
    <style>
        .content h1 { margin-bottom: 20px; }
        .card form { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .card input { padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; }
        .card button { padding: 10px 20px; border-radius: 25px; border: none; background: #16a34a; color: #fff; cursor: pointer; }
        .card button:hover { background: #15803d; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        table th { background: #16a34a; color: #fff; }
        table a { color: #e11d48; text-decoration: none; font-weight: 600; }
        table a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="dashboard-container">

<aside class="sidebar">
    <h2>🛠 Admin Panel</h2>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_teachers.php">Manage Teachers</a></li>
        <li><a class="active" href="manage_students.php">Manage Students</a></li>
        <li><a href="manage_subjects.php">Manage Subjects</a></li>
        <li><a href="manage_assignments.php">Manage Assignments</a></li>
        <li><a href="manage_materials.php">Manage Materials</a></li>
        <li class="logout"><a href="admin_logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>Manage Students</h1>

<div class="card">
    <h3>Add New Student</h3>
    <form method="post">
        <input type="text" name="student_id" placeholder="Student ID" required>
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email (optional)">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="add_student">Add Student</button>
    </form>

    <h3>All Students</h3>
    <?php if ($students): ?>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['student_id']) ?></td>
                    <td><?= htmlspecialchars($s['fullname']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= $s['created_at'] ?></td>
                    <td>
                        <a href="manage_students.php?delete=<?= $s['student_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No students added yet.</p>
    <?php endif; ?>
</div>

</main>
</div>

</body>
</html>
