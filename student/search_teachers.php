<?php
// /appointment_system/student/search_teachers.php
$page_title = 'Search Teachers';
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Prepare search query
$sql = "SELECT p.user_id, p.full_name, p.department, p.subject 
        FROM profiles p
        JOIN users u ON p.user_id = u.user_id
        WHERE u.role = 'teacher'";

if (!empty($search_term)) {
    $like_term = "%" . $search_term . "%";
    $sql .= " AND (p.full_name LIKE ? OR p.department LIKE ? OR p.subject LIKE ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $like_term, $like_term, $like_term);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$teachers = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="page-header">
    <h1>Book an Appointment</h1>
    <p>Find a teacher to see their schedule.</p>
</div>

<div class="content-box neumorphic-outset" style="margin-bottom: 30px;">
    <form action="search_teachers.php" method="GET" class="neumorphic-form">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="search">Search by Name, Department, or Subject</label>
            <input type="search" id="search" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="e.g., Prof. Smith, Science, or Math">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Search</button>
    </form>
</div>

<h2>Search Results (<?php echo $teachers->num_rows; ?>)</h2>
<div class="card-grid">
    <?php if ($teachers->num_rows > 0): ?>
        <?php while ($teacher = $teachers->fetch_assoc()): ?>
            <div class="card neumorphic-outset">
                <h3><?php echo htmlspecialchars($teacher['full_name']); ?></h3>
                <p>
                    <strong>Department:</strong> <?php echo htmlspecialchars($teacher['department']); ?><br>
                    <strong>Subject:</strong> <?php echo htmlspecialchars($teacher['subject']); ?>
                </p>
                <a href="book_appointment.php?teacher_id=<?php echo $teacher['user_id']; ?>" class="btn btn-primary">
                    View Schedule
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No teachers found matching your search.</p>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
require_once '../includes/footer.php';
?>