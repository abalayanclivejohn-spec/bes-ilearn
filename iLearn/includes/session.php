<?php
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../auth/teacher_login.php");
    exit();
}
