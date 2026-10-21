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

$user_id = intval($_SESSION["user_id"]);

// --- DELETE LOGIC ---
if (isset($_GET["delete"])) {
    $appointment_id = intval($_GET["delete"]);
    
    $stmt = mysqli_prepare($conn, "DELETE FROM appointments WHERE id = ? AND customer_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: ../users/my_appointment.php");
    exit();
}

// --- EDIT / UPDATE LOGIC ---
if (isset($_POST["update_appointment"])) {
    $appointment_id = intval($_POST["appointment_id"]);
    $new_date       = trim($_POST["appointment_date"]);
    $new_time       = trim($_POST["appointment_time"]);
    $notes          = trim($_POST["notes"]);

    $current_date = date('Y-m-d');

    if ($new_date < $current_date) {
        $error_msg = "Invalid appointment date. You cannot select a date in the past.";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE appointments SET appointment_date = ?, appointment_time = ?, notes = ?, status = 'Pending' WHERE id = ? AND customer_id = ?");
        mysqli_stmt_bind_param($stmt, "sssii", $new_date, $new_time, $notes, $appointment_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: ../users/my_appointment.php");
        exit();
    }
}

// FETCH CUSTOMER APPOINTMENTS //
$sql = "
    SELECT 
        appointments.id AS appointment_id,
        users.full_name AS customer_name,
        services.service_name,
        artists.artist_name,
        appointments.appointment_date,
        appointments.appointment_time,
        appointments.notes,
        appointments.status
    FROM appointments
    LEFT JOIN users ON appointments.customer_id = users.id
    LEFT JOIN services ON appointments.service_id = services.id
    LEFT JOIN artists ON appointments.artist_id = artists.id
    WHERE appointments.customer_id = '$user_id'
    ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Kuhaon ang ID gikan sa URL kung naay gipili
$view_id = isset($_GET['view_id']) ? intval($_GET['view_id']) : 0;
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href= "../users/users_style.css">
    
</head>
<body>

<div class="app-layout">
    <aside class="sidebar">
        <div>
            <div class="brand">HLR INK <span>STUDIO</span></div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="../users/customer.php"><i class="fa-solid fa-house"></i><span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="../users/appointment.php"><i class="fa-regular fa-calendar-plus"></i><span>Book Appointment</span></a>
                </li>
                <li class="nav-item">
                    <a href="../users/my_appointment.php" class="active"><i class="fa-solid fa-calendar-check"></i><span>My Appointments</span></a>
                </li>
                <li class="nav-item">
                    <a href="../users/profile.php"><i class="fa-regular fa-user"></i><span>My Profile</span></a>
                </li>
            </ul>
        </div>
        <div>
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Are you sure you want to log out?');">
                <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="container">
            <h1 class="page-title"><i class="fa-solid fa-calendar-check"></i> My Appointments</h1>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Service</th>
                            <th>Artist</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                            $app_id = intval($row['appointment_id']);
                            $status = strtolower($row['status']);
                            $badge_class = ($status == 'confirmed') ? 'status-confirmed' : 'status-pending';
                            $status = trim($row['status'] ?? '');

                        if (strcasecmp($status, 'Complete') === 0 ||
                            strcasecmp($status, 'Complete') === 0) {
                            $status_label = 'Complete';
                            $badge_class = 'status-confirmed';
                        } else {
                        $status_label = $status !== '' ? $status : 'Pending';
                        $badge_class = (strcasecmp($status, 'Confirmed') === 0)
                         ? 'status-confirmed'
                        : 'status-pending';
                        }

                        ?>
                            <tr>
                                <td><?php echo ($row['customer_name'] ?? 'N/A'); ?></td>
                                <td><?php echo ($row['service_name'] ?? 'N/A'); ?></td>
                                <td><?php echo ($row['artist_name'] ?? 'N/A'); ?></td>
                                <td><?php echo date("M d, Y", strtotime($row['appointment_date'])); ?></td>
                                <td><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $badge_class; ?>">

                                        <?php echo ($status_label); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="../users/my_appointment.php?view_id=<?php echo $app_id; ?>" class="btn-action btn-view">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                    <a href="../users/my_appointment.php?edit_id=<?php echo $app_id; ?>" class="btn-action btn-edit">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="../users/my_appointment.php?delete=<?php echo $app_id; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to permanently delete this appointment?');">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- VIEW SECTION -->
                            <?php if ($view_id === $app_id) { ?>
                                <tr class="action-row">
                                    <td colspan="7">
                                        <div class="details-box">
                                            <p><strong>Service:</strong> <?php echo ($row['service_name']); ?></p>
                                            <p><strong>Artist:</strong> <?php echo ($row['artist_name']); ?></p>
                                            <p><strong>Date & Time:</strong> <?php echo date("M d, Y", strtotime($row['appointment_date'])) . ' at ' . date("h:i A", strtotime($row['appointment_time'])); ?></p>
                                            <p><strong>Notes:</strong> <?php echo !empty($row['notes']) ? ($row['notes']) : '<span style="color:#71717a;">None provided</span>'; ?></p>
                                            <div style="margin-top: 10px;">
                                                <a href="../users/my_appointment.php" class="btn-close">Close View</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>

                            <!-- EDIT SECTION -->
                            <?php if ($edit_id === $app_id) { ?>
                                <tr class="action-row">
                                    <td colspan="7">
                                        <form method="POST" action="../users/my_appointment.php" class="edit-form">
                                            <input type="hidden" name="appointment_id" value="<?php echo $app_id; ?>">
                                            
                                            <div>
                                                <label style="font-size: 0.75rem; color: #a1a1aa; display:block; margin-bottom:4px;">Date:</label>
                                                <input type="date" name="appointment_date" value="<?php echo ($row['appointment_date']); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                            
                                            <div>
                                                <label style="font-size: 0.75rem; color: #a1a1aa; display:block; margin-bottom:4px;">Time:</label>
                                                <input type="time" name="appointment_time" value="<?php echo ($row['appointment_time']); ?>" required>
                                            </div>

                                            <div>
                                                <label style="font-size: 0.75rem; color: #a1a1aa; display:block; margin-bottom:4px;">Notes:</label>
                                                <textarea name="notes" rows="1" style="width: 200px;"><?php echo ($row['notes']); ?></textarea>
                                            </div>
                                            
                                            <div style="display:flex; gap:10px; align-items:center; margin-top:16px;">
                                                <button type="submit" name="update_appointment" class="btn-save">
                                                    <i class="fa-solid fa-check"></i> Save
                                                </button>
                                                <a href="../users/my_appointment.php" class="btn-close">Cancel</a>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>

                        <?php 
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #888891; padding: 40px 0;">
                                    <i class="fa-regular fa-calendar-xmark" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                    No appointments found.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>