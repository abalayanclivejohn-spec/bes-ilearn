<?php
require_once "../includes/db.php";

$message = "";
$error = "";

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check = $pdo->prepare("SELECT teacher_id FROM teachers WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $error = "Email already registered!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO teachers (fullname, email, password) VALUES (?, ?, ?)"
            );
            $stmt->execute([$fullname, $email, $hashed]);

            $message = "Registration successful! You may now login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Register | iLearn</title>
    <link rel="stylesheet" href="../assets/css/register.css">
    <link rel="icon" type="image/png" href="../assets/img/fav.jpg">
</head>
<body>

<div class="register-container">
    <h2>📝 Teacher Registration</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="success"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
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
            Already have an account?
            <a href="teacher_login.php">Login here</a>
        </p>

        <p class="link">
            <a href="../index.php">← Back to Home</a>
        </p>
    </form>
</div>

</body>
</html>
