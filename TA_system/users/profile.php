<?php
// Anti-cache headers aron dili ma-stock sa browser history ang pahina human mag-logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = intval($_SESSION["user_id"]);

/* DELETE ACCOUNT LOGIC */
if (isset($_POST["delete_account"])) {
    $stmt1 = mysqli_prepare($conn, "DELETE FROM appointments WHERE customer_id = ?");
    mysqli_stmt_bind_param($stmt1, "i", $user_id);
    mysqli_stmt_execute($stmt1);

    $stmt2 = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt2, "i", $user_id);
    mysqli_stmt_execute($stmt2);

    session_destroy();
    header("Location: ../index.php");
    exit();
}

/* FETCH CURRENT USER INFO */
$stmt = mysqli_prepare($conn, "SELECT full_name, email, phone, profile_image FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$avatar_path = !empty($user['profile_image']) && file_exists("../uploads/profile_image/" . $user['profile_image'])
    ? "../uploads/profile_image/" . $user['profile_image']
    : "https://via.placeholder.com/130/121212/ffffff?text=" . urlencode(substr($user['full_name'] ?? 'User', 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - HLR INK STUDIO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href= "../users/users_style.css">
</head>
<body>

<aside class="sidebar">
    <div>
        <div class="brand">HLR INK <span>STUDIO</span></div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="../users/customer.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li class="nav-item"><a href="../users/my_appointment.php"><i class="fa-solid fa-calendar-check"></i> My Appointments</a></li>
            <li class="nav-item"><a href="../users/profile.php" class="active"><i class="fa-regular fa-user"></i> My Profile</a></li>
        </ul>
    </div>
        <div>
            <a href="../auth/logout.php" class="logout-link" onclick="return confirm('Are you sure you want to log out? You will need to sign in again.');">
                <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
            </a>
        </div>
</aside>

<main class="main-content">
    <div class="container">
        <div class="profile-card">
            
            <div class="card-header">
                <h1 class="page-title"><i class="fa-regular fa-user"></i> My Profile</h1>
                <div style="display: flex; gap: 10px;">
                    <a href="../users/edit_profile.php" class="btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                    <form method="POST" onsubmit="return confirm('Delete account permanently?');" style="display:inline;">
                        <button type="submit" name="delete_account" class="btn-delete"><i class="fa-solid fa-trash-can"></i> Delete</button>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated') { ?>
                <div class="alert-success"><i class="fa-solid fa-circle-check"></i> Profile updated successfully!</div>
            <?php } ?>

            <div class="avatar-section">
                <div class="avatar-wrapper">
                    <img src="<?php echo ($avatar_path); ?>" alt="Profile Picture">
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">Full Name</div>
                <div class="info-value"><?php echo ($user['full_name']); ?></div>
            </div>

            <div class="info-group">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?php echo ($user['email']); ?></div>
            </div>

            <div class="info-group">
                <div class="info-label">Phone Number</div>
                <div class="info-value"><?php echo !empty($user['phone']) ? ($user['phone']) : '<span style="color:#71717a;">Not provided</span>'; ?></div>
            </div>

        </div>
    </div>
</main>

</body>
</html>