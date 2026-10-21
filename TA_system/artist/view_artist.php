<?php
session_start();
include "../config/db.php";

/* =========================
   ADMIN ACCESS ONLY
========================= */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* =========================
   FETCH ARTIST DETAILS
========================= */
$stmt = mysqli_prepare($conn, "SELECT * FROM artists WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$artist = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Redirect back if artist not found
if (!$artist) {
    header("Location: ../admin/manage_artist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Artist Details - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../artist/artist_style.css">
    
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
                    <a href="../admin/view_appointment.php">
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
                    <a href="../admin/manage_artists.php" class="active">
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
                <a href="../admin/manage_artist.php" class="btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Artists
                </a>
                <h1><i class="fa-solid fa-id-card" style="color: #8a2be2;"></i> Artist Details</h1>
            </div>

            <div class="details-card">
                <div class="profile-header">
                    <div class="avatar-circle">
                        <i class="fa-solid fa-user-ninja"></i>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo ($artist['artist_name']); ?></h2>
                        <span>Artist ID: #<?php echo ($artist['id']); ?></span>
                    </div>
                </div>

                <div class="info-group">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?php echo ($artist['artist_name']); ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Specialty / Tattoo Style</div>
                    <div class="info-value"><?php echo ($artist['specialty'] ?? 'N/A'); ?></div>
                </div>

                <div class="card-actions">
                    <a href="../artist/edit_artist.php?id=<?php echo $artist['id']; ?>" class="btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Profile
                    </a>
                    <a href="../admin/manage_artist.php?delete=<?php echo $artist['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this artist?');">
                        <i class="fa-solid fa-trash-can"></i> Delete
                    </a>
                </div>
            </div>

        </div>
    </main>

</div>

</body>
</html>