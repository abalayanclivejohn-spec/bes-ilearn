<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['student_id'])) {
    header("Location: ../auth/student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Make sure assignment ID is provided
if (!isset($_POST['assignment_id'])) {
    header("Location: assignments.php");
    exit();
}

$assignment_id = (int)$_POST['assignment_id'];
$drawing_data = $_POST['drawing_data'] ?? null;
$file = $_FILES['submission_file'] ?? null;
$folder = "../uploads/submissions/";

if (!is_dir($folder)) mkdir($folder, 0777, true);

$fileName = null;

// Handle uploaded file (optional)
if ($file && $file['error'] === 0) {
    $fileName = time() . "_" . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $folder . $fileName);
}

// Handle drawing submission (optional)
if ($drawing_data) {
    $img = str_replace('data:image/png;base64,', '', $drawing_data);
    $img = str_replace(' ', '+', $img);
    $fileName = time() . "_drawing.png";
    file_put_contents($folder . $fileName, base64_decode($img));
}

// Ensure at least one submission exists
if (!$fileName) {
    $_SESSION['error'] = "Please upload a file or draw something.";
    header("Location: assignments.php");
    exit();
}

// Save submission to database
$stmt = $pdo->prepare("INSERT INTO submissions (assignment_id, student_id, file_name) VALUES (?, ?, ?)");
$stmt->execute([$assignment_id, $student_id, $fileName]);

$_SESSION['success'] = "Assignment submitted successfully!";
header("Location: assignments.php");
exit();
