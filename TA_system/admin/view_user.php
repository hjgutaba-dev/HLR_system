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
   FETCH USER DETAILS
========================= */
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Redirect back if user not found
if (!$user) {
    header("Location: ../admin/manage_users.php");
    exit();
}

// Profile picture path resolution
$profile_image = !empty($user['profile_image']) && file_exists("../uploads/profile_image/" . $user['profile_image'])
    ? "../uploads/profile_image/" . $user['profile_image'] 
    : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - HLR INK STUDIO</title>
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
                    <a href="../admin/manage_artist.php">
                        <i class="fa-solid fa-user-ninja"></i>
                        <span>Artists</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../admin/manage_users.php" class="active">
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
                <a href="../admin/manage_users.php" class="btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Back to Users
                </a>
                <h1><i class="fa-solid fa-id-card" style="color: #8a2be2;"></i> User Details</h1>
            </div>

            <div class="profile-card">
                <div class="profile-header">
                    <?php if ($profile_image): ?>
                        <img src="<?php echo ($profile_image); ?>" alt="Profile Image" class="avatar-lg">
                    <?php else: ?>
                        <div class="avatar-lg"><i class="fa-solid fa-user"></i></div>
                    <?php endif; ?>

                   <div class="profile-title">
                        <h2><?php echo ($user['full_name']); ?></h2>
                        <?php 
                        $role = strtolower(trim($user['role'] ?? 'customer'));
                            $badge_class = 'role-customer';
                        if ($role === 'admin') {
                            $badge_class = 'role-admin';
                        } elseif ($role === 'artist') {
                            $badge_class = 'role-artist';
                        }
                        ?>
                        <span class="role-badge <?php echo $badge_class; ?>">
                        <?php echo strtoupper($user['role'] ?? 'CUSTOMER'); ?>
                        </span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-group">
                        <div class="info-label">User ID</div>
                        <div class="info-value"><i class="fa-solid fa-hashtag"></i> #<?php echo ($user['id']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Account Role</div>
                        <div class="info-value"><i class="fa-solid fa-user-shield"></i> <?php echo ucfirst(($user['role'])); ?></div>
                    </div>

                    <div class="info-group full-width">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><i class="fa-solid fa-user"></i> <?php echo ($user['full_name']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><i class="fa-solid fa-envelope"></i> <?php echo ($user['email']); ?></div>
                    </div>

                    <div class="info-group">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><i class="fa-solid fa-phone"></i> <?php echo ($user['Phone'] ?? 'N/A'); ?></div>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="../admin/edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i> Edit User
                    </a>
                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                        <a href="../admin/manage_users.php?delete=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">
                            <i class="fa-solid fa-trash-can"></i> Delete Account
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

</div>

</body>
</html>