<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['upload_bg']) && isset($_FILES['background'])) {
    $file = $_FILES['background'];
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Invalid file type!");
    }

    $new_name = 'bg_'.time().'.'.$ext;
    $target = "../assets/img/".$new_name;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $stmt = $pdo->prepare("UPDATE admins SET background_image = ? WHERE admin_id = ?");
        $stmt->execute([$new_name, $_SESSION['admin_id']]);
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Failed to upload background!");
    }
}
<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['upload_bg']) && isset($_FILES['background'])) {
    $file = $_FILES['background'];
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Invalid file type!");
    }

    $new_name = 'bg_'.time().'.'.$ext;
    $target = "../assets/img/".$new_name;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $stmt = $pdo->prepare("UPDATE admins SET background_image = ? WHERE admin_id = ?");
        $stmt->execute([$new_name, $_SESSION['admin_id']]);
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Failed to upload background!");
    }
}
