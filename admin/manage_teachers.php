<?php
// /appointment_system/admin/manage_teachers.php
$page_title = 'Manage Teachers';
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

// Fetch all teachers
$stmt = $conn->prepare("SELECT u.user_id, p.full_name, p.department, p.subject, u.email 
                        FROM users u
                        JOIN profiles p ON u.user_id = p.user_id
                        WHERE u.role = 'teacher'
                        ORDER BY p.full_name ASC");
$stmt->execute();
$teachers = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Manage Teachers</h1>
    <p>Add, edit, or remove teacher accounts from the system.</p>
</div>

<?php if (isset($_GET['message'])): ?>
    <div class="message <?php echo $_GET['type']; ?>-message"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

<div class="layout-grid">
    <div class="content-box neumorphic-outset">
        <h2>Add New Teacher</h2>
        <form action="admin_actions.php" method="POST" class="neumorphic-form">
            <input type="hidden" name="action" value="add_teacher">
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Default Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <input type="text" id="department" name="department" placeholder="e.g., Computer Science">
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" placeholder="e.g., Web Development">
            </div>
            <button type="submit" class="btn btn-primary">Add Teacher</button>
        </form>
    </div>

    <div class="content-box neumorphic-outset">
        <h2>Current Teachers</h2>
        <div class="neumorphic-table-container">
            <table class="neumorphic-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Subject</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($teachers->num_rows > 0): ?>
                        <?php while ($row = $teachers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?><br><small><?php echo htmlspecialchars($row['email']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td class="action-links">
                                    <form action="admin_actions.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher? This is permanent.');">
                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                        <button type="submit" name="action" value="delete_user" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No teachers found.</td>
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