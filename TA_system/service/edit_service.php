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
   UPDATE SERVICE
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = trim($_POST["service_name"]);
    $description  = trim($_POST["description"]);

    $stmt = mysqli_prepare($conn, "UPDATE services SET service_name = ?, description = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $service_name, $description, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: ../service/manage_services.php?msg=updated");
    exit();
}

/* =========================
   FETCH SERVICE DETAILS
========================= */
$stmt = mysqli_prepare($conn, "SELECT * FROM services WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$service = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Redirect if service not found
if (!$service) {
    header("Location: ../service/manage_services.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service - PATIK STUDIO</title>
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

        /* Fixed Sidebar Navigation */
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
            max-width: 700px;
            margin: 0 auto;
        }

        /* Header Navigation */
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

        /* Form Card */
        .form-card {
            background: #18181c;
            border-radius: 16px;
            border: 1px solid #2a2a32;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #a0a0ab;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: #0f0f11;
            border: 1px solid #2a2a32;
            border-radius: 8px;
            color: #e1e1e6;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #8a2be2;
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #2a2a32;
        }

        .btn-submit {
            background: #8a2be2;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(138, 43, 226, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background: #7a1ed2;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: rgba(255, 255, 255, 0.05);
            color: #a0a0ab;
            border: 1px solid #2a2a32;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.1);
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
                <h1><i class="fa-solid fa-pen-to-square" style="color: #8a2be2;"></i> Edit Service</h1>
            </div>

            <div class="form-card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="service_name">Service Name</label>
                        <input 
                            type="text" 
                            id="service_name" 
                            name="service_name" 
                            class="form-control" 
                            value="<?php echo htmlspecialchars($service['service_name'] ?? ''); ?>" 
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-control" 
                            rows="5" 
                            required
                        ><?php echo htmlspecialchars($service['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-floppy-disk"></i> Update Service
                        </button>
                        <a href="../service/manage_services.php" class="btn-cancel">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </main>

</div>

</body>
</html>