<?php
// Anti-cache headers aron dili ma-stock sa browser history ang pahina human mag-logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "../config/db.php";

/* =========================
   ADMIN ACCESS ONLY
========================= */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

/* =========================
   ADD NEW USER
========================= */
if (isset($_POST["add_user"])) {
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);
    $role      = trim($_POST["role"]);
    $password  = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Profile picture upload handling
    $profile_image = "";
    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === 0) {
        $ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "webp"];

        if (in_array($ext, $allowed)) {
            $new_name = "user_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $target = "../uploads/profile_image/" . $new_name;

            if (!is_dir("../uploads/profile_image/")) {
                mkdir("../uploads/profile_image/", 0777, true);
            }

            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target)) {
                $profile_image = $new_name;
            }
        }
    }

    $sql = "INSERT INTO users (full_name, email, phone, role, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $full_name, $email, $phone, $role, $password, $profile_image);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: ../admin/manage_users.php?msg=added");
    exit();
}

/* =========================
   DELETE USER
========================= */
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);

    // Prevent admin from deleting themselves
    if ($id !== $_SESSION["user_id"]) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: ../admin/manage_users.php?msg=deleted");
    exit();
}

/* =========================
   FETCH ALL USERS
========================= */
$sql = "SELECT * FROM users ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

// Check if modal should be open via URL parameter
$is_modal_open = isset($_GET['modal']) && $_GET['modal'] === 'open';

// Check if password should be shown in plain text via URL parameter
$show_password = isset($_GET['show_pass']) && $_GET['show_pass'] === 'true';
$pass_input_type = $show_password ? 'text' : 'password';
$pass_toggle_label = $show_password ? '<i class="fa-solid fa-eye-slash"></i> Hide Password' : '<i class="fa-solid fa-eye"></i> Show Password';
$next_pass_state = $show_password ? 'false' : 'true';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - HLR INK STUDIO</title>
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

            <!-- Page Header with Top Right Add Button (Triggers Modal via PHP Link) -->
            <div class="page-header">
                <div class="header-title">
                    <h1><i class="fa-solid fa-users" style="color: #8a2be2;"></i> Manage Users</h1>
                </div>
                <a href="../admin/manage_users.php?modal=open" class="btn-add-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user-plus"></i> Add User
                </a>
            </div>

            <!-- Users List Table -->
            <div class="table-card">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th>User Name</th>
                            <th>Email Address</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th style="width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) {
                            while ($user = mysqli_fetch_assoc($result)) { 
                                $profile_image = !empty($user['profile_image']) && file_exists("../uploads/profile_image/" . $user['profile_image'])
                                    ? "../uploads/profile_image/" . $user['profile_image'] 
                                    : "";
                            ?>
                                <tr>
                                    <td>#<?php echo ($user["id"]); ?></td>
                                    <td>
                                        <div class="user-meta">
                                            <?php if ($profile_image): ?>
                                                <img src="<?php echo ($profile_image); ?>" alt="Profile" class="user-avatar">
                                            <?php else: ?>
                                                <div class="user-avatar"><i class="fa-solid fa-user"></i></div>
                                            <?php endif; ?>
                                            <strong><?php echo ($user["full_name"]); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo ($user["email"]); ?></td>
                                    <td><?php echo ($user["Phone"] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                            $current_role = trim($user["role"] ?? '');
                                            if (empty($current_role)) {
                                                $current_role = 'CUSTOMER'; 
                                            }

                                            $role_lower = strtolower($current_role);
                                            if ($role_lower === 'admin') {
                                                $badge_class = 'role-admin';
                                            } elseif ($role_lower === 'customer') {
                                                $badge_class = 'role-customer';
                                            } else {
                                                $badge_class = 'role-artist'; 
                                            }
                                        ?>
                                        <span class="role-badge <?php echo $badge_class; ?>">
                                            <?php echo strtoupper(($current_role)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-cell">
                                            <a href="../admin/view_user.php?id=<?php echo $user['id']; ?>" class="btn-action btn-view">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                            <a href="../admin/edit_user.php?id=<?php echo $user["id"]; ?>" class="btn-action btn-edit">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="../admin/manage_users.php?delete=<?php echo $user["id"]; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">
                                                <i class="fa-solid fa-trash-can"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #a0a0ab; padding: 30px;">
                                    No users found in database.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</div>

<!-- Add User Modal Popup Window (Controlled entirely via PHP parameters) -->
<div class="modal-overlay" id="addUserModal" style="display: <?php echo $is_modal_open ? 'flex' : 'none'; ?>;">
    <div class="modal-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus" style="color: #8a2be2;"></i> Add New User</h3>
            <!-- Close button redirects back without modal parameter -->
            <a href="manage_users.php" class="btn-close-modal" style="text-decoration: none; color: inherit;">
                <i class="fa-solid fa-xmark"></i>
            </a>
        </div>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile_image">Profile Picture</label>
                <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="john@gmail.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="09457132914" required>
            </div>

            <div class="form-group">
                <label for="role">User Role</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                    <option value="artist">Artist</option>
                </select>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="password" style="margin-bottom: 0;">Password</label>
                    <!-- Pure PHP Show/Hide Password Toggle Link -->
                    <a href="../admin/manage_users.php?modal=open&show_pass=<?php echo $next_pass_state; ?>" style="font-size: 0.8rem; color: #8a2be2; text-decoration: none;">
                        <?php echo $pass_toggle_label; ?>
                    </a>
                </div>
                <input type="<?php echo $pass_input_type; ?>" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="add_user" class="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Save User
            </button>
        </form>
    </div>
</div>

</body>
</html>