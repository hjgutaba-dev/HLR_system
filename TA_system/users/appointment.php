<?php
// Anti-cache headers aron dili ma-stock sa browser history ang pahina human mag-logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["role"] !== "customer") {
    header("Location: ../admin/admin_dashboard.php");
    exit();
}

// Fetch dynamic lists from DB
$services = mysqli_query($conn, "SELECT id, service_name FROM services ORDER BY service_name ASC");
$artists  = mysqli_query($conn, "SELECT id, artist_name, specialty FROM artists ORDER BY artist_name ASC");

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = intval($_SESSION["user_id"]);
    $service_id  = intval($_POST["service_id"]);
    $artist_id   = intval($_POST["artist_id"]);
    $date        = trim($_POST["appointment_date"]);
    $time        = trim($_POST["appointment_time"]);
    $notes       = trim($_POST["notes"]);

    // --- STRICT DATE VALIDATION ---
    $current_date = date('Y-m-d'); 

    if ($date < $current_date) {
        $error_msg = "Invalid appointment date. You cannot select a date in the past.";
    } elseif (empty($time)) {
        $error_msg = "Please select a valid appointment time.";
    } else {
        // --- PREVENT DUPLICATE SUBMISSIONS PERMANENTLY ---
        // Checks if an active (Pending or Confirmed) appointment already exists for this customer on the exact same date and time
        $check_duplicate = mysqli_prepare($conn, "SELECT id FROM appointments WHERE customer_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled' LIMIT 1");
        mysqli_stmt_bind_param($check_duplicate, "iss", $customer_id, $date, $time);
        mysqli_stmt_execute($check_duplicate);
        mysqli_stmt_store_result($check_duplicate);

        if (mysqli_stmt_num_rows($check_duplicate) > 0) {
            $error_msg = "You already have an active appointment scheduled at this exact date and time. Multiple bookings for the same slot are not allowed.";
            mysqli_stmt_close($check_duplicate);
        } else {
            mysqli_stmt_close($check_duplicate);

            // Prepared statement to insert appointment safely
            $stmt = mysqli_prepare($conn, "INSERT INTO appointments (customer_id, service_id, artist_id, appointment_date, appointment_time, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
            mysqli_stmt_bind_param($stmt, "iiisss", $customer_id, $service_id, $artist_id, $date, $time, $notes);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                // Redirect instantly using PRG pattern to prevent browser back-button duplication/resubmission
                header("Location: ../users/my_appointment.php");
                exit();
            } else {
                $error_msg = "Booking Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href= "../users/users_style.css">

</head>
<body>

<div class="app-layout">

    <!-- Fixed Sidebar Navigation -->
    <aside class="sidebar">
        <div>
            <div class="brand">HLR INK <span>STUDIO</span></div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="../users/customer.php">
                        <i class="fa-solid fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../users/my_appointment.php">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>My Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../users/profile.php">
                        <i class="fa-regular fa-user"></i>
                        <span>My Profile</span>
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Are you sure you want to log out?');">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="container">
            <div class="form-card">
                <h1 class="page-title">
                    <i class="fa-regular fa-calendar-plus" style="color: #ffffff;"></i> Book Appointment
                </h1>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span><?php echo ($error_msg); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="service_id">Choose Service</label>
                        <select name="service_id" id="service_id" class="form-control" required>
                            <option value="">Select Service</option>
                            <?php while ($service = mysqli_fetch_assoc($services)) { ?>
                                <option value="<?php echo ($service['id']); ?>">
                                    <?php echo ($service['service_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="artist_id">Choose Artist</label>
                        <select name="artist_id" id="artist_id" class="form-control" required>
                            <option value="">Select Artist</option>
                            <?php while ($artist = mysqli_fetch_assoc($artists)) { ?>
                                <option value="<?php echo ($artist['id']); ?>">
                                    <?php echo ($artist['artist_name'] . ' (' . $artist['specialty'] . ')'); ?>
                                </option>
                            <?php } ?>
                          </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="appointment_date">Appointment Date</label>
                                 <!-- Gidugangan nako ni ug min attribute aron ma-disable ang mga nangaging adlaw -->
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="appointment_time">Appointment Time</label>
                            <input type="time" name="appointment_time" id="appointment_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes (optional)</label>
                        <textarea name="notes" id="notes" class="form-control" placeholder="Describe placement, size, or special requests..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-check"></i> Book Appointment
                    </button>
                </form>
            </div>
        </div>
    </main>

</div>

</body>
</html>