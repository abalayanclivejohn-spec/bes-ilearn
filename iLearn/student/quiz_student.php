<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$quiz_id = $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    die("No quiz selected.");
}

/* =========================
   FETCH QUIZ INFO
========================= */
$stmt = $pdo->prepare("SELECT q.*, s.subject_name FROM quizzes q JOIN subjects s ON q.subject_id = s.subject_id WHERE q.quiz_id=?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();
if (!$quiz) die("Quiz not found.");

/* =========================
   FETCH QUESTIONS
========================= */
$stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id=?");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   HANDLE SUBMISSION
========================= */
if (isset($_POST['submit_quiz'])) {
    $answers = $_POST['answers'] ?? [];
    $total = count($questions);

    if ($total == 0) {
        echo "<script>alert('This quiz has no questions yet. Please contact your teacher.'); window.location='quizzes.php';</script>";
        exit();
    }

    if (count($answers) < $total) {
        echo "<script>alert('Please answer all questions before submitting.');</script>";
    } else {
        $score = 0;

        foreach ($questions as $q) {
            $qid = $q['question_id'];
            if (isset($answers[$qid]) && $answers[$qid] === $q['correct_option']) {
                $score++;
            }
        }

        $percentage = round(($score / $total) * 100, 2);

        // Save attempt
        $stmt = $pdo->prepare("INSERT INTO quiz_attempts (quiz_id, student_id, score, attempted_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$quiz_id, $student_id, $percentage]);
        $attempt_id = $pdo->lastInsertId();

        // Save answers
        foreach ($answers as $qid => $selected) {
            $stmt = $pdo->prepare("INSERT INTO quiz_answers (attempt_id, question_id, selected_option) VALUES (?, ?, ?)");
            $stmt->execute([$attempt_id, $qid, $selected]);
        }

        echo "<script>alert('Quiz submitted! Your score: $percentage%'); window.location='quizzes.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quizzes | iLearn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/student.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
    <style>
       .card { 
    background:#2607; 
    padding:20px; 
    margin-bottom:15px; 
    border-radius:10px; 
    box-shadow:0 2px 8px rgba(117, 0, 0, 0.1); 
    color:#000;              /* all text black */
}

label { 
    display:block; 
    margin-bottom:6px; 
    cursor:pointer; 
    color:#fff;              /* black label text */
}

button { 
    padding:10px 20px; 
    border:none; 
    border-radius:8px; 
    background:#3b82f6;      /* keep blue button */
    color:#000;              /* black text on button */
    cursor:pointer; 
    font-weight:600; 
}
    </style>
</head>
<body>
<div class="dashboard">
<main class="content">
<h1>📋 <?= htmlspecialchars($quiz['title']) ?></h1>
<p><strong>Subject:</strong> <?= htmlspecialchars($quiz['subject_name']) ?></p>
<p><strong>Due:</strong> <?= $quiz['due_date'] ?></p>

<?php if (!$questions): ?>
    <p>No questions yet. Please contact your teacher.</p>
<?php else: ?>
<form method="post">
    <?php foreach ($questions as $idx => $q): ?>
        <div class="card">
            <p><strong>Q<?= $idx + 1 ?>:</strong> <?= htmlspecialchars($q['question_text']) ?></p>

            <?php
            // Loop through each option dynamically
            $options = ['A','B','C','D'];
            foreach ($options as $opt):
                $opt_text = $q['option_'.strtolower($opt)];
                if ($opt_text):
            ?>
                <label>
                    <input type="radio" name="answers[<?= $q['question_id'] ?>]" value="<?= $opt ?>" required>
                    <?= htmlspecialchars($opt_text) ?>
                </label>
            <?php
                endif;
            endforeach;
            ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" name="submit_quiz">Submit Quiz</button>
</form>
<?php endif; ?>

</main>
</div>
</body>
</html>
