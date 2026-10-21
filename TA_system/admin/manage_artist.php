<?php
// Anti-cache headers aron dili ma-stock sa browser history ang pahina human mag-logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "../config/db.php";

// Check authorization
if (!isset($_SESSION['user_id']) || !in_array(strtoupper($_SESSION['role']), ['ADMIN', 'ARTIST'])) {
    header("Location: ../auth/login.php");
    exit();
}

$success_msg = "";
$error_msg = "";
$edit_mode = false;
$edit_data = null;
$show_form = isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit');

// 1. CREATE / ADD NEW ARTIST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_artist'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = 'ARTIST';

    $check_email = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check_email, "s", $email);
    mysqli_stmt_execute($check_email);
    mysqli_stmt_store_result($check_email);

    if (mysqli_stmt_num_rows($check_email) > 0) {
        $error_msg = "Email already exists in the system!";
        $show_form = true;
    } else {
        $insert_query = "INSERT INTO users (full_name, email, password, role, Phone) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "sssss", $full_name, $email, $password, $role, $phone);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../admin/manage_artist.php?msg=added");
            exit();
        } else {
            $error_msg = "Failed to add artist: " . mysqli_error($conn);
            $show_form = true;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($check_email);
}

// 2. LOAD EDIT DATA IF REQUESTED
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_query = mysqli_query($conn, "SELECT id, full_name, email, Phone, role FROM users WHERE id = $edit_id AND UPPER(role) = 'ARTIST'");
    if ($edit_query && mysqli_num_rows($edit_query) > 0) {
        $edit_data = mysqli_fetch_assoc($edit_query);
        $edit_mode = true;
        $show_form = true;
    }
}

// 3. UPDATE ARTIST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_artist'])) {
    $id = intval($_POST['artist_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);

    $update_query = "UPDATE users SET full_name = ?, email = ?, Phone = ?, role = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $phone, $role, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../admin/manage_artist.php?msg=updated");
        exit();
    } else {
        $error_msg = "Failed to update artist: " . mysqli_error($conn);
        $show_form = true;
        $edit_mode = true;
    }
    mysqli_stmt_close($stmt);
}

// 4. DELETE ARTIST
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_query = "DELETE FROM users WHERE id = $id AND UPPER(role) = 'ARTIST'";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: ../admin/manage_artist.php?msg=deleted");
        exit();
    } else {
        $error_msg = "Failed to delete artist: " . mysqli_error($conn);
    }
}

// Success feedback messages via GET
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'added') $success_msg = "New artist added successfully!";
    if ($_GET['msg'] == 'updated') $success_msg = "Artist updated successfully!";
    if ($_GET['msg'] == 'deleted') $success_msg = "Artist deleted successfully!";
}

// FETCH ALL ARTISTS
$query = "SELECT id, full_name, email, Phone, profile_image, role 
          FROM users 
          WHERE UPPER(role) = 'ARTIST'
          ORDER BY full_name ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artists - HLR INK STUDIO</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/admin_style.css">
</head>
<body>

<!-- SIDEBAR NAVIGATION -->
<div class="sidebar">
    <div>
        <div class="sidebar-brand">PATIK <span>STUDIO</span></div>
        <ul class="sidebar-menu">
            <li><a href="../admin/admin_dashboard.php">Dashboard</a></li>
            <li><a href="../users/appointment.php">Appointments</a></li>
            <li><a href="../admin/manage_services.php">Services</a></li>
            <li><a href="../admin/manage_artist.php" class="active">Artists</a></li>
            <li><a href="../admin/manage_users.php">Manage Users</a></li>
        </ul>
    </div>
     <div>
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Are you sure you want to log out? You will need to sign in again.');">
                <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
            </a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="header-flex">
        <h2>
            <?php if ($show_form): ?>
                <?php echo $edit_mode ? 'Edit Artist Details' : 'Add New Artist'; ?>
            <?php else: ?>
                Manage Artists
            <?php endif; ?>
        </h2>

        <?php if (!$show_form): ?>
            <a href="../admin/manage_artist.php?action=add" class="btn">+ Add Artist</a>
        <?php else: ?>
            <a href="../admin/manage_artist.php" class="btn btn-secondary">← Back to List</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- CONDITIONAL VIEW: FORM ONLY OR TABLE LIST -->
    <?php if ($show_form): ?>
        <!-- FORM VIEW -->
        <div class="card">
            <form method="POST">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="artist_id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo $edit_mode ? ($edit_data['full_name']) : ''; ?>" placeholder="Enter artist name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo $edit_mode ? ($edit_data['email']) : ''; ?>" placeholder="Enter email address" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo $edit_mode ? ($edit_data['Phone']) : ''; ?>" placeholder="Enter phone number">
                    </div>
                    
                    <?php if ($edit_mode): ?>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role">
                                <option value="ARTIST" selected>ARTIST</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="CUSTOMER">CUSTOMER</option>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <label>Temporary Password</label>
                            <input type="password" name="password" placeholder="Enter temporary password" required>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 15px;">
                    <?php if ($edit_mode): ?>
                        <button type="submit" name="update_artist" class="btn">Save Changes</button>
                    <?php else: ?>
                        <button type="submit" name="save_artist" class="btn">Save Artist</button>
                    <?php endif; ?>
                    <a href="../admin/manage_artist.php" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- TABLE VIEW -->
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Email & Contact</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><strong><?php echo ($row['full_name']); ?></strong></td>
                                <td>
                                    <?php echo ($row['email']); ?><br>
                                    <small style="color: #888;"><?php echo ($row['Phone'] ?? 'No phone'); ?></small>
                                </td>
                                <td><span class="badge"><?php echo ($row['role']); ?></span></td>
                                <td class="action-cell">
                                    <a href="../artist/view_artist.php?id=<?php echo $row['id']; ?>" class="btn-action">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                    <a href="../admin/manage_artist.php?edit=<?php echo $row['id']; ?>" class="btn-action">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="../admin/manage_artist.php?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this artist?');">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #888; padding: 30px;">No artists found. Click "+ Add Artist" to register one.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>