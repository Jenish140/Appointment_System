<?php
// /appointment_system/register.php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';
            $status = 'pending'; // Requires admin approval

            // Insert into users table
            $stmt = $conn->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $hashed_password, $role, $status);
            
            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                
                // Insert into profiles table
                $stmt_profile = $conn->prepare("INSERT INTO profiles (user_id, full_name) VALUES (?, ?)");
                $stmt_profile->bind_param("is", $user_id, $full_name);
                
                if ($stmt_profile->execute()) {
                    $success = "Registration successful! Your account is pending admin approval. You will be redirected to login.";
                    header("Refresh: 3; url=login.php");
                } else {
                    $error = "An error occurred during profile creation.";
                    // Optionally, delete the user entry if profile fails
                    $conn->query("DELETE FROM users WHERE user_id = $user_id");
                }
                $stmt_profile->close();
            } else {
                $error = "An error occurred during registration.";
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Appointment System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="form-page">
    <div class="form-container">
        <h2 class="form-title">Create Student Account</h2>
        <p class="form-subtitle">Registration is free but requires admin approval.</p>

        <?php if (!empty($error)): ?>
            <div class="message error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="message success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="neumorphic-form">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>