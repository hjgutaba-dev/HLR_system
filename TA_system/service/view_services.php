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

if ($_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

/* =========================
   GET SERVICE ID & FETCH DATA
========================= */
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: ../service/manage_services.php");
    exit();
}

$service_id = intval($_GET["id"]);

$sql = "SELECT * FROM services WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $service_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $service_name = $row["service_name"];
    $description  = $row["description"];
} else {
    // If service not found, redirect back
    header("Location: ../service/manage_services.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Service - PATIK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #0f0f11;
            color: #f1f1f1;
            min-height: 100vh;
        }

        .app-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Fixed Sidebar */
        .sidebar {
            width: 260px;
            background: #18181c;
            border-right: 1px solid #2a2a32;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 25px 20px;
            position: fixed;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #ffffff;
            text-transform: uppercase;
            margin-bottom: 35px;
            padding-left: 10px;
        }

        .brand span {
            color: #8a2be2;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav-item a {
            color: #a0a0ab;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 12px 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .nav-item a:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item a.active {
            color: #ffffff;
            background: #8a2be2;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(138, 43, 226, 0.4);
        }

        .logout-link {
            color: #ff4d4d !important;
            border: 1px solid rgba(230, 57, 70, 0.3);
            background: rgba(230, 57, 70, 0.1);
            border-radius: 10px;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .logout-link:hover {
            background: #e63946;
            color: #ffffff !important;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Top Navigation Header */
        .page-header {
            margin-bottom: 30px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #a0a0ab;
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 15px;
            transition: color 0.2s ease;
        }

        .btn-back:hover {
            color: #8a2be2;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Details Card */
        .details-card {
            background: #18181c;
            border-radius: 16px;
            border: 1px solid #2a2a32;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .detail-group {
            margin-bottom: 25px;
        }

        .detail-group:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #8a2be2;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 1.1rem;
            color: #ffffff;
            font-weight: 500;
        }

        .detail-description {
            font-size: 0.95rem;
            color: #e1e1e6;
            line-height: 1.6;
            background: #0f0f11;
            padding: 16px;
            border-radius: 10px;
            border: 1px solid #2a2a32;
        }

        .badge-id {
            display: inline-block;
            background: rgba(138, 43, 226, 0.15);
            color: #8a2be2;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid rgba(138, 43, 226, 0.3);
        }

        /* Action Buttons Area */
        .card-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #2a2a32;
            display: flex;
            gap: 12px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-edit {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .btn-edit:hover {
            background: #ffc107;
            color: #000000;
        }

        .btn-delete {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .btn-delete:hover {
            background: #dc3545;
            color: #ffffff;
        }

        @media (max-width: 850px) {
            .sidebar {
                width: 70px;
                padding: 20px 10px;
            }
            .brand span, .nav-item span, .logout-link span {
                display: none;
            }
            .main-content {
                margin-left: 70px;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="app-layout">

    <!-- Fixed Sidebar Navigation -->
    <aside class="sidebar">
        <div>
            <div class="brand">PATIK <span>STUDIO</span></div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="../admin/admin_dashboard.php">
                        <i class="fa-solid fa-gauge"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../auth/view_appointment.php">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../service/manage_services.php" class="active">
                        <i class="fa-solid fa-list-check"></i>
                        <span>Services</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../artist/manage_artist.php">
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
            <a href="../auth/logout.php" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <div class="container">

            <div class="page-header">
                <a href="../service/manage_services.php" class="btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Services
                </a>
                <h1><i class="fa-solid fa-eye" style="color: #8a2be2;"></i> Service Details</h1>
            </div>

            <div class="details-card">
                <div class="detail-group">
                    <div class="detail-label">Service ID</div>
                    <div class="detail-value">
                        <span class="badge-id">#<?php echo htmlspecialchars($service_id); ?></span>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Service Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($service_name); ?></div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Description</div>
                    <div class="detail-description">
                        <?php echo nl2br(htmlspecialchars($description)); ?>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="../service/edit_service.php?id=<?php echo $service_id; ?>" class="btn-action btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Service
                    </a>
                    <a href="../service/manage_services.php?delete=<?php echo $service_id; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this service?');">
                        <i class="fa-solid fa-trash-can"></i> Delete Service
                    </a>
                </div>
            </div>

        </div>
    </main>

</div>

</body>
</html>