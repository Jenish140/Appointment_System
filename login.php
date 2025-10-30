<?php
// /appointment_system/login.php
session_start();
require_once 'includes/db.php';

// If already logged in, redirect to respective dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/");
    } elseif ($_SESSION['role'] == 'teacher') {
        header("Location: teacher/");
    } else {
        header("Location: student/");
    }
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT u.user_id, u.email, u.password, u.role, u.status, p.full_name 
                            FROM users u 
                            JOIN profiles p ON u.user_id = p.user_id 
                            WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if ($user['status'] == 'pending') {
                $error = "Your account approval is pending by administrator.";
            } else {
                // Password is correct, start session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header("Location: admin/");
                } elseif ($user['role'] == 'teacher') {
                    header("Location: teacher/");
                } elseif ($user['role'] == 'student') {
                    header("Location: student/");
                }
                exit;
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Appointment System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="form-page">
    <div class="form-container">
        <div class="form-logo">
    Appoint<span class="logo-accent">Me</span>
</div>
<p class="form-subtitle">Please login or register to book an appointment.</p>

        <?php if (!empty($error)): ?>
            <div class="message error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="neumorphic-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="form-footer">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>