<?php
// /appointment_system/includes/header.php
// We assume auth_check.php has been called before this
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - Appointment System</title>
    <link rel="stylesheet" href="/appointment_system/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/appointment_system/<?php echo $_SESSION['role']; ?>/" class="nav-logo">
                Appoint<span class="logo-accent">Me</span>
            </a>
            <ul class="nav-menu">
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li><a href="/appointment_system/admin/index.php">Dashboard</a></li>
                    <li><a href="/appointment_system/admin/manage_teachers.php">Teachers</a></li>
                    <li><a href="/appointment_system/admin/approve_students.php">Students</a></li>
                <?php elseif ($_SESSION['role'] == 'teacher'): ?>
                    <li><a href="/appointment_system/teacher/index.php">Dashboard</a></li>
                    <li><a href="/appointment_system/teacher/schedule.php">My Schedule</a></li>
                    <li><a href="/appointment_system/teacher/manage_appointments.php">Appointments</a></li>
                <?php elseif ($_SESSION['role'] == 'student'): ?>
                    <li><a href="/appointment_system/student/index.php">Dashboard</a></li>
                    <li><a href="/appointment_system/student/search_teachers.php">Book Appointment</a></li>
                <?php endif; ?>
                <li><a href="/appointment_system/logout.php" class="btn btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main class="page-container">
        <div class="container">