<?php
require_once "../includes/db.php"; // your PDO connection

$error = '';
$success = '';

if (isset($_POST['create'])) {
    $fullname = trim($_POST['fullname']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Hash the password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO admins (fullname, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$fullname, $email, $hashed])) {
            $success = "Admin account created successfully!";
        } else {
            $error = "Failed to create admin!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Admin | iLearn</title>
    <link rel="stylesheet" href="../assets/css/admin_login.css">
</head>
<body>

<div class="login-container">
    <h2>Create Admin</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" name="create">Create Admin</button>
    </form>
</div>

</body>
</html>
