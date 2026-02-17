<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$teacher_id = $_SESSION['teacher_id'];

/* =========================
   DELETE QUIZ
========================= */
if (isset($_GET['delete'])) {
    $quiz_id = (int)$_GET['delete'];
    
    // Delete related answers and attempts if needed
    $pdo->prepare("DELETE FROM quiz_answers WHERE attempt_id IN (SELECT attempt_id FROM quiz_attempts WHERE quiz_id = ?)")->execute([$quiz_id]);
    $pdo->prepare("DELETE FROM quiz_attempts WHERE quiz_id = ?")->execute([$quiz_id]);
    $pdo->prepare("DELETE FROM quizzes WHERE quiz_id = ? AND teacher_id = ?")->execute([$quiz_id, $teacher_id]);

    header("Location: quiz.php");
    exit();
}

/* =========================
   ADD OR EDIT QUIZ
========================= */
if (isset($_POST['save_quiz'])) {
    $quiz_id = $_POST['quiz_id'] ?? null;
    $subject_id = $_POST['subject_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    if ($quiz_id) {
        // Update existing quiz
        $stmt = $pdo->prepare("UPDATE quizzes SET subject_id=?, title=?, description=?, due_date=? WHERE quiz_id=? AND teacher_id=?");
        $stmt->execute([$subject_id, $title, $description, $due_date, $quiz_id, $teacher_id]);

        // Remove old questions
        $pdo->prepare("DELETE FROM quiz_questions WHERE quiz_id=?")->execute([$quiz_id]);
    } else {
        // Insert new quiz
        $stmt = $pdo->prepare("INSERT INTO quizzes (subject_id, title, description, due_date, teacher_id) VALUES (?,?,?,?,?)");
        $stmt->execute([$subject_id, $title, $description, $due_date, $teacher_id]);
        $quiz_id = $pdo->lastInsertId();
    }

    // Add questions
    $questions = $_POST['questions'];
    foreach ($questions as $q) {
        $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([
            $quiz_id,
            $q['question_text'],
            $q['option_a'],
            $q['option_b'],
            $q['option_c'] ?? null,
            $q['option_d'] ?? null,
            $q['correct_option']
        ]);
    }

    header("Location: quiz.php");
    exit();
}

/* =========================
   FETCH SUBJECTS
========================= */
$subjectsStmt = $pdo->prepare("SELECT * FROM subjects WHERE teacher_id=?");
$subjectsStmt->execute([$teacher_id]);
$subjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FETCH QUIZZES
========================= */
$stmt = $pdo->prepare("
    SELECT q.*, s.subject_name
    FROM quizzes q
    JOIN subjects s ON q.subject_id = s.subject_id
    WHERE s.teacher_id=?
    ORDER BY q.due_date ASC
");
$stmt->execute([$teacher_id]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quizzes | Teacher</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="icon" href="../assets/img/fav.jpg">
    <style>
        .card { background:#fff; padding:15px; border-radius:8px; margin-bottom:15px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
        input, select, textarea { width:100%; padding:6px; margin-bottom:10px; }
        button { padding:8px 15px; border:none; border-radius:5px; cursor:pointer; }
        .quiz-actions { margin-top:10px; }
        .question-block { border:1px solid #ddd; padding:10px; border-radius:5px; margin-bottom:10px; }
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
    <h1>📝 Quizzes</h1>

    <!-- ADD NEW QUIZ -->
    <form method="post" class="card">
        <h3>Create / Edit Quiz</h3>
        <select name="subject_id" required>
            <option value="">Select Subject</option>
            <?php foreach ($subjects as $s): ?>
                <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="title" placeholder="Quiz Title" required>
        <input type="date" name="due_date" required>

        <h4>Questions (Multiple Choice)</h4>
        <div id="questions-wrapper">
            <div class="question-block">
                <input type="text" name="questions[0][question_text]" placeholder="Question" required>
                <input type="text" name="questions[0][option_a]" placeholder="Option A" required>
                <input type="text" name="questions[0][option_b]" placeholder="Option B" required>
                <input type="text" name="questions[0][option_c]" placeholder="Option C">
                <input type="text" name="questions[0][option_d]" placeholder="Option D">
                <select name="questions[0][correct_option]" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="addQuestion()">+ Add Another Question</button><br><br>
        <button type="submit" name="save_quiz">Save Quiz</button>
    </form>

    <!-- QUIZ LIST -->
    <div class="cards">
        <?php foreach ($quizzes as $q): ?>
        <div class="card">
            <h3><?= htmlspecialchars($q['title']) ?></h3>
            <p><strong>Subject:</strong> <?= htmlspecialchars($q['subject_name']) ?></p>
            <p><strong>Due:</strong> <?= $q['due_date'] ?></p>
            <p><?= nl2br(htmlspecialchars($q['description'])) ?></p>

            <div class="quiz-actions">
                <a href="?delete=<?= $q['quiz_id'] ?>" onclick="return confirm('Delete this quiz?')" style="color:red;">Delete</a> |
                <a href="quiz_results.php?quiz_id=<?= $q['quiz_id'] ?>">View Results</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>
</div>

<script>
let questionIndex = 1;
function addQuestion() {
    const wrapper = document.getElementById('questions-wrapper');
    const block = document.createElement('div');
    block.className = 'question-block';
    block.innerHTML = `
        <input type="text" name="questions[${questionIndex}][question_text]" placeholder="Question" required>
        <input type="text" name="questions[${questionIndex}][option_a]" placeholder="Option A" required>
        <input type="text" name="questions[${questionIndex}][option_b]" placeholder="Option B" required>
        <input type="text" name="questions[${questionIndex}][option_c]" placeholder="Option C">
        <input type="text" name="questions[${questionIndex}][option_d]" placeholder="Option D">
        <select name="questions[${questionIndex}][correct_option]" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>
    `;
    wrapper.appendChild(block);
    questionIndex++;
}
</script>
</body>
</html>
