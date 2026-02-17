<?php
require_once "../includes/db.php";

$message = "";
$error = "";

if (isset($_POST['register'])) {
    $student_id = trim($_POST['student_id']); // New Student ID field
    $fullname   = trim($_POST['fullname']);
    $email      = trim($_POST['email']);      // Optional, can be left empty
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];

    // Check password confirmation
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Check if student ID already exists
        $check = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $check->execute([$student_id]);

        if ($check->rowCount() > 0) {
            $error = "Student ID already registered!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO students (student_id, fullname, email, password) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$student_id, $fullname, $email, $hashed]);
            $message = "Registration successful! You may now login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Register | iLearn</title>
    <link rel="stylesheet" href="../assets/css/student_register.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
</head>
<body>

<div class="register-container">
    <h2>📝 Student Registration</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="input-group">
            <input type="text" name="student_id" required>
            <label>Student ID</label>
        </div>

        <div class="input-group">
            <input type="text" name="fullname" required>
            <label>Full Name</label>
        </div>
        
        <div class="input-group">
            <input type="email" name="email" required>
            <label>Email Address</label>
        </div>
        
        <div class="input-group">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>

        <div class="input-group">
            <input type="password" name="confirm_password" required>
            <label>Confirm Password</label>
        </div>

        <button type="submit" name="register">Register</button>

        <p class="link">
            Already have an account? <a href="student_login.php">Login here</a>
        </p>

        <p class="link">
            <a href="../index.php">← Back to Home</a>
        </p>
    </form>
</div>

</body>
</html>
