<?php
// /appointment_system/teacher/teacher_actions.php
require_once '../includes/auth_check.php'; // $user_id, $role
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    $action = $_POST['action'];

    // --- Action: Add Availability ---
    if ($action == 'add_availability') {
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        if (strtotime($start_time) >= strtotime($end_time)) {
            header("Location: schedule.php?message=End time must be after start time.&type=error");
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO availability (teacher_id, start_time, end_time) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $start_time, $end_time);

        if ($stmt->execute()) {
            header("Location: schedule.php?message=Availability slot added.&type=success");
        } else {
            header("Location: schedule.php?message=Error adding slot.&type=error");
        }
        $stmt->close();
    }

    // --- Action: Delete Availability ---
    elseif ($action == 'delete_availability' && isset($_POST['availability_id'])) {
        $availability_id = $_POST['availability_id'];

        // Only allow deleting if it's not booked and belongs to this teacher
        $stmt = $conn->prepare("DELETE FROM availability WHERE availability_id = ? AND teacher_id = ? AND is_booked = 0");
        $stmt->bind_param("ii", $availability_id, $user_id);
        
        if ($stmt->execute()) {
            header("Location: schedule.php?message=Slot deleted.&type=success");
        } else {
            header("Location: schedule.php?message=Error deleting slot (it may be booked).&type=error");
        }
        $stmt->close();
    }

    // --- Action: Approve Appointment ---
    elseif ($action == 'approve_appointment' && isset($_POST['appointment_id']) && isset($_POST['availability_id'])) {
        $appointment_id = $_POST['appointment_id'];
        $availability_id = $_POST['availability_id'];

        // 1. Set appointment status to 'approved'
        $stmt_app = $conn->prepare("UPDATE appointments SET status = 'approved' WHERE appointment_id = ? AND teacher_id = ?");
        $stmt_app->bind_param("ii", $appointment_id, $user_id);
        
        if ($stmt_app->execute()) {
            // 2. Set availability slot to 'is_booked = 1'
            $stmt_avail = $conn->prepare("UPDATE availability SET is_booked = 1 WHERE availability_id = ? AND teacher_id = ?");
            $stmt_avail->bind_param("ii", $availability_id, $user_id);
            $stmt_avail->execute();
            $stmt_avail->close();
            
            header("Location: index.php?message=Appointment approved.&type=success");
        } else {
            header("Location: index.php?message=Error approving appointment.&type=error");
        }
        $stmt_app->close();
    }

    // --- Action: Cancel Appointment ---
    elseif ($action == 'cancel_appointment' && isset($_POST['appointment_id']) && isset($_POST['availability_id'])) {
        $appointment_id = $_POST['appointment_id'];
        $availability_id = $_POST['availability_id'];

        // 1. Set appointment status to 'cancelled'
        $stmt_app = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE appointment_id = ? AND teacher_id = ?");
        $stmt_app->bind_param("ii", $appointment_id, $user_id);

        if ($stmt_app->execute()) {
            // 2. Set availability slot back to 'is_booked = 0'
            $stmt_avail = $conn->prepare("UPDATE availability SET is_booked = 0 WHERE availability_id = ? AND teacher_id = ?");
            $stmt_avail->bind_param("ii", $availability_id, $user_id);
            $stmt_avail->execute();
            $stmt_avail->close();

            header("Location: index.php?message=Appointment cancelled.&type=success");
        } else {
            header("Location: index.php?message=Error cancelling appointment.&type=error");
        }
        $stmt_app->close();
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