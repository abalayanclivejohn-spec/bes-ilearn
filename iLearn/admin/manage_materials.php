<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch subjects for select dropdown
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY subject_name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle Add Material
if (isset($_POST['add_material'])) {
    $title = trim($_POST['title']);
    $subject_id = $_POST['subject_id'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $file = $_FILES['file'];
        $filename = time() . "_" . basename($file['name']);
        $target = "../uploads/materials/" . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            $stmt = $pdo->prepare("INSERT INTO materials (title, subject_id, file_name) VALUES (?, ?, ?)");
            $stmt->execute([$title, $subject_id, $filename]);
            header("Location: manage_materials.php");
            exit();
        } else {
            $error = "Failed to upload file.";
        }
    } else {
        $error = "Please select a file to upload.";
    }
}

// Handle Delete Material
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $file = $pdo->prepare("SELECT file_name FROM materials WHERE material_id = ?")->execute([$id]);
    $filename = $pdo->query("SELECT file_name FROM materials WHERE material_id = $id")->fetchColumn();
    if ($filename && file_exists("../uploads/materials/$filename")) {
        unlink("../uploads/materials/$filename");
    }
    $pdo->prepare("DELETE FROM materials WHERE material_id = ?")->execute([$id]);
    header("Location: manage_materials.php");
    exit();
}

// Fetch all materials
$materials = $pdo->query("
    SELECT m.*, s.subject_name
    FROM materials m
    JOIN subjects s ON m.subject_id = s.subject_id
    ORDER BY m.uploaded_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Materials | Admin iLearn</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" type="image/png" href="../assets/img/admin-icon.jpg">
    <style>
        .content h1 { margin-bottom: 20px; }
        .card form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        .card input, .card select { padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; }
        .card button { padding: 10px 20px; border-radius: 25px; border: none; background: #2563eb; color: #fff; cursor: pointer; }
        .card button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        table th { background: #2563eb; color: #fff; }
        table a { color: #e11d48; text-decoration: none; font-weight: 600; }
        table a:hover { text-decoration: underline; }
        .error { color: red; margin-bottom: 10px; }
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
        <li><a href="manage_assignments.php">Manage Assignments</a></li>
        <li><a class="active" href="manage_materials.php">Manage Materials</a></li>
        <li class="logout"><a href="admin_logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>Manage Materials</h1>

<div class="card">

    <h3>Add New Material</h3>
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Material Title" required>
        <select name="subject_id" required>
            <option value="">Select Subject</option>
            <?php foreach($subjects as $s): ?>
                <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="file" required>
        <button type="submit" name="add_material">Upload Material</button>
    </form>

    <h3>All Materials</h3>
    <?php if ($materials): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>File</th>
                    <th>Uploaded At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($materials as $m): ?>
                <tr>
                    <td><?= $m['material_id'] ?></td>
                    <td><?= htmlspecialchars($m['title']) ?></td>
                    <td><?= htmlspecialchars($m['subject_name']) ?></td>
                    <td><a href="../uploads/materials/<?= $m['file_name'] ?>" target="_blank">Download</a></td>
                    <td><?= $m['uploaded_at'] ?></td>
                    <td>
                        <a href="manage_materials.php?delete=<?= $m['material_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No materials uploaded yet.</p>
    <?php endif; ?>

</div>

</main>
</div>

</body>
</html>
