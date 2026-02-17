<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/teacher_login.php");
    exit();
}

$quiz_id = $_GET['quiz_id'] ?? null;
if (!$quiz_id) die("No quiz selected.");

/* =========================
   FETCH QUIZ
========================= */
$stmt = $pdo->prepare("SELECT title FROM quizzes WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) die("Quiz not found.");

/* =========================
   ADD QUESTION
========================= */
if (isset($_POST['add_question'])) {
    $text = trim($_POST['question_text']);
    $a = $_POST['option_a'];
    $b = $_POST['option_b'];
    $c = $_POST['option_c'] ?: null;
    $d = $_POST['option_d'] ?: null;
    $correct = $_POST['correct_option'];

    if ($text && $a && $b && $correct) {
        $stmt = $pdo->prepare("
            INSERT INTO quiz_questions 
            (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$quiz_id, $text, $a, $b, $c, $d, $correct]);
        header("Location: quiz_questions.php?quiz_id=$quiz_id");
        exit();
    }
}

/* =========================
   DELETE QUESTION
========================= */
if (isset($_GET['delete'])) {
    $qid = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE question_id = ? AND quiz_id = ?");
    $stmt->execute([$qid, $quiz_id]);
}

/* =========================
   FETCH QUESTIONS
========================= */
$stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Questions | iLearn</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="dashboard">
<aside class="sidebar">
    <h2>📘B.E.S. iLearn</h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="quiz.php" class="active">Quizzes</a></li>
        <li class="logout"><a href="../auth/logout.php">Logout</a></li>
    </ul>
</aside>

<main class="content">
<h1>🧠 Manage Questions: <?= htmlspecialchars($quiz['title']) ?></h1>

<form method="post" class="card">
    <textarea name="question_text" placeholder="Question" required></textarea>
    <input type="text" name="option_a" placeholder="Option A" required>
    <input type="text" name="option_b" placeholder="Option B" required>
    <input type="text" name="option_c" placeholder="Option C">
    <input type="text" name="option_d" placeholder="Option D">

    <select name="correct_option" required>
        <option value="">Correct Answer</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
    </select><br><br>

    <button type="submit" name="add_question">Add Question</button>
</form>

<div class="cards">
<?php foreach ($questions as $q): ?>
    <div class="card">
        <p><strong><?= htmlspecialchars($q['question_text']) ?></strong></p>
        <p>A. <?= $q['option_a'] ?></p>
        <p>B. <?= $q['option_b'] ?></p>
        <?php if ($q['option_c']): ?><p>C. <?= $q['option_c'] ?></p><?php endif; ?>
        <?php if ($q['option_d']): ?><p>D. <?= $q['option_d'] ?></p><?php endif; ?>
        <p><strong>Correct:</strong> <?= $q['correct_option'] ?></p>

        <a href="?quiz_id=<?= $quiz_id ?>&delete=<?= $q['question_id'] ?>" style="color:red;">Delete</a>
    </div>
<?php endforeach; ?>
</div>

</main>
</div>
</body>
</html>
