<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Make sure the teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Check if submission ID is provided
if (!isset($_GET['id'])) {
    header("Location: materials.php");
    exit();
}

$submission_id = (int)$_GET['id'];

// Fetch the submission and verify it belongs to this teacher
$stmt = $pdo->prepare("
    SELECT sub.*, a.teacher_id, a.file_name, a.activity_id
    FROM submissions sub
    JOIN activities a ON sub.activity_id = a.activity_id
    WHERE sub.submission_id = ? AND a.teacher_id = ?
");
$stmt->execute([$submission_id, $teacher_id]);
$sub = $stmt->fetch();

if ($sub) {
    // Delete file from server
    $filePath = "../uploads/submissions/" . $sub['file_name'];
    if (file_exists($filePath)) unlink($filePath);

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM submissions WHERE submission_id = ?");
    $stmt->execute([$submission_id]);
}

header("Location: materials.php");
exit();
