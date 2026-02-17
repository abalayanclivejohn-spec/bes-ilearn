<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$teacher_id = $_SESSION['teacher_id'];

if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
    $fileName = $_FILES['profile_pic']['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg','jpeg','png','gif'];

    if(in_array($fileExt, $allowedExts)) {
        $newFileName = "teacher_{$teacher_id}.".$fileExt;
        $uploadDir = "../uploads/profiles/";
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $destPath = $uploadDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $destPath)) {
            $stmt = $pdo->prepare("UPDATE teachers SET profile_pic = ? WHERE teacher_id = ?");
            $stmt->execute(["uploads/profiles/".$newFileName, $teacher_id]);
        }
    }
}

header("Location: dashboard.php");
exit;
?>
