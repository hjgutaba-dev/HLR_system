<?php
// Anti-cache headers aron dili ma-stock sa browser history ang pahina human mag-logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "../config/db.php";

/* =========================
   ARTIST ACCESS ONLY
========================= */
if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"]) !== "artist") {
    // If not logged in or not an artist, redirect back to login
    header("Location: ../auth/login.php");
    exit();
}

$artist_id = $_SESSION["user_id"];
$artist_name = $_SESSION["full_name"] ?? "artist";

// Handle status update submission directly from dashboard if needed
$success_msg = "";
$error_msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_status'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_query = "UPDATE appointments SET status = '$new_status' WHERE id = $appointment_id AND artist_id = $artist_id";
    if (mysqli_query($conn, $update_query)) {
        $success_msg = "Appointment status updated successfully!";
    } else {
        $error_msg = "Failed to update status: " . mysqli_error($conn);
    }
}

// Fetch appointments booked for this specific artist
$query = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes, 
                 u.full_name AS client_name, u.email AS client_email, u.Phone AS client_phone 
          FROM appointments a
          JOIN users u ON a.customer_id = u.id
          WHERE a.artist_id = $artist_id
          ORDER BY a.appointment_date ASC, a.appointment_time ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Link to your shared admin/dashboard style sheet -->
    <link rel="stylesheet" href="../artist/artist_style.css">
</head>

<body>

<div class="app-layout">

    <!-- Fixed Sidebar Navigation for Artist -->
    <aside class="sidebar">
        <div>
            <div class="brand">HLR INK <span>STUDIO</span></div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="../artist/artist_dashboard.php" class="active">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="../artist/artist_schedule.php">
                        <i class="fa-solid fa-clock"></i>
                        <span>Schedule</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../index.php">
                        <i class="fa-solid fa-home"></i>
                        <span>Home Page</span>
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

            <!-- Welcome Header Card -->
            <div class="page-header">
                <div class="header-title">
                    <h2>Welcome, <?php echo htmlspecialchars($artist_name); ?></h2>
                    <p>Here is your tattoo schedule and client request overview for HLR Ink Studio.</p>
                </div>
                <div>
                    <span class="role-badge role-artist">
                        <i class="fa-solid fa-user-ninja"></i> ARTIST
                    </span>
                </div>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <!-- Appointments Section replacing Quick Actions -->
            <div style="margin-bottom: 20px;">
                <h3>
                    <i class="fa-regular fa-calendar-check"></i> Clients Booked With Me
                </h3>
            </div>

            <div class="card table-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Contact Info</th>
                                <th>Date & Time</th>
                                <th>Notes / Request</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php $counter = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['client_name']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['client_email']); ?><br>
                                            <small style="color: #666;"><?php echo htmlspecialchars($row['client_phone'] ?? 'No phone'); ?></small>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($row['appointment_date'])); ?><br>
                                            <small style="font-weight: 600;"><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?>
                                                    <?php 
                                                        if ($row['status'] == 'Approve' || $row['status'] == 'Confirmed') echo 'badge-success';
                                                        elseif ($row['status'] == 'Pending') echo 'badge-pending';
                                                        elseif ($row['status'] == 'Complete') echo 'badge-success';
                                                        else echo 'badge-danger';
                                                    ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: flex; gap: 5px; align-items: center;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                <select name="status" style="padding: 5px; font-size: 0.8rem; width: 100px;">
                                                    <option value="Pending" <?php if($row['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                                    <option value="Approve" <?php if($row['status']=='Approve') echo 'selected'; ?>>Approve</option>
                                                    <option value="Complete" <?php if($row['status']=='Complete') echo 'selected'; ?>>Complete</option>
                                                    <option value="Rejected" <?php if($row['status']=='Rejected') echo 'selected'; ?>>Rejected</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #666; padding: 30px;">No clients have booked an appointment with you yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</div>

</body>
</html>