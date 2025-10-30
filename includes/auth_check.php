<?php
// /appointment_system/includes/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in. If not, redirect to login page.
if (!isset($_SESSION["user_id"])) {
    header("Location: /appointment_system/login.php");
    exit;
}

// Get the current directory to determine the required role
$current_dir = basename(dirname($_SERVER['PHP_SELF'])); // 'admin', 'teacher', or 'student'
$required_role = $current_dir;

// Check if the user's role matches the required role for the directory
if ($_SESSION["role"] != $required_role) {
    // If roles don't match, log them out and redirect to login
    // This prevents a student from accessing /admin/ or /teacher/ URLs
    session_unset();
    session_destroy();
    header("Location: /appointment_system/login.php?error=unauthorized");
    exit;
}

// Store user info in variables for easy access in pages
$user_id = $_SESSION["user_id"];
$full_name = $_SESSION["full_name"];
$role = $_SESSION["role"];
?>