<?php
// Anti-cache headers aron dili ma-stock sa browser history ang pahina human mag-logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "../config/db.php";

// Check if user is logged in and is an artist
if (!isset($_SESSION['user_id']) || strtoupper($_SESSION['role']) !== 'ARTIST') {
    header("Location: ../auth/login.php");
    exit();
}

$artist_id = $_SESSION['user_id'];

// Fetch appointments assigned to this artist for schedule tracking
$query = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes, 
                 u.full_name AS client_name, u.email AS client_email, u.Phone AS client_phone 
          FROM appointments a
          JOIN users u ON a.customer_id = u.id
          WHERE a.artist_id = $artist_id AND a.status IN ('Approve', 'Complete', 'Rejected' , 'Pending')
          ORDER BY a.appointment_date ASC, a.appointment_time ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule - HLR Ink Studio</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <a href="../artist/artist_dashboard.php">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../artist/artist_appointment.php">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../artist/artist_schedule.php" class="active">
                        <i class="fa-solid fa-clock"></i>
                        <span>Schedule</span>
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

            <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2><i class="fa-solid fa-clock" style="color: #8a2be2;"></i> Schedule </h2>
            </div>

            <!-- Upcoming Schedule Table -->
            <div class="card table-card">
                <h3 style="font-size: 1.1rem; margin-bottom: 15px; color: #fff;"><i class="fa-solid fa-business-time" style="color: #8a2be2;"></i> Upcoming Booked Schedule</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Client Name</th>
                                <th>Client Contact</th>
                                <th>Notes / Request</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><strong><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></strong></td>
                                        <td><span style="color: #8a2be2; font-weight: 600;"><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></span></td>
                                        <td><strong><?php echo htmlspecialchars($row['client_name']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($row['client_email']); ?><br>
                                            <small style="color: #888;"><?php echo htmlspecialchars($row['client_phone'] ?? 'No phone'); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?> 
                                                    if ($row['status'] == 'Approve' || $row['status'] == 'Complete' || $row['status'] == 'Rejected') echo 'badge-approved';
                                                    else echo 'badge-pending';
                                                ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #888; padding: 30px;">No upcoming schedule or active bookings found.</td>
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