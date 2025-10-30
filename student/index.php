<?php
// /appointment_system/student/index.php
$page_title = 'Student Dashboard';
require_once '../includes/auth_check.php'; // $user_id, $full_name, $role
require_once '../includes/db.php';

// Fetch all appointments for this student
$stmt = $conn->prepare("SELECT a.*, p.full_name AS teacher_name, p.department, av.start_time, av.end_time
                        FROM appointments a
                        JOIN profiles p ON a.teacher_id = p.user_id
                        JOIN availability av ON a.availability_id = av.availability_id
                        WHERE a.student_id = ?
                        ORDER BY av.start_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
    <p>Search for teachers and book your appointments.</p>
</div>

<?php if (isset($_GET['message'])): ?>
    <div class="message <?php echo $_GET['type']; ?>-message"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<div class="content-box neumorphic-outset">
    <h2>My Appointments</h2>
    <div class="neumorphic-table-container">
        <table class="neumorphic-table">
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Department</th>
                    <th>Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($appointments->num_rows > 0): ?>
                    <?php while ($app = $appointments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['teacher_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['department']); ?></td>
                            <td><?php echo date('D, M j, Y \a\t g:i a', strtotime($app['start_time'])); ?></td>
                            <td><?php echo htmlspecialchars($app['purpose']); ?></td>
                            <td>
                                <strong style="text-transform: capitalize; color: <?php
                                    if ($app['status'] == 'approved') echo '#28a745';
                                    if ($app['status'] == 'cancelled') echo '#dc3545';
                                    if ($app['status'] == 'pending') echo '#ffc107';
                                ?>;">
                                    <?php echo $app['status']; ?>
                                </strong>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">You have not booked any appointments.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
require_once '../includes/footer.php';
?>