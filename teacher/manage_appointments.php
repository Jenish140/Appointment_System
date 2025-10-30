<?php
// /appointment_system/teacher/manage_appointments.php
$page_title = 'All Appointments';
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

// Fetch ALL appointments for this teacher
$stmt = $conn->prepare("SELECT a.*, p.full_name AS student_name, av.start_time
                        FROM appointments a
                        JOIN profiles p ON a.student_id = p.user_id
                        JOIN availability av ON a.availability_id = av.availability_id
                        WHERE a.teacher_id = ?
                        ORDER BY av.start_time DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$all_apps = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>All Appointments</h1>
    <p>Here is the history of all your appointments.</p>
</div>

<div class="content-box neumorphic-outset">
    <h2>Appointment History</h2>
    
    <div class="neumorphic-table-container">
        <table class="neumorphic-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Appointment Time</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($all_apps->num_rows > 0): ?>
                    <?php while ($app = $all_apps->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['student_name']); ?></td>
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
                        <td colspan="4">You have no appointment history.</td>
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