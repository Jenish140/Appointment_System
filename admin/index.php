<?php
// /appointment_system/admin/index.php
$page_title = 'Admin Dashboard';
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

// Fetch some quick stats for the dashboard
$pending_students = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='student' AND status='pending'")->fetch_assoc()['count'];
$total_teachers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='teacher'")->fetch_assoc()['count'];
$total_appointments = $conn->query("SELECT COUNT(*) as count FROM appointments WHERE status='approved'")->fetch_assoc()['count'];

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
    <p>This is the Admin Control Panel. You can manage the system from here.</p>
</div>

<div class="card-grid">
    <div class="card neumorphic-outset">
        <h3>Pending Students</h3>
        <p class="stat-number"><?php echo $pending_students; ?></p>
        <p>New student registrations waiting for your approval.</p>
        <a href="approve_students.php" class="btn">Approve Students</a>
    </div>
    <div class="card neumorphic-outset">
        <h3>Total Teachers</h3>
        <p class="stat-number"><?php echo $total_teachers; ?></p>
        <p>Manage teacher accounts, subjects, and departments.</p>
        <a href="manage_teachers.php" class="btn">Manage Teachers</a>
    </div>
    <div class="card neumorphic-outset">
        <h3>Approved Appointments</h3>
        <p class="stat-number"><?php echo $total_appointments; ?></p>
        <p>Total appointments successfully booked in the system.</p>
        <a href="#" class="btn" style="opacity: 0.5; pointer-events: none;">View All</a>
    </div>
</div>

<?php
$conn->close();
require_once '../includes/footer.php';
?>