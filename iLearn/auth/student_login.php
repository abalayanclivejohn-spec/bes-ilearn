<?php
session_start();
require_once "../includes/db.php";

$error = "";

if (isset($_POST['login'])) {
    $student_id = trim($_POST['student_id']); // Changed from email
    $password = $_POST['password'];

    try {
        // Query using student_id instead of email
        $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    if ($student && password_verify($password, $student['password'])) {
        // Store student session variables
        $_SESSION['student_id'] = $student['student_id'];
        $_SESSION['student_name'] = $student['fullname'];

        // Redirect to student dashboard
        header("Location: ../student/dashboard.php");
        exit();
    } else {
        $error = "Invalid student ID or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login | iLearn</title>
    <link rel="stylesheet" href="../assets/css/student_login.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
</head>
<body>

<div class="login-container">
    <h2>👨‍🎓 Student Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="input-group">
            <input type="text" name="student_id" required>
            <label>Student ID</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>

        <button type="submit" name="login">Login</button>

        <p class="link">
            No account? <a href="student_register.php">Register here</a>
        </p>

        <p class="link">
            <a href="../index.php">← Back to Home</a>
        </p>
    </form>
</div>

</body>
</html>
