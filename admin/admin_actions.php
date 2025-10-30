<?php
// /appointment_system/admin/admin_actions.php
require_once '../includes/auth_check.php'; // Ensures only admin can run this
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    $action = $_POST['action'];

    // --- Action: Approve Student ---
    if ($action == 'approve_student' && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        
        $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE user_id = ? AND role = 'student'");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            header("Location: approve_students.php?message=Student approved successfully.&type=success");
        } else {
            header("Location: approve_students.php?message=Error approving student.&type=error");
        }
        $stmt->close();
    }

    // --- Action: Delete User (Student or Teacher) ---
    elseif ($action == 'delete_user' && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        
        // Deleting from 'users' table will cascade and delete from 'profiles'
        // and also 'appointments' etc. due to FOREIGN KEY ON DELETE CASCADE
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            header("Location: {$_SERVER['HTTP_REFERER']}?message=User deleted successfully.&type=success");
        } else {
            header("Location: {$_SERVER['HTTP_REFERER']}?message=Error deleting user.&type=error");
        }
        $stmt->close();
    }

    // --- Action: Add Teacher ---
    elseif ($action == 'add_teacher') {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $department = $_POST['department'];
        $subject = $_POST['subject'];

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'teacher';
        $status = 'approved';

        // Check if email exists
        $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            header("Location: manage_teachers.php?message=Email already exists.&type=error");
            exit;
        }
        $stmt_check->close();

        // Insert into users
        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, ?)");
        $stmt_user->bind_param("ssss", $email, $hashed_password, $role, $status);
        
        if ($stmt_user->execute()) {
            $user_id = $stmt_user->insert_id;
            
            // Insert into profiles
            $stmt_profile = $conn->prepare("INSERT INTO profiles (user_id, full_name, department, subject) VALUES (?, ?, ?, ?)");
            $stmt_profile->bind_param("isss", $user_id, $full_name, $department, $subject);
            
            if ($stmt_profile->execute()) {
                header("Location: manage_teachers.php?message=Teacher added successfully.&type=success");
            } else {
                // Rollback user creation
                $conn->query("DELETE FROM users WHERE user_id = $user_id");
                header("Location: manage_teachers.php?message=Error adding teacher profile.&type=error");
            }
            $stmt_profile->close();
        } else {
            header("Location: manage_teachers.php?message=Error adding teacher account.&type=error");
        }
        $stmt_user->close();
    }

    else {
        header("Location: index.php?message=Unknown action.&type=error");
    }

    $conn->close();
} else {
    header("Location: index.php");
    exit;
}
?>