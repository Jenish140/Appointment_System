<?php
// /appointment_system/student/student_actions.php
require_once '../includes/auth_check.php'; // $user_id, $role
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    $action = $_POST['action'];

    // --- Action: Book an Appointment ---
    if ($action == 'book_appointment') {
        $teacher_id = $_POST['teacher_id'];
        $availability_id = $_POST['availability_id'];
        $purpose = $_POST['purpose']; // This is the "message"

        // Check if the slot is still available (to prevent double-booking)
        $stmt_check = $conn->prepare("SELECT is_booked FROM availability WHERE availability_id = ? FOR UPDATE");
        $stmt_check->bind_param("i", $availability_id);
        $stmt_check->execute();
        $slot = $stmt_check->get_result()->fetch_assoc();
        
        if ($slot && $slot['is_booked'] == 0) {
            
            // Insert the appointment request
            $stmt_insert = $conn->prepare("INSERT INTO appointments (student_id, teacher_id, availability_id, purpose, status) 
                                           VALUES (?, ?, ?, ?, 'pending')");
            $stmt_insert->bind_param("iiis", $user_id, $teacher_id, $availability_id, $purpose);
            
            if ($stmt_insert->execute()) {
                // Set slot to booked to prevent others from taking it while pending
                $stmt_book = $conn->prepare("UPDATE availability SET is_booked = 1 WHERE availability_id = ?");
                $stmt_book->bind_param("i", $availability_id);
                $stmt_book->execute();
                $stmt_book->close();

                header("Location: index.php?message=Appointment request sent successfully!&type=success");
            } else {
                header("Location: book_appointment.php?teacher_id=$teacher_id&message=Error sending request.&type=error");
            }
            $stmt_insert->close();
            
        } else {
            // Slot was already booked
            header("Location: book_appointment.php?teacher_id=$teacher_id&message=Sorry, that slot was just booked. Please choose another.&type=error");
        }
        $stmt_check->close();
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