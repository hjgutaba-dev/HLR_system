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

if ($_SESSION["role"] != "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$admin_name =$_SESSION["full_name"] ?? "admin";

// --- QUERY PARA SA TOTAL USERS ---
$users_query = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users");
$users_data = mysqli_fetch_assoc($users_query);
$total_users =$users_data['total_users'] ?? 0;

// --- QUERY PARA SA TOTAL APPOINTMENTS ---
$appointments_query = mysqli_query($conn, "SELECT COUNT(*) AS total_appointments FROM appointments");
$appointments_data = mysqli_fetch_assoc($appointments_query);
$total_appointments =$appointments_data['total_appointments'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/admin_style.css">

</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">HLR INK <span>STUDIO</span></div>
        <ul class="sidebar-menu">
            <li>
                <a href="admin_dashboard.php" class="active">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../admin/view_appointment.php">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Appointments</span>
                </a>
            </li>
            <li>
                <a href="../admin/manage_services.php">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Services</span>
                </a>
            </li>
            <li>
                <a href="../admin/manage_artist.php">
                    <i class="fa-solid fa-user-ninja"></i>
                    <span>Artists</span>
                </a>
            </li>
            <li>
                <a href="../admin/manage_users.php">
                    <i class="fa-solid fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            
        </ul>
         <div>
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Are you sure you want to log out? You will need to sign in again.');">
                <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="wrapper">

        <!-- Top Header Area -->
        <header class="top-header">
            <div class="badge-role">
                <i class="fa-solid fa-shield-halved"></i> Administrator
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="main-content">
            <div class="container">
                
                <!-- Dashboard Welcome Header -->
                <div class="dashboard-header">
                    <div class="welcome-text">
                        <h1>Welcome Admin, <span><?php echo ($admin_name); ?></span></h1>
                        <p>Overview and management controls for HLR INK Studio.</p>
                    </div>
                    <div class="badge-role">
                        <i class="fa-solid fa-shield-halved"></i> Administrator
                    </div>
                </div>

                <!-- Statistics Section (Replaced Quick Actions) -->
                <h2 class="section-title" style="color: #ffffff; font-size: 1.3rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-chart-pie" style="color: #ffffff;"></i> System Statistics Overview
                </h2>

                <div class="stats-grid">
                    <!-- Total Registered Users Card -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <div class="stat-number"><?php echo $total_users; ?></div>
                        </div>
                    </div>

                    <!-- Total Appointments Card -->
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Appointments</h3>
                            <div class="stat-number"><?php echo $total_appointments; ?></div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

    </div>

</body>
</html>