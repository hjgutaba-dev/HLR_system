<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id        = intval($_SESSION["user_id"]);
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_date = mysqli_real_escape_string($conn, $_POST["appointment_date"]);
    $new_time = mysqli_real_escape_string($conn, $_POST["appointment_time"]);

    $stmt = mysqli_prepare($conn, "UPDATE appointments SET appointment_date = ?, appointment_time = ?, status = 'Pending' WHERE id = ? AND customer_id = ?");
    mysqli_stmt_bind_param($stmt, "ssii", $new_date, $new_time, $appointment_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: ../auth/my_appointment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reschedule Appointment</title>
</head>
<body>
    <h1>Reschedule Appointment</h1>
    <form method="POST">
        <label>New Date:</label><br>
        <input type="date" name="appointment_date" required><br><br>

        <label>New Time:</label><br>
        <input type="time" name="appointment_time" required><br><br>

        <button type="submit">Update Schedule</button>
    </form>
    <br>
    <a href="../auth/my_appointment.php">&larr; Cancel</a>
</body>
</html>