<?php
// /appointment_system/admin/approve_students.php
$page_title = 'Approve Students';
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

// Fetch pending students
$stmt = $conn->prepare("SELECT u.user_id, p.full_name, u.email, u.created_at 
                        FROM users u
                        JOIN profiles p ON u.user_id = p.user_id
                        WHERE u.role = 'student' AND u.status = 'pending'
                        ORDER BY u.created_at ASC");
$stmt->execute();
$result = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Approve Student Registrations</h1>
    <p>Review new student accounts and approve or deny them.</p>
</div>

<?php if (isset($_GET['message'])): ?>
    <div class="message <?php echo $_GET['type']; ?>-message"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<div class="content-box neumorphic-outset">
    <h2>Pending Approvals</h2>
    
    <div class="neumorphic-table-container">
        <table class="neumorphic-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Registered On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo date('M j, Y, g:i a', strtotime($row['created_at'])); ?></td>
                            <td class="action-links">
                                <form action="admin_actions.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" name="action" value="approve_student" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="admin_actions.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" name="action" value="delete_user" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No pending student registrations.</td>
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