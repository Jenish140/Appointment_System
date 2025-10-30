<?php
// /appointment_system/student/book_appointment.php
if (!isset($_GET['teacher_id'])) {
    header("Location: search_teachers.php");
    exit;
}

$teacher_id = (int)$_GET['teacher_id'];

$page_title = 'Book Appointment';
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

// Get Teacher's info
$stmt_teacher = $conn->prepare("SELECT full_name, department, subject FROM profiles WHERE user_id = ?");
$stmt_teacher->bind_param("i", $teacher_id);
$stmt_teacher->execute();
$teacher = $stmt_teacher->get_result()->fetch_assoc();
if (!$teacher) {
    header("Location: search_teachers.php?message=Teacher not found.&type=error");
    exit;
}

// Get Teacher's available slots
$stmt_slots = $conn->prepare("SELECT * FROM availability 
                              WHERE teacher_id = ? AND is_booked = 0 AND start_time > NOW()
                              ORDER BY start_time ASC");
$stmt_slots->bind_param("i", $teacher_id);
$stmt_slots->execute();
$slots = $stmt_slots->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Book Appointment with <?php echo htmlspecialchars($teacher['full_name']); ?></h1>
    <p><strong>Department:</strong> <?php echo htmlspecialchars($teacher['department']); ?> | <strong>Subject:</strong> <?php echo htmlspecialchars($teacher['subject']); ?></p>
</div>

<div class="content-box neumorphic-outset">
    <h2>Available Time Slots</h2>
    <p>Select a time slot and provide a purpose for your appointment. This will be sent as a message to the teacher.</p>
    
    <div class="neumorphic-table-container" style="margin-top: 20px;">
        <table class="neumorphic-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Message / Purpose</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($slots->num_rows > 0): ?>
                    <?php while ($slot = $slots->fetch_assoc()): ?>
                        <tr>
                            <form action="student_actions.php" method="POST">
                                <input type="hidden" name="action" value="book_appointment">
                                <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">
                                <input type="hidden" name="availability_id" value="<?php echo $slot['availability_id']; ?>">
                                
                                <td>
                                    <strong><?php echo date('D, M j, Y', strtotime($slot['start_time'])); ?></strong><br>
                                    <?php echo date('g:i a', strtotime($slot['start_time'])); ?> - <?php echo date('g:i a', strtotime($slot['end_time'])); ?>
                                </td>
                                <td>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <textarea name="purpose" rows="2" placeholder="State the purpose of your meeting..." required class="neumorphic-inset" style="width: 100%; padding: 10px;"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-sm">Request Booking</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">This teacher has no available appointments. Please check back later.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$stmt_teacher->close();
$stmt_slots->close();
$conn->close();
require_once '../includes/footer.php';
?>