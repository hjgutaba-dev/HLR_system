<?php
session_start();
include "../config/db.php";

/* =========================
   ADMIN ACCESS ONLY
========================= */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "artist") {
    header("Location: ../auth/login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

/* =========================
   UPDATE ARTIST
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artist_name = trim($_POST["artist_name"]);
    $specialty   = trim($_POST["specialty"]);

    $stmt = mysqli_prepare($conn, "UPDATE artists SET artist_name = ?, specialty = ? WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $artist_name, $specialty, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: ../artist/manage_artist.php");
    exit();
}

/* =========================
   FETCH ARTIST DETAILS
========================= */
$stmt = mysqli_prepare($conn, "SELECT * FROM artists WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$artists = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Redirect back if artist not found
if (!$artist) {
    header("Location: ../artist/manage_artist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artist - HLR INK STUDIO</title>
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
                <h1><i class="fa-solid fa-pen-to-square" style="color: #8a2be2;"></i> Edit Artist Profile</h1>
            </div>

            <div class="form-card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="artist_name">Artist Name</label>
                        <input 
                            type="text" 
                            id="artist_name" 
                            name="artist_name" 
                            class="form-control" 
                            value="<?php echo ($artists['artist_name'] ?? ''); ?>" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="specialty">Specialty / Style</label>
                        <input 
                            type="text" 
                            id="specialty" 
                            name="specialty" 
                            class="form-control" 
                            value="<?php echo ($artists['specialty'] ?? ''); ?>" 
                            required
                        >
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-floppy-disk"></i> Update Artist
                        </button>
                        <a href="../admin/manage_artist.php" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </main>

</div>

</body>
</html>