<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "customer") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = intval($_SESSION["user_id"]);
$error_msg = "";

/* UPDATE PROFILE LOGIC */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);

    // Handle Profile Picture Upload
    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES["profile_image"]["tmp_name"];
        $fileName      = $_FILES["profile_image"]["name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = "user_" . $user_id . "_" . time() . "." . $fileExtension;
            $uploadDir   = "../uploads/profile_image/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $stmtPic = mysqli_prepare($conn, "UPDATE users SET profile_image = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmtPic, "si", $newFileName, $user_id);
                mysqli_stmt_execute($stmtPic);
                mysqli_stmt_close($stmtPic);
            }
        } else {
            $error_msg = "Invalid file type. Only JPG, PNG, and WEBP allowed.";
        }
    }

    if (empty($error_msg)) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $full_name, $email, $phone, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $_SESSION["full_name"] = $full_name;
        header("Location: ../users/profile.php?msg=updated");
        exit();
    }
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
    <title>Edit Profile - HLR INK STUDIO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href= "../users/users_style.css">
</head>
<body>

<aside class="sidebar">
    <div>
        <div class="brand">HLR INK <span>STUDIO</span></div>
        <ul class="nav-menu">
            <li class="nav-item"><a href="../users/customer.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li class="nav-item"><a href="../users/appointment.php"><i class="fa-regular fa-calendar-plus"></i> Book Appointment</a></li>
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
                <h1 class="page-title"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</h1>
                <a href="profile.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo ($error_msg); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="avatar-section">
                    <div class="avatar-wrapper">
                        <img src="<?php echo ($avatar_path); ?>" alt="Profile Picture">
                    </div>
                    <div style="margin-top: 10px;">
                        <input type="file" name="profile_image" accept="image/*" style="color: #a1a1aa; font-size: 0.85rem;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo ($user['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo ($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?php echo ($user['phone'] ?? ''); ?>">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </form>

        </div>
    </div>
</main>

</body>
</html>