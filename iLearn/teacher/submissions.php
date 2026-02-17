<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Fetch all assignments created by this teacher
$stmt = $pdo->prepare(
    "SELECT a.*, s.subject_name
     FROM assignments a
     JOIN subjects s ON a.subject_id = s.subject_id
     WHERE a.teacher_id = ?
     ORDER BY a.due_date ASC"
);
$stmt->execute([$teacher_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assignment Submissions | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard">

<aside class="sidebar">
    <h2>📘 iLearn</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="subjects.php">Subjects</a></li>
        <li><a href="assignments.php">Assignments</a></li>
        <li><a href="materials.php">Materials</a></li>
        <li><a href="submissions.php" class="active">Submissions</a></li>
        <li><a href="chat.php">Chat</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>📝 Assignment Submissions</h1>

<?php if ($assignments): ?>
    <?php foreach($assignments as $a): ?>
        <div class="card">
            <h3><?= htmlspecialchars($a['title']) ?></h3>
            <p><strong>Subject:</strong> <?= htmlspecialchars($a['subject_name']) ?></p>
            <p><strong>Due:</strong> <?= htmlspecialchars($a['due_date']) ?></p>

            <?php
            // Fetch submissions for this assignment
            $sub_stmt = $pdo->prepare(
                "SELECT sub.*, st.fullname
                 FROM submissions sub
                 JOIN students st ON sub.student_id = st.student_id
                 WHERE sub.assignment_id = ?
                 ORDER BY sub.submitted_at DESC"
            );
            $sub_stmt->execute([$a['assignment_id']]);
            $submissions = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if ($submissions): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>File</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['fullname']) ?></td>
                            <td><a href="../uploads/assignments/<?= $s['file_name'] ?>" target="_blank">Download</a></td>
                            <td><?= $s['submitted_at'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No submissions yet.</p>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No assignments created yet.</p>
<?php endif; ?>

</main>
</div>

</body>
</html>
