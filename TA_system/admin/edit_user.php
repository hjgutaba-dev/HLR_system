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

$error_msg = "";

/* =========================
   UPDATE USER PROCESS
========================= */
if (isset($_POST["update_user"])) {
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);
    $role      = trim($_POST["role"]);
    $password  = trim($_POST["password"]);

    $profile_image = $user['profile_image'] ?? ""; // Default to current photo

    // Profile image update handling
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
                // Delete old profile picture if exists
                if (!empty($user['profile_image']) && file_exists("../uploads/profile_image/" . $user['profile_image'])) {
                    unlink("../uploads/profile_image/" . $user['profile_image']);
                }
                $profile_image = $new_name;
            }
        }
    }

    // Check if new password was provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, role = ?, password = ?, profile_image = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssi", $full_name, $email, $phone, $role, $hashed_password, $profile_image, $id);
    } else {
        $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, role = ?, profile_image = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $full_name, $email, $phone, $role, $profile_image, $id);
    }

    if ($stmt && mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../admin/manage_users.php?msg=updated");
        exit();
    } else {
        $error_msg = "Failed to update user details. Please try again.";
    }
}

// Current profile photo path resolution
$profile_image = !empty($user['profile_image']) && file_exists("../uploads/profile_image/" . $user['profile_image'])
    ? "../uploads/profile_image/" . $user['profile_image'] 
    : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - HLR INK STUDIO</title>
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
                <h1><i class="fa-solid fa-user-pen" style="color: #8a2be2;"></i> Edit User Details</h1>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?></div>
            <?php endif; ?>

            <div class="edit-card">
                <form method="POST" action="" enctype="multipart/form-data">

                    <!-- Profile Image Preview & Change -->
                    <div class="avatar-preview-container">
                        <?php if ($profile_image): ?>
                            <img src="<?php echo ($profile_image); ?>" alt="Profile Picture" class="avatar-lg">
                        <?php else: ?>
                            <div class="avatar-lg"><i class="fa-solid fa-user"></i></div>
                        <?php endif; ?>

                        <div style="flex: 1;">
                            <label for="profile_image" style="display: block; font-size: 0.82rem; font-weight: 600; color: #a0a0ab; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px;">
                                Change Profile Image
                            </label>
                            <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
                            <p class="field-hint">Leave blank to keep current photo.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo ($user['full_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo ($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" value="<?php echo ($user['phone'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="role">User Role</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="customer" <?php echo strtolower($user['role']) === 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="admin" <?php echo strtolower($user['role']) === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="artist" <?php echo strtolower($user['role']) === 'artist' ? 'selected' : ''; ?>>Artist</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
                        <p class="field-hint">Leave blank if you don't want to change the password.</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_user" class="btn-submit">
                            <i class="fa-solid fa-floppy-disk"></i> Save Changes
                        </button>
                        <a href="../admin/manage_users.php" class="btn-cancel">Cancel</a>
                    </div>

                </form>
            </div>

        </div>
    </main>

</div>

</body>
</html>