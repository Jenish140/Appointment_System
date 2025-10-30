<?php
// /appointment_system/teacher/index.php
$page_title = 'Teacher Dashboard';
require_once '../includes/auth_check.php'; // $user_id, $full_name, $role are set
require_once '../includes/db.php';

// Fetch pending appointments
$stmt = $conn->prepare("SELECT a.*, p.full_name AS student_name, av.start_time
                        FROM appointments a
                        JOIN profiles p ON a.student_id = p.user_id
                        JOIN availability av ON a.availability_id = av.availability_id
                        WHERE a.teacher_id = ? AND a.status = 'pending'
                        ORDER BY av.start_time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_apps = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
    <p>Manage your schedule and student appointments.</p>
</div>

<?php if (isset($_GET['message'])): ?>
    <div class="message <?php echo $_GET['type']; ?>-message"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<div class="content-box neumorphic-outset">
    <h2>Pending Appointment Requests (<?php echo $pending_apps->num_rows; ?>)</h2>
    <div class="neumorphic-table-container">
        <table class="neumorphic-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Appointment Time</th>
                    <th>Purpose (Message)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pending_apps->num_rows > 0): ?>
                    <?php while ($app = $pending_apps->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                            <td><?php echo date('D, M j, Y \a\t g:i a', strtotime($app['start_time'])); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($app['purpose'])); ?></td>
                            <td class="action-links">
                                <form action="teacher_actions.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $app['appointment_id']; ?>">
                                    <input type="hidden" name="availability_id" value="<?php echo $app['availability_id']; ?>">
                                    <button type="submit" name="action" value="approve_appointment" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="teacher_actions.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $app['appointment_id']; ?>">
                                    <button type="submit" name="action" value="cancel_appointment" class="btn btn-danger btn-sm">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">You have no pending appointment requests.</td>
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