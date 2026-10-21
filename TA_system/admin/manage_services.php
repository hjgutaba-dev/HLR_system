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
   ADD SERVICE
========================= */
if (isset($_POST["add_service"])) {
    $service_name = trim($_POST["service_name"]);
    $description  = trim($_POST["description"]);

    $sql = "INSERT INTO services (service_name, description) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $service_name, $description);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: ../admin/manage_services.php?msg=added");
    exit();
}

/* =========================
   DELETE SERVICE
========================= */
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    $sql = "DELETE FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: ../admin/manage_services.php?msg=deleted");
    exit();
}

/* =========================
   FETCH ALL SERVICES
========================= */
$sql = "SELECT * FROM services ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - HLR INK STUDIO</title>
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
            <div class="brand">PATIK <span>STUDIO</span></div>
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
                    <a href="../admin/manage_services.php" class="active">
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

            <!-- Page Header with Top Right Add Button -->
            <div class="page-header">
                <div class="header-title">
                    <h1><i class="fa-solid fa-list-check" style="color: #8a2be2;"></i> Manage Tattoo Services</h1>
                
                </div>
                <button class="btn-add-primary" onclick="openModal()">
                    <i class="fa-solid fa-plus"></i> Add Service
                </button>
            </div>

            <!-- Available Services Table -->
            <div class="table-card">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 250px;">Service Name</th>
                            <th>Description</th>
                            <th style="width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) {
                            while ($service = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td>#<?php echo ($service["id"]); ?></td>
                                    <td><strong><?php echo ($service["service_name"]); ?></strong></td>
                                    <td><?php echo ($service["description"]); ?></td>
                                    <td>
                                        <div class="action-cell">
                                            <a href="../admin/view_service.php?id=<?php echo $service['id']; ?>" class="btn-action btn-view">
                                                 <i class="fa-solid fa-eye"></i> View
                                            </a>
                                            <a href="../admin/edit_service.php?id=<?php echo $service['id']; ?>" class="btn-action btn-edit">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="../admin/manage_services.php?delete=<?php echo $service['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure?');">
                                                <i class="fa-solid fa-trash-can"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #a0a0ab; padding: 30px;">
                                    No services available. Click "+ Add Service" above to create one.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</div>

<!-- Separate Add Service Modal Window -->
<div class="modal-overlay" id="addServiceModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-plus-circle" style="color: #8a2be2;"></i> Add New Service</h3>
            <button class="btn-close-modal" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label for="service_name">Service Name</label>
                <input type="text" id="service_name" name="service_name" class="form-control" placeholder="Example: Small Tattoo" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" placeholder="Enter service description" rows="4" required></textarea>
            </div>

            <button type="submit" name="add_service" class="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Save Service
            </button>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('addServiceModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('addServiceModal').style.display = 'none';
    }

    // Close modal if user clicks outside of the card
    window.onclick = function(event) {
        const modal = document.getElementById('addServiceModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

</body>
</html>