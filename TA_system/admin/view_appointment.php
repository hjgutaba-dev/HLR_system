<?php
session_start();
include "../config/db.php";

/* =========================
   ADMIN ACCESS ONLY
========================= */

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["role"] != "admin") {
    header("Location: ../users/customer.php");
    exit();
}


/* =========================
   APPROVE APPOINTMENT
========================= */

if (isset($_GET["approve"])) {
    $id = intval($_GET["approve"]);

    $stmt = mysqli_prepare($conn, "UPDATE appointments SET status='Approve' WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    header("Location: ../admin/view_appointment.php");
    exit();
}


/* =========================
   COMPLETE APPOINTMENT
========================= */

if (isset($_GET["complete"])) {
    $id = intval($_GET["complete"]);

    $stmt = mysqli_prepare($conn, "UPDATE appointments SET status='Complete' WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    header("Location: ../admin/view_appointment.php");
    exit();
}


/* =========================
   REJECT APPOINTMENT
========================= */

if (isset($_GET["reject"])) {
    $id = intval($_GET["reject"]);

    $stmt = mysqli_prepare($conn, "UPDATE appointments SET status='Rejected' WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    header("Location: ../admin/view_appointment.php");
    exit();
}


/* =========================
   GET ALL APPOINTMENTS
========================= */

$sql = "
    SELECT
        appointments.id AS appointment_id,
        users.id AS customer_id,
        users.full_name AS customer_name,
        users.email,
        services.service_name,
        artists.artist_name,
        appointments.appointment_date,
        appointments.appointment_time,
        appointments.status
    FROM appointments
    LEFT JOIN users
        ON appointments.customer_id = users.id
    LEFT JOIN services
        ON appointments.service_id = services.id
    LEFT JOIN artists
        ON appointments.artist_id = artists.id
    ORDER BY appointments.appointment_date ASC,
             appointments.appointment_time ASC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/admin_style.css">
</head>
<body>

<div class="app-layout">

    <!-- Fixed Sidebar Navigation -->
    <aside class="sidebar">
        <div>
            <div class="brand">HLR INK <span>STUDIO</span></div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="../admin/admin_dashboard.php">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../admin/view_appointment.php" class="active">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../admin/manage_services.php">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../admin/manage_artist.php">
                        <i class="fa-solid fa-user-ninja"></i>
                        <span>Artists</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../admin/manage_users.php">
                        <i class="fa-solid fa-users"></i>
                        <span>Manage Users</span>
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
            
            <div class="page-header">
                <h1><i class="fa-regular fa-calendar-check" style="color: #8a2be2;"></i> All Customer Appointments</h1>
                <p>Below are all appointments submitted by customers.</p>
            </div>

            <div class="table-card">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cust ID</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Artist</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td>#<?php echo ($row["appointment_id"]); ?></td>
                                    <td><?php echo ($row["customer_id"] ?? 'N/A'); ?></td>
                                    <td><strong><?php echo ($row["customer_name"] ?? 'N/A'); ?></strong></td>
                                    <td><?php echo ($row["email"] ?? 'N/A'); ?></td>
                                    <td><?php echo ($row["service_name"] ?? 'N/A'); ?></td>
                                    <td><?php echo ($row["artist_name"] ?? 'N/A'); ?></td>
                                    <td><?php echo ($row["appointment_date"]); ?></td>
                                    <td><?php echo ($row["appointment_time"]); ?></td>
                                    <td>
                                        <?php 
                                            if ($row["status"] == "Approve" || $row["status"] == "Approved") { 
                                                echo '<span class="badge badge-approve"><i class=""></i> Approve</span>';
                                            } elseif ($row["status"] == "Complete") {
                                                echo '<span class="badge badge-complete"><i class=""></i> Complete</span>';
                                            } elseif ($row["status"] == "Rejected") { 
                                                echo '<span class="badge badge-rejected"><i class=""></i> Rejected</span>';
                                            } else { 
                                                echo '<span class="badge badge-pending"><i class=""></i> Pending</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
    <?php if ($row["status"] == "Pending") { ?>
        <div class="action-cell">
            <a href="../admin/view_appointment.php?approve=<?php echo $row["appointment_id"]; ?>"
               class="btn-action btn-approve"
               onclick="return confirm('Approve this appointment?');">
               Approve
            </a>

            <a href="../admin/view_appointment.php?complete=<?php echo $row["appointment_id"]; ?>"
               class="btn-action btn-complete"
               onclick="return confirm('Complete this appointment?');">
               Complete
            </a>

            <a href="../admin/view_appointment.php?reject=<?php echo $row["appointment_id"]; ?>"
               class="btn-action btn-reject"
               onclick="return confirm('Reject this appointment?');">
               Reject
            </a>
        </div>
    <?php } else { ?>
        <span class="text-muted">No Action</span>
    <?php } ?>
</td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="10" style="text-align: center; color: #a0a0ab; padding: 30px;">
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