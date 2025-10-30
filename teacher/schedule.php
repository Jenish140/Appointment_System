<?php
// /appointment_system/teacher/schedule.php
$page_title = 'My Schedule';
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

// Fetch teacher's existing availability
$stmt = $conn->prepare("SELECT * FROM availability 
                        WHERE teacher_id = ? AND start_time > NOW()
                        ORDER BY start_time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$availability = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Set Your Availability</h1>
    <p>Students can book appointments during these times.</p>
</div>

<?php if (isset($_GET['message'])): ?>
    <div class="message <?php echo $_GET['type']; ?>-message"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<div class="layout-grid">
    <div class="content-box neumorphic-outset">
        <h2>Add New Slot</h2>
        <form action="teacher_actions.php" method="POST" class="neumorphic-form">
            <input type="hidden" name="action" value="add_availability">
            
            <div class="form-group">
                <label for="start_time">Start Time</label>
                <input type="datetime-local" id="start_time" name="start_time" required>
            </div>
            <div class="form-group">
                <label for="end_time">End Time</label>
                <input type="datetime-local" id="end_time" name="end_time" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Slot</button>
        </form>
    </div>

    <div class="content-box neumorphic-outset">
        <h2>Your Upcoming Slots</h2>
        <div class="neumorphic-table-container">
            <table class="neumorphic-table">
                <thead>
                    <tr>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($availability->num_rows > 0): ?>
                        <?php while ($row = $availability->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('M j, Y, g:i a', strtotime($row['start_time'])); ?></td>
                                <td><?php echo date('M j, Y, g:i a', strtotime($row['end_time'])); ?></td>
                                <td>
                                    <?php if ($row['is_booked']): ?>
                                        <span style="color: #dc3545; font-weight: bold;">Booked</span>
                                    <?php else: ?>
                                        <span style="color: #28a745; font-weight: bold;">Available</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-links">
                                    <?php if (!$row['is_booked']): ?>
                                    <form action="teacher_actions.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this slot?');">
                                        <input type="hidden" name="availability_id" value="<?php echo $row['availability_id']; ?>">
                                        <button type="submit" name="action" value="delete_availability" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">You have no upcoming availability.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
require_once '../includes/footer.php';
?>