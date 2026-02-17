<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch teachers and subjects for select dropdowns
$teachers = $pdo->query("SELECT * FROM teachers ORDER BY fullname ASC")->fetchAll(PDO::FETCH_ASSOC);
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY subject_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle Add Assignment
if (isset($_POST['add_assignment'])) {
    $teacher_id = $_POST['teacher_id'];
    $subject_id = $_POST['subject_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    $stmt = $pdo->prepare("INSERT INTO assignments (teacher_id, subject_id, title, description, due_date) VALUES (?,?,?,?,?)");
    $stmt->execute([$teacher_id, $subject_id, $title, $description, $due_date]);
    header("Location: manage_assignments.php");
    exit();
}

// Handle Delete Assignment
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM assignments WHERE assignment_id = ?")->execute([$id]);
    header("Location: manage_assignments.php");
    exit();
}

// Fetch all assignments with teacher and subject names
$assignments = $pdo->query("
    SELECT a.*, t.fullname AS teacher_name, s.subject_name
    FROM assignments a
    JOIN teachers t ON a.teacher_id = t.teacher_id
    JOIN subjects s ON a.subject_id = s.subject_id
    ORDER BY a.due_date ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Assignments | Admin iLearn</title>
    <link rel="stylesheet" href="../assets/css/admin_panel.css">
    <link rel="icon" type="image/png" href="../assets/img/admin-icon.jpg">
    <style>
        .content h1 { margin-bottom: 20px; }
        .card form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        .card input, .card select, .card textarea { padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; }
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
        <li><a href="manage_subjects.php">Manage Subjects</a></li>
        <li><a class="active" href="manage_assignments.php">Manage Assignments</a></li>
        <li><a href="manage_materials.php">Manage Materials</a></li>
        <li class="logout"><a href="admin_logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>Manage Assignments</h1>

<div class="card">
    <h3>Add New Assignment</h3>
    <form method="post">
        <select name="teacher_id" required>
            <option value="">Select Teacher</option>
            <?php foreach($teachers as $t): ?>
                <option value="<?= $t['teacher_id'] ?>"><?= htmlspecialchars($t['fullname']) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="subject_id" required>
            <option value="">Select Subject</option>
            <?php foreach($subjects as $s): ?>
                <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="title" placeholder="Assignment Title" required>
        <textarea name="description" placeholder="Description (optional)"></textarea>
        <input type="date" name="due_date" required>
        <button type="submit" name="add_assignment">Add Assignment</button>
    </form>

    <h3>All Assignments</h3>
    <?php if ($assignments): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Teacher</th>
                    <th>Subject</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($assignments as $a): ?>
                <tr>
                    <td><?= $a['assignment_id'] ?></td>
                    <td><?= htmlspecialchars($a['title']) ?></td>
                    <td><?= htmlspecialchars($a['teacher_name']) ?></td>
                    <td><?= htmlspecialchars($a['subject_name']) ?></td>
                    <td><?= $a['due_date'] ?></td>
                    <td>
                        <a href="manage_assignments.php?delete=<?= $a['assignment_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No assignments added yet.</p>
    <?php endif; ?>
</div>

</main>
</div>

</body>
</html>
