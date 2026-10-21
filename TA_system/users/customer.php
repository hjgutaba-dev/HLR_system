<?php
// Anti-cache headers aron dili ma-stock sa browser history ang pahina human mag-logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "../config/db.php";

// Allow only customers to access this page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "customer") {
    header("Location: ../auth/login.php");
    exit();
}

$customer_id = intval($_SESSION["user_id"]);

// Fetch dynamic lists from DB for the embedded booking form
$services = mysqli_query($conn, "SELECT id, service_name FROM services ORDER BY service_name ASC");
$artists  = mysqli_query($conn, "SELECT id, artist_name, specialty FROM artists ORDER BY artist_name ASC");

$success_msg = "";
$error_msg = "";

// Handle direct booking submission on dashboard
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["book_appointment"])) {
    $service_id = intval($_POST["service_id"]);
    $artist_id  = intval($_POST["artist_id"]);
    $date       = $_POST["appointment_date"];
    $time       = $_POST["appointment_time"];
    $notes      = trim($_POST["notes"]);

    $stmt = mysqli_prepare($conn, "INSERT INTO appointments (customer_id, service_id, artist_id, appointment_date, appointment_time, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    mysqli_stmt_bind_param($stmt, "iiisss", $customer_id, $service_id, $artist_id, $date, $time, $notes);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        $success_msg = "Appointment booked successfully!";
    } else {
        $error_msg = "Booking Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - HLR INK STUDIO</title>
    <!-- Google Fonts for Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
                    <a href="../users/customer.php" class="active">
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
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Are you sure you want to log out? You will need to sign in again.');">
                <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="container">
            
            <!-- Welcome Banner -->
            <div class="welcome-card">
                <h1>Welcome, <?php echo ($_SESSION["full_name"]); ?> 👋</h1>
                <p>Welcome to <strong>HLR INK STUDIO</strong>. Use the form below to secure your tattoo appointment directly from your dashboard.</p>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $success_msg; ?></div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?></div>
            <?php endif; ?>

            <!-- Visible Booking Form Card -->
            <div class="form-card">
                <h2 class="page-title">
                    <i class="fa-regular fa-calendar-plus"></i> Book Appointment
                </h2>

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
                            <input type="date" name="appointment_date" id="appointment_date" class="form-control" required>
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

                    <button type="submit" name="book_appointment" class="btn-submit">
                        <i class="fa-solid fa-check"></i> Book Appointment
                    </button>
                </form>
            </div>

        </div>
    </main>

</div>

</body>
</html>